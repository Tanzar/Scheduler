<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Services\Exceptions\PrivilageException as PrivilageException;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Data\Access\PrivilageDataAccess as PrivilageDataAccess;
use Data\Containers\Privilages as Privilages;
use Data\Entities\Privilage as Privilage;

/**
 * Description of PrivilagesService
 *
 * @author Tanzar
 */
class PrivilagesService{
    private PrivilageDataAccess $privilageDataAccess;
    
    public function __construct() {
        $this->privilageDataAccess = new PrivilageDataAccess();
    }

    public function getConfigPrivilages() : Container{
        $appconfig = AppConfig::getInstance();
        $config = $appconfig->getSecurity();
        $privilages = $config->getValue('privilages');
        return new Container($privilages);
    }
    
    public function getUserPrivilages(int $id) : Container{
        $privilages = $this->privilageDataAccess->getPrivilagesByUserID($id);
        return $this->parseData($privilages);
    }
    
    public function getPrivilageByID(int $id) : Container {
        $privilage = $this->privilageDataAccess->getPrivilageByID($id);
        return new Container($privilage);
    }
    
    public function addPrivilage(Container $data) : int {
        $arr = $data->toArray();
        $privilage = new Privilage($arr);
        return $this->privilageDataAccess->add($privilage);
    }
    
    public function changeStatus(int $id){
        $privilage = $this->privilageDataAccess->getPrivilageByID($id);
        $active = $privilage->getActive();
        if($active){
            $this->deactivate($privilage);
        }
        else{
            $this->privilageDataAccess->activate($id);
        }
    }
    
    private function deactivate(Privilage $privilage){
        $id = $privilage->getId();
        if($privilage->is('admin')){
            if($this->isLastAdmin()){
                throw new PrivilageException('Cannot disable last admin.');
            }
            else{
                $this->privilageDataAccess->deactivate($id);
            }
        }
        else{
            $this->privilageDataAccess->deactivate($id);
        }
    }
    
    private function isLastAdmin(){
        $count = $this->privilageDataAccess->countAdmins();
        if($count <= 1){
            return true;
        }
        else{
            return false;
        }
    }
    
    private function parseData(Privilages $data) : Container{
        $container = new Container();
        foreach($data->toArray() as $item){
            $container->add($this->parsePrivilage($item));
        }
        return $container;
    }
    
    private function parsePrivilage(Privilage $item) : array {
        return $item->toArray();
    }
}
