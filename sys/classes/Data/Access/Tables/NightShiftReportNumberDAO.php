<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of NightShiftReportNumberDAO
 *
 * @author Tanzar
 */
class NightShiftReportNumberDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'night_shift_report_number';
    }

    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('night_shift_report_number')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getByYear(int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('night_shift_report_number')->where('year', $year);
        $data = $this->select($sql);
        if($data->length() > 1){
            $this->throwDataAccessException('column year is not unique for table night_shift_report_number');
        }
        if($data->length() === 0){
            return new Container();
        }
        else{
            return new Container($data->get(0));
        }
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('night_shift_report_number', 'id', $id)
                ->set('active', 1);
        $this->update($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('night_shift_report_number', 'id', $id)
                ->set('active', 0);
        $this->update($sql);
    }
    
}
