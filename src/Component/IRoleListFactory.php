<?php

namespace Nepttune\Component;

interface IRoleListFactory
{
    /** @return RoleList */
    public function create() : RoleList;
}
