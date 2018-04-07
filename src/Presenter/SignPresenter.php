<?php

namespace App\Presenter;

final class SignPresenter extends \Nepttune\Presenter\BasePresenter
{
    /** @persistent */
    public $backlink;

    /**
     * @inject
     * @var \Nepttune\Component\ILoginFormFactory
     */
    public $iLoginFormFactory;

    public function actionOut()
    {
        $this->getUser()->logout();

        $this->flashMessage($this->translator->translate('admin.flash.sign_out'), 'success');
        $this->redirect(':Sign:in');
    }

    protected function createComponentLoginForm()
    {
        return $this->iLoginFormFactory->create();
    }
}
