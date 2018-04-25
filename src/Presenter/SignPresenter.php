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

    public function actionOut()
    {
        $this->getUser()->logout();

        $this->flashMessage($this->translator->translate('admin.flash.sign_out'), 'success');
        $this->redirect(':Sign:in');
    }

    protected function createComponentLoginForm()
    {
        $control = $this->iLoginFormFactory->create();
        $control->setRedirect($this->dest['adminHomepage']);
        return $control;
    }
}
