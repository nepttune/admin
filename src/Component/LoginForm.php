<?php

namespace Nepttune\Component;

use \Nette\Application\UI\Form;

final class LoginForm extends BaseFormComponent
{
    const SAVE_NEXT = false;
    const SAVE_LIST = false;

    /** @var  string */
    protected $redirectSignIn;

    /** @var  \Nepttune\Model\LoginLogModel */
    protected $loginLogModel;

    /** @var \Nette\Http\Request */
    protected $request;

    public function __construct(
        string $redirectSignIn,
        \Nepttune\Model\LoginLogModel $loginLogModel,
        \Nette\Http\Request $request)
    {
        $this->redirectSignIn = $redirectSignIn;
        $this->loginLogModel = $loginLogModel;
        $this->request = $request;
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('username', 'Username')->setRequired();
        $form->addPassword('password', 'Password')->setRequired();

        $ids = $this->loginLogModel->getTable()
            ->where('ip_address', inet_pton($this->request->getRemoteAddress()))
            ->order('id DESC')
            ->limit(5)
            ->fetchPairs(null, 'id');

        if ($this->loginLogModel->getTable()->where('id', $ids)->where('result', 'failure')->count() === 5)
        {
            $form->addReCaptcha('recaptcha', 'Security check', 'Please prove you are not a robot.');
        }

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        $failure = false;

        try
        {
            $this->getPresenter()->getUser()->login($values->username, $values->password);
            $this->getPresenter()->getUser()->setExpiration(0, TRUE);
        }
        catch (\Nette\Security\AuthenticationException $e)
        {
            $failure = $e->getMessage();
        }

        $this->loginLogModel->insert([
            'datetime' => new \Nette\Utils\DateTime(),
            'result' => (bool) $failure ? 'failure' : 'success',
            'ip_address' => inet_pton($this->request->getRemoteAddress()),
            'username' => $values->username
        ]);

        if ($failure)
        {
            $this->getPresenter()->flashMessage($failure, 'danger');
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->flashMessage('Successfully logged in.', 'success');
        $this->getPresenter()->restoreRequest($this->getPresenter()->getParameter('backlink'));
        $this->getPresenter()->redirect($this->redirectSignIn);
    }
}
