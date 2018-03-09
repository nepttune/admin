<?php

namespace Nepttune\Presenter;

abstract class BaseAuthPresenter extends BasePresenter
{
    protected function startup()
    {
        if (!$this->user->isLoggedIn())
        {
            $this->redirect($this->context->parameters['destSignIn'], ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    public static function getDefaultLayout() : string
    {
        return static::getAdminLayout();
    }

    public static function getAdminLayout() : string
    {
        return __DIR__ . '/../templates/@admin.latte';
    }
    
    public function useNotifications() : bool
    {
        return $this->context->hasService('notifications');
    }

    public function useUserDetail() : bool
    {
        return $this->context->hasService('userDetail');
    }

    public function useSidebar() : bool
    {
        return $this->context->hasService('sidebar');
    }

    public function useSearch() : bool
    {
        return $this->context->hasService('search');
    }
}
