<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of SuspensionTypeObjectDAO
 *
 * @author Tanzar
 */
class SuspensionTypeObjectDAO  extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'suspension_type_object';
    }

    public function getById(int $id) : Container{
        $sql = new MysqlBuilder();
        $sql->select('suspension_type_object')->where('id', $id);
        $resultset = $this->select($sql);
        if($resultset->length() > 1){
            $this->throwIdColumnException('suspension_type_object');
        }
        if($resultset->length() === 0){
            $this->throwNotFoundException('suspension_type_object');
        }
        $result = new Container($resultset->get(0));
        return $result;
    }
    
    public function getByType(int $idType) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_type_object')->where('id_suspension_type', $idType);
        return $this->select($sql);
    }
    
    public function getByTypeAndObject(int $idType, int $idObject) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_type_object')->where('id_suspension_type', $idType)
                ->and()->where('id_suspension_object', $idObject);
        $resultset = $this->select($sql);
        if($resultset->length() > 1){
            $this->throwDataAccessException('multiple same combinations for '
                    . 'table suspension_type_object on id_suspension_type = ' . 
                    $idType . ' and id_suspension_object = ' . $idObject);
        }
        if($resultset->length() === 0){
            $result = new Container();
        }
        else{
            $result = new Container($resultset->get(0));
        }
        return $result;
    }
}
