<?php

namespace App\AdminModule\Presenter;

final class SignPresenter extends \Nepttune\Presenter\BasePresenter
{
    /** @persistent */
    public $backlink;

    public function actionOut()
    {
        $this->user->logout();

        $this->flashMessage('Successfully logged out.', 'success');
        $this->redirect($this->context->parameters['redirectSignOut']);
    }
}
