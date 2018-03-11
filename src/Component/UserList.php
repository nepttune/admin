<?php

namespace Nepttune\Component;

use \Ublaboo\DataGrid\DataGrid;

final class UserList extends BaseListComponent
{
    public function __construct(\Nepttune\Model\UserModel $userModel)
    {
        $this->repository = $userModel;
    }

    protected function modifyList(DataGrid $grid) : DataGrid
    {
        $grid->addColumnText('username', 'admin.username')
            ->setSortable();

        $grid->addToolbarButton(':add', 'global.add')
            ->setIcon('plus')
            ->setClass('btn btn-primary');

        return $grid;
    }
}
