<?php

declare(strict_types = 1);

namespace Nepttune\Model;

interface IAuthorizator
{
    public function isAllowed(string $resource, string $privilege = null, array $params = []) : bool;

    public function isRoot() : bool;

    public function getUserId() : ?int;

    public function getRoleId() : ?int;
}
