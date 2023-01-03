<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of DaysOffUserDetailsView
 *
 * @author Tanzar
 */
class DaysOffUserDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'days_off_user_details';
    }
    
    public function getByDayOff(int $dayOffId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off_user_details')->where('id_days_off', $dayOffId);
        return $this->select($sql);
    }
    
    public function getByUsername(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off_user_details')->where('username', $username);
        return $this->select($sql);
    }
    
    public function getActiveByUsername(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off_user_details')->where('username', $username)->and()->where('days_off_active', 1);
        return $this->select($sql);
    }
    
    public function getByUsernameMonthYear(string $username, int $month, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off_user_details')->where('username', $username)
                ->and()->where('month(days_off_date)', $month)
                ->and()->where('year(days_off_date)', $year);
        return $this->select($sql);
    }
}
