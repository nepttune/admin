<?php

namespace App\AdminModule\Presenter;

final class SignPresenter extends \Nepttune\Presenter\BasePresenter
{
    /** @persistent */
    public $backlink;

    /**
     * @inject
     * @var \Nepttune\Component\ILoginFormFactory
     */
    public $iLoginFormFactory;

    /** @var  string */
    protected $redirectSignOut;

    public function __construct(string $redirectSignOut)
    {
        $this->redirectSignOut = $redirectSignOut;
    }

    public function actionOut()
    {
        $this->getUser()->logout();

        $this->flashMessage('Successfully logged out.', 'success');
        $this->redirect($this->redirectSignOut);
    }

    protected function createComponentLoginForm()
    {
        return $this->iLoginFormFactory->create();
    }
}
