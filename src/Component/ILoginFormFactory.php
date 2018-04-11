<?php

namespace Nepttune\Component;

interface ILoginFormFactory
{
    /** @return LoginForm */
    public function create() : LoginForm;
}
