<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\Model;

class Authorizator
{
    use \Nette\SmartObject;

    /** @var  RoleAccessModel */
    protected $roleAccessModel;

    /** @var \Nette\Security\User */
    protected $user;

    /** @var \Nette\Caching\Cache */
    protected $cache;

    /** @var \Nette\Application\IPresenterFactory */
    protected $presenterFactory;

    /** @var \Nette\Http\Request */
    protected $request;

    public function __construct(
        RoleAccessModel $roleAccessModel,
        \Nette\Security\User $user,
        \Nette\Caching\IStorage $storage,
        \Nette\Application\IPresenterFactory $presenterFactory)
    {
        $this->roleAccessModel = $roleAccessModel;
        $this->user = $user;
        $this->cache = new \Nette\Caching\Cache($storage, 'Nepttune.Authorizator');
        $this->presenterFactory = $presenterFactory;
    }

    /**
     * Checks whether current user is allowed to access resource.
     * @param string $resource
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed(string $resource, string $privilege = null, array $params = []) : bool
    {
        static::validateResource($resource);

        /** Root user */
        if ($this->isRoot())
        {
            return true;
        }

        while (true)
        {
            $restricted = $this->getRestricted($resource);

            /** Resource is not restricted */
            if (!\array_key_exists($resource, $restricted))
            {
                return true;
            }

            /** Resource is root only */
            if (!empty($restricted[$resource]['root']))
            {
                return false;
            }
            
            /** Rosource is for logged users */
            if (!empty($restricted[$resource]['loggedin']))
            {
                return $this->user->isLoggedIn();
            }

            /** Resource traces other */
            if (!empty($restricted[$resource]['traces']))
            {
                $resource = \array_pop($restricted[$resource]['traces']);
                static::validateResource($resource);
                continue;
            }

            /**
             * Resource morphs into other
             * This option is not ideal and designed just for edge cases.
             */
            if (!empty($restricted[$resource]['morphs']))
            {
                $resource = $this->handleMorph(\array_pop($restricted[$resource]['morphs']), $resource, $params);

                if (\is_bool($resource))
                {
                    return $resource;
                }

                static::validateResource($resource);
                continue;
            }

            /** Database check */
            return $this->roleAccessModel->findByArray([
                'role_id' => $this->getRoleId(),
                'resource' => $resource,
                'privilege' => $privilege
            ])->count() > 0;
        }
    }

    /**
     * Checks wherther current user is root.
     */
    public function isRoot() : bool
    {
        return $this->user->isInRole('root');
    }

    /**
     * Returns user id of current user.
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user->getId();
    }

    /**
     * Returns role id of current user.
     * @return int|null
     */
    public function getRoleId() : ?int
    {
        return $this->user->getIdentity()->role_id;
    }

    /**
     * Validates format of resource string - avoids possible false positives.
     * @param string $resource
     * @throws \Nette\InvalidStateException
     */
    protected static function validateResource(string $resource) : void
    {
        if (\substr_count($resource, ':') !== 3)
        {
            throw new \Nette\InvalidStateException('Invalid destination provided. Enter FQN.');
        }
    }

    private function getRestricted(string $resource) : array
    {
        $cacheName = 'restrictedActions_' . $resource;
        $restricted = $this->cache->load($cacheName);

        if ($restricted !== null)
        {
            return $restricted;
        }

        list($presenter, $action) = static::splitResource($resource);
        $presenterClass = $this->presenterFactory->getPresenterClass($presenter);
        $restricted = $presenterClass::getRestrictedStatic();

        $this->cache->save($cacheName, $restricted);
        return $restricted;
    }
    
    private static function splitResource(string $resource) : array
    {
        $temp = \array_filter(\explode(':', $resource), '\strlen');

        if (\count($temp) === 3)
        {
            return [
                "$temp[1]:$temp[2]",
                $temp[3]
            ];
        }

        return $temp;
    }

    private function handleMorph(string $morph, string $resource = null, array $params = [])
    {
        if (\is_callable($morph))
        {
            return \call_user_func($function, $params);
        }

        list($presenterName, $action) = static::splitResource($resource);
        $presenter = $this->presenterFactory->createPresenter($presenterName);

        return \call_user_func([$presenter, $morph], $params);
    }
}
