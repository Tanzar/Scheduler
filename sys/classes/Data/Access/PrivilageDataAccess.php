<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Data\Access\DataAccess as DataAccess;
use Data\Containers\Privilages as Privilages;
use Data\Entities\Privilage as Privilage;
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
    
    public function getPrivilagesByUserID(int $idUser) : Privilages {
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('id_user', $idUser);
        $data = $this->select($sql);
        return $this->parsePrivilages($data);
    }
    
    public function getPrivilageByID(int $id) : Privilage {
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('id', $id);
        $data = $this->select($sql);
        if($data->getLength() === 0){
            throw new PrivilageException('privilage not found.');
        }
        if($data->getLength() > 1){
            throw new PrivilageException('privilage id column values are not unique.');
        }
        $item = $data->getValue(0);
        return new Privilage($item);
    }
    
    public function add(Privilage $privilage) : int{
        $idUser = $privilage->getIdUser();
        $name = $privilage->getPrivilage();
        $old = $this->getUserPrivilage($idUser, $name);
        if($old === false){
            return $this->insertNew($privilage);
        }
        else{
            $id = $old->getId();
            $this->activate($id);
            return $id;
        }
    }
    
    private function getUserPrivilage(int $idUser, string $privilage){
        $sql = new MysqlBuilder();
        $sql->select('privilages')->where('id_user', $idUser)->and()
                ->where('privilage', $privilage);
        $data = $this->select($sql);
        if($data->getLength() === 0){
            return false;
        }
        else{
            $item = $data->getValue(0);
            return new Privilage($item);
        }
    }
    
    private function insertNew(Privilage $privilage) : int{
        $sql = new MysqlBuilder();
        $sql->insert('privilages')
                ->into('privilage', $privilage->getPrivilage())
                ->into('id_user', $privilage->getIdUser());
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
    
    private function parsePrivilages(Container $data) : Privilages{
        $privilages = new Privilages();
        foreach($data->toArray() as $item){
            $privilage = new Privilage($item);
            $privilages->add($privilage);
        }
        return $privilages;
    }

}
