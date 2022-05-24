<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Security;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Database\Database as Database;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Tanweb\Security\SecurityException as SecurityException;
use Tanweb\Security\Encrypter as Encrypter;
use Tanweb\Logger\Logger as Logger;

/**
 * Class responsible for managing access to application.
 * Object allows to check if user have privilages for accessing applicaiton requests and pages
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Security {
    private bool $isEnabled;
    private string $dbIndex;
    private string $usersTable;
    private string $usersIndex;
    private string $usernameColumn;
    private string $passwordColumn;
    
    private bool $usePassword;
    
    private string $privilageTable;
    private string $privilageUserIndex;
    private string $privilageColumn;
    private Container $privilagesNames;
    
    public function __construct() {
        $appconfig = AppConfig::getInstance();
        $config = $appconfig->getSecurity();
        $this->isEnabled = $config->getValue('enable');
        $this->usePassword = $config->getValue('usePasswords');
        $this->initializeDatabase($config);
    }
    
    private function initializeDatabase(Container $config){
        $this->dbIndex = $config->getValue('database_index');
        $this->usersTable = $config->getValue('users_table');
        $this->usersIndex = $config->getValue('index_column');
        $this->usernameColumn = $config->getValue('username_column');
        $this->passwordColumn = $config->getValue('password_column');
        $this->privilageTable = $config->getValue('privilige_table');
        $this->privilageUserIndex = $config->getValue('privilage_user_index');
        $this->privilageColumn = $config->getValue('privilage_column');
        $privilages = $config->getValue('privilages');
        $this->privilagesNames = new Container($privilages);
    }
    
    /*
     * Checking if user have at least one privilage required to access
     * 
     * @param Container $privilages - container of strings, contains required privilages to pass checks, if empty allows everyone
     * @return bool - true if successful, false if user don't have these privilages
     */
    public function userHaveAnyPrivilage(Container $privilages) : bool{
        if($this->isEnabled){
            if($privilages->isEmpty()){
                return true;
            }
            $userPrivilages = $this->getUserPrivilages();
            foreach ($privilages->toArray() as $privilage){
                if($userPrivilages->contains($privilage)){
                    return true;
                }
            }
            return false;
        }
        else{
            return true;
        }
    }
    
    /**
     * Checking if user have certain privilage
     * 
     * @param string $privilage - privilage required to pass check 
     * @return bool - true if successful,  if not throws SecurityException
     */
    public function userHavePrivilage(string $privilage) : bool{
        if($this->isEnabled){
            $privilages = $this->getUserPrivilages();
            if($privilages->contains($privilage)){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
    }
    
    /**
     * Check if user have privilages to access if not throws SecurityException
     * 
     * @param Container $privilages - required privilages to pass this check
     * @return void
     */
    public function checkPrivilages(Container $privilages) : void {
        if(!$this->userHaveAnyPrivilage($privilages)){
            $logger = Logger::getInstance();
            $logger->logSecurity("Access Denied: user don't have required privilages.");
            $this->throwException("Access Denied: You don't have required privilages.");
        }
    }
    
    /**
     * Sets user Session if successfull
     * on failure throws SecurityException
     * 
     * @param string $username
     * @param string $password - depands if check for usePasswords in config.ini is true(throws SecurityException if missing) or not
     */
    public function login(string $username, string $password = null){
        $user = $this->getUserDetails($username);
        if($this->usePassword){
            if(isset($password)){
                $this->verifyPassword($user, $password);
            }
            else{
                $this->throwException('password verification active, variable $password is required');
            }
        }
        else{
            $this->logout();
            Session::setUser($username);
        }
    }
    
    private function verifyPassword(Container $user, string $password){
        if(isset($password)){
            $storedPassword = $user->getValue($this->passwordColumn);
            if(Encrypter::areSame($storedPassword, $password)){
                $username = $user->getValue($this->usernameColumn);
                $this->logout();
                Session::setUser($username);
            }
            else{
                $this->throwException('wrong username or password.');
            }
        }
        else{
            $this->throwException('password verification active, variable $password is required');
        }
    }
    
    public function logout(){
        Session::unsetUser();
    }
    
    /**
     * Returns all possible privilages form config.ini
     * for privilages stored in database you should use these
     * @return Container - container of privilage names (strings)
     */
    public function getPrivilageNames() : Container{
        return $this->privilagesNames;
    }
    
    public function isUsingPasswords() : bool {
        return $this->usePassword;
    }
    
    public function getUserPrivilages() : Container {
        $user = $this->getUserDetails();
        $userIndex = $user->getValue($this->usersIndex);
        $sql = new MysqlBuilder();
        $sql->select($this->privilageTable)->where($this->privilageUserIndex, $userIndex);
        $database = Database::getInstance($this->dbIndex);
        $privilages = $database->select($sql);
        $result = new Container();
        foreach ($privilages->toArray() as $privilage){
            if(isset($privilage[$this->privilageColumn])){
                $result->add($privilage[$this->privilageColumn]);
            }
            else{
                $this->throwException('privilage_column not defined in table: ' . $this->privilageTable);
            }
        }
        return $result;
    }
    
    private function getUserDetails(string $username = null) : Container{
        if(!isset($username)){
            $username = Session::getUsername();
        }
        $sql = new MysqlBuilder();
        $sql->select($this->usersTable)->where($this->usernameColumn, $username);
        $database = Database::getInstance($this->dbIndex);
        $users = $database->select($sql);
        if($users->getLength() > 1){
            $this->throwException('usernames are not unique, make sure table '
                    . ''. $this->usersTable . ' column ' . 
                    $this->usernameColumn . ' have only unique names.');
        }
        if($users->getLength() === 0){
            $this->throwException('user ' . $username . ' not found.');
        }
        $user = $users->getValue(0);
        return new Container($user);
    }
    
    private function throwException($msg){
        throw new SecurityException($msg);
    }
}
