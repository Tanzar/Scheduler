<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of DaysOffUserDAO
 *
 * @author Tanzar
 */
class DaysOffUserDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'days_off_user';
    }
    
    public function getByIds(int $userId, int $dayOffId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off_user')->where('id_days_off', $dayOffId)
                ->and()->where('id_user', $userId);
        $data = $this->select($sql);
        if($data->length() > 1){
            $this->throwIdColumnException('days_off_user');
        }
        if($data->length() === 0){
            return new Container();
        }
        else{
            return new Container($data->get(0));
        }
    }
}
