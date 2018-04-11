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

abstract class BaseAuthPresenter extends BasePresenter
{
    /**
     * @inject
     * @var  \Nepttune\Component\IConfigMenuFactory
     */
    public $iConfigMenuFactory;

    /**
     * @inject
     * @var  \Nepttune\Component\IBreadcrumbFactory
     */
    public $iBreadcrumbFactory;

    /** @var  array */
    protected $admin;

    public function injectAdminParameters(array $admin)
    {
        $this->admin = $admin;
    }

    protected function startup()
    {
        if (!$this->user->isLoggedIn())
        {
            $this->redirect(':Sign:in', ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    protected function beforeRender()
    {
        $this->template->admin = $this->admin;

        parent::beforeRender();
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
