<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of OvertimeReductionDetailsView
 *
 * @author Tanzar
 */
class OvertimeReductionDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'overtime_reduction_details';
    }
    
    public function getByUsername(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('overtime_reduction_details')->where('username', $username);
        return $this->select($sql);
    }
    
    public function getActiveByUsernameBeforeOrAt(string $username, DateTime $date) : Container {
        $sql = new MysqlBuilder();
        $sql->select('overtime_reduction_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('date', $date->format('Y-m-d'), '<=');
        return $this->select($sql);
    }
    
    public function getActiveByUsernameAt(string $username, DateTime $date) : Container {
        $sql = new MysqlBuilder();
        $sql->select('overtime_reduction_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('date', $date->format('Y-m-d'), '=');
        return $this->select($sql);
    }
}
