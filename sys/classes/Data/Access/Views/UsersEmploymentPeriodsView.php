<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of UserEmploymentPeriodsDAO
 *
 * @author Tanzar
 */
class UsersEmploymentPeriodsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'users_employment_periods';
    }
    
    public function getByUserAndDate(string $username, string $date) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('username', $username)
                ->and()->where('start', $date, '<')
                ->and()->where('end', $date, '>');
        return $this->select($sql);
    }
    
    public function getOrderedActiveByDate(string $date) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('active', 1)
                ->and()->where('start', $date, '<')
                ->and()->where('end', $date, '>')
                ->orderBy('sort_priority', false);
        return $this->select($sql);
    }
    
}
