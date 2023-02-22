<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of DocumentDAO
 *
 * @author Tanzar
 */
class DocumentDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'document';
    }
    
    public function getActiveByMonthAndYear(int $month, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document')->where('active', 1)
                ->and()->openBracket()->openBracket()
                ->where('month(start)', $month)->and()
                ->where('year(start)', $year)->closeBracket()
                ->or()->openBracket()
                ->where('month(end)', $month)->and()->where('year(end)', $year)
                ->closeBracket()->closeBracket()
                ->orderBy('start', false);
        return $this->select($sql);
    }
    
    public function getAllByMonthAndYear(int $month, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document')->openBracket()->openBracket()
                ->where('month(start)', $month)->and()
                ->where('year(start)', $year)->closeBracket()
                ->or()->openBracket()
                ->where('month(end)', $month)->and()->where('year(end)', $year)
                ->closeBracket()->closeBracket()
                ->orderBy('start', false);
        return $this->select($sql);
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('document', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('document', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}
