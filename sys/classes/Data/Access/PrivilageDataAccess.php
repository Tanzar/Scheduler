<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Data\Access\DataAccess as DataAccess;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Exceptions\PrivilageException as PrivilageException;

/**
 * Description of PrivilageDataAccess
 *
 * @author Tanzar
 */
class PrivilageDataAccess extends DataAccess{
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    public function getPrivilagesByUserID(int $idUser) : Container {
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('id_user', $idUser);
        $data = $this->select($sql);
        return $data;
    }
    
    public function getPrivilageByID(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('id', $id);
        $data = $this->select($sql);
        if($data->getLength() === 0){
            throw new PrivilageException('privilage not found.');
        }
        if($data->getLength() > 1){
            throw new PrivilageException('privilage id column values are not unique.');
        }
        return new Container($data->getValue(0));
    }
    
    public function add(Container $privilage) : int{
        $idUser = $privilage->getValue('id_user');
        $name = $privilage->getValue('privilage');
        $old = $this->getUserPrivilage($idUser, $name);
        if($old === false){
            return $this->insertNew($privilage);
        }
        else{
            $id = $old->getValue('id');
            $this->activate($id);
            return $id;
        }
    }
    
    private function getUserPrivilage(int $idUser, string $privilage) : Container{
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('id_user', $idUser)->and()
                ->where('privilage', $privilage);
        $data = $this->select($sql);
        if($data->getLength() === 0){
            return false;
        }
        else{
            return new Container($data->getValue(0));
        }
    }
    
    private function insertNew(Container $privilage) : int{
        $sql = new MysqlBuilder();
        $sql->insert('privilages')
                ->into('privilage', $privilage->getValue('privilage'))
                ->into('id_user', $privilage->getValue('id_user'));
        return $this->insert($sql);
        
    }
    
    public function activate(int $id){
        $sql = new MysqlBuilder();
        $sql->update('privilages', 'id', $id)
                ->set('active', 1);
        $this->update($sql);
    }
    
    public function deactivate(int $id){
        $sql = new MysqlBuilder();
        $sql->update('privilages', 'id', $id)
                ->set('active', 0);
        $this->update($sql);
    }
    
    public function countAdmins() : int {
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('privilage', 'admin');
        $data = $this->select($sql);
        return $data->getLength();
    }
}
