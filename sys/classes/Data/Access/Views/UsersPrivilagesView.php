<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of UsersPrivilagesDAO
 *
 * @author Tanzar
 */
class UsersPrivilagesView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'users_privilages';
    }
    
    public function getActiveAdmins() : Container {
        $sql = new MysqlBuilder();
        $sql->select('users_privilages')->where('privilage', 'admin')
                ->and()->where('user_active', 1)
                ->and()->where('privilage_active', 1);
        return $this->select($sql);
    }
    
    public function countActiveAdmins() : int {
        $data = $this->getActiveAdmins();
        return $data->length();
    }
    
    public function isLastAdmin() : bool {
        $admins = $this->countActiveAdmins();
        if($admins <= 1){
            return true;
        }
        else{
            return false;
        }
    }
}
