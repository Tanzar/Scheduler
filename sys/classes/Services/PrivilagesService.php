<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Services\Exceptions\PrivilageException as PrivilageException;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Data\Access\PrivilageDataAccess as PrivilageDataAccess;
use Tanweb\Config\INI\Languages as Languages;

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
        return $this->privilageDataAccess->getPrivilagesByUserID($id);
    }
    
    public function getPrivilageByID(int $id) : Container {
        return $this->privilageDataAccess->getPrivilageByID($id);
    }
    
    public function addPrivilage(Container $data) : int {
        return $this->privilageDataAccess->add($data);
    }
    
    public function changeStatus(int $id){
        $privilage = $this->privilageDataAccess->getPrivilageByID($id);
        $active = $privilage->getValue('active');
        if($active){
            $this->deactivate($privilage);
        }
        else{
            $this->privilageDataAccess->activate($id);
        }
    }
    
    private function deactivate(Container $privilage){
        $id = $privilage->getValue('id');
        if($privilage->getValue('privilage') === 'admin'){
            if($this->isLastAdmin()){
                $languages = Languages::getInstance();
                throw new PrivilageException($languages->get('last_admin'));
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
    
}
