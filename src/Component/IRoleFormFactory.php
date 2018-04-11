<?php

namespace Nepttune\Component;

interface IRoleFormFactory
{
    /** @return RoleForm */
    public function create() : RoleForm;
}
