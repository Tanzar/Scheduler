<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of SuzugUserDetailsView
 *
 * @author Tanzar
 */
class SuzugUserDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'suzug_user_details';
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suzug_user_details')->where('id', $id);
        $result = $this->select($sql);
        if($result->length() > 1){
            $this->throwIdColumnException('suzug_user_details');
        }
        if($result->length() === 0){
            return new Container();
        }
        else{
            return new Container($result->get(0));
        }
    }
    
    public function getByYear(int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suzug_user_details')->where('year', $year)
                ->orderBy('number');
        return $this->select($sql);
    }
    
    public function getActiveByYear(int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suzug_user_details')->where('active', 1)
                ->and()->where('year', $year)->orderBy('number');
        return $this->select($sql);
    }
    
    public function isNotReserved(int $year, string $number) : bool {
        $item = $this->getActiveByYearAndNumber($year, $number);
        if($item->length() === 0 ){
            return true;
        }
        return false;
    }
    
    public function getActiveByYearAndNumber(int $year, string $number) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suzug_user_details')->where('active', 1)
                ->and()->where('year', $year)
                ->and()->where('number', $number);
        $result = $this->select($sql);
        if($result->length() > 1){
            $this->throwDataAccessException('number ' . $number . ' active two times for year ' . $year . 'in suzug_user');
        }
        if($result->length() === 0){
            return new Container();
        }
        else{
            return new Container($result->get(0));
        }
    }
    
    public function isNotAssigned(int $year, string $idUser) : bool {
        $item = $this->getActiveByYearAndUser($year, $idUser);
        if($item->length() === 0 ){
            return true;
        }
        return false;
    }
    
    public function getActiveByYearAndUser(int $year, string $idUser) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suzug_user_details')->where('active', 1)
                ->and()->where('year', $year)
                ->and()->where('id_user', $idUser);
        $result = $this->select($sql);
        if($result->length() > 1){
            $this->throwDataAccessException('id_user ' . $idUser . ' active two times for year ' . $year . 'in suzug_user');
        }
        if($result->length() === 0){
            return new Container();
        }
        else{
            return new Container($result->get(0));
        }
    }
    
}
