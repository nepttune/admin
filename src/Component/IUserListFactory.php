<?php

namespace Nepttune\Component;

interface IUserListFactory
{
    /** @return UserList */
    public function create() : UserList;
}
