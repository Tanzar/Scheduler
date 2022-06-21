<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of LocationDataAccess
 *
 * @author Tanzar
 */
class LocationDAO extends DAO{
    
    public function __construct() {
        parent::__construct(false);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'location';
    }

    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('location')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getByID(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('location')->where('id', $id);
        $data = $this->select($sql);
        if($data->length() > 1 ){
            $this->throwIdColumnException('location');
        }
        if($data->length() === 0){
            $this->throwNotFoundException('location');
        }
        $result = new Container($data->get(0));
        return $result;
    }
    
    public function getByGroupId(int $idGroup) : Container {
        $sql = new MysqlBuilder();
        $sql->select('location')
                ->where('active', 1)->and()
                ->where('id_location_group', $idGroup);
        return $this->select($sql);
    }
    
    public function getByTypeId(int $idType) : Container {
        $sql = new MysqlBuilder();
        $sql->select('location')
                ->where('active', 1)->and()
                ->where('id_location_type', $idType);
        return $this->select($sql);
    }
    
    public function enable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('location', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('location', 'id', $id)->set('active', 0);
        $this->update($sql);
    }

}
