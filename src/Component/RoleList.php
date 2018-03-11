<?php

namespace Nepttune\Component;

use \Ublaboo\DataGrid\DataGrid;

final class RoleList extends BaseListComponent
{
    public function __construct(\Nepttune\Model\RoleModel $roleModel)
    {
        $this->repository = $roleModel;
    }

    protected function modifyList(DataGrid $grid) : DataGrid
    {
        $grid->addColumnText('name', 'global.name')
            ->setSortable();

        $grid->addToolbarButton(':add', 'global.add')
            ->setIcon('plus')
            ->setClass('btn btn-primary');

        return $grid;
    }
}
