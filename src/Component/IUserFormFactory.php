<?php

namespace Nepttune\Component;

interface IUserFormFactory
{
    /** @return UserForm */
    public function create() : UserForm;
}
