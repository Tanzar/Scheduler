<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;

/**
 * Description of EmploymentDataAccess
 *
 * @author Tanzar
 */
class EmploymentDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'employment';
    }

    public function getByUserId(int $idUser) : Container {
        $sql = new MysqlBuilder();
        $sql->select('employment')->where('id_user', $idUser);
        return $this->select($sql);
    }
    
    public function getByUserAndDate(string $username, string $date) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('start', $date, '<')
                ->and()->where('end', $date, '>');
        return $this->select($sql);
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('employment', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('employment', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}
