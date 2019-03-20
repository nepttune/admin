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

namespace Nepttune\Presenter;

abstract class BaseAuthPresenter extends BasePresenter implements \Nepttune\TI\IRestricted
{
    use \Nepttune\TI\TRestricted;

    /** @var  \Nepttune\Component\IConfigMenuFactory */
    protected $iConfigMenuFactory;

    /** @var  \Nepttune\Component\IBreadcrumbFactory */
    protected $iBreadcrumbFactory;

    /** @var  array */
    protected $admin;

    public function injectAdminParameters(
        array $admin,
        \Nepttune\Component\IConfigMenuFactory $IConfigMenuFactory,
        \Nepttune\Component\IBreadcrumbFactory $IBreadcrumbFactory)
    {
        $this->admin = $admin;
        $this->iConfigMenuFactory = $IConfigMenuFactory;
        $this->iBreadcrumbFactory = $IBreadcrumbFactory;
    }

    public function checkRequirements($element)
    {
        if (!$this->getUser()->isLoggedIn())
        {
            $this->redirect($this->dest['signIn'], ['backlink' => $this->storeRequest()]);
        }

        if (!$this->authorizator->isAllowed($this->getAction(true)))
        {
            throw new \Nette\Application\BadRequestException('Forbidden', 403);
        }
    }

    protected function beforeRender() : void
    {
        parent::beforeRender();

        $this->template->admin = $this->admin;
        $this->template->collapsedMenu = $this->session->getSection('nepttune')->collapsedMenu ?? false;
    }
    
    public function handleMenuState() : void
    {
        $this->session->getSection('nepttune')->collapsedMenu = (bool) $this->getParameter('state');

        $this->terminate();
    }

    public static function getDefaultLayout() : string
    {
        return static::getAdminLayout();
    }

    public static function getAdminLayout() : string
    {
        return __DIR__ . '/../templates/@admin.latte';
    }

    protected function createComponentMenu()
    {
        return $this->iConfigMenuFactory->create();
    }

    protected function createComponentBreadcrumb()
    {
        return $this->iBreadcrumbFactory->create();
    }
}
