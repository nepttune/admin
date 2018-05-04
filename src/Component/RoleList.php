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

namespace Nepttune\Component;

use \Ublaboo\DataGrid\DataGrid;

class RoleList extends BaseListComponent
{
    protected $add = ':add';
    protected $edit = ':edit';

    public function __construct(\Nepttune\Model\RoleModel $roleModel)
    {
        parent::__construct();
        
        $this->repository = $roleModel;
    }

    protected function modifyList(DataGrid $grid) : DataGrid
    {
        $grid->addColumnText('name', 'global.name')
            ->setSortable();
        $grid->addColumnText('description', 'global.description');

        return $grid;
    }
}
