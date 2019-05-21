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

abstract class SignPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink;

    /**
     * @inject
     * @var \Nepttune\Component\ILoginFormFactory
     */
    public $iLoginFormFactory;

    public function renderIn()
    {
        $this->assetsRecaptcha = true;
        $this->template->setFile(__DIR__ . '/../templates/Sign/in.latte');
    }
    
    public function actionOut()
    {
        $this->getUser()->logout();

        $this->flashMessage($this->translator->translate('admin.flash.sign_out'), 'success');
        $this->redirect($this->dest['signIn']);
    }

    protected function createComponentLoginForm()
    {
        $control = $this->iLoginFormFactory->create();
        $control->saveCallback = function () {
            $this->flashMessage('admin.flash.sign_in', 'success');
            if ($this->getParameter('backlink')) {
                $this->restoreRequest($this->getParameter('backlink'));
            }
            $this->redirect($this->dest['adminHomepage']);
        };
        $control->failureCallback = function ($form, $msg) {
            $this->flashMessage($msg, 'danger');
            $this->redirect('this');
        };
        return $control;
    }
}
