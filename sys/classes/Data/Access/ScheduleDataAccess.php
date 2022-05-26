<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Data\Access\DataAccess as DataAccess;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of ScheduleDataAccess
 *
 * @author Tanzar
 */
class ScheduleDataAccess extends DataAccess{
    //put your code here
    protected function setDatabaseIndex(): string {
        return 'schedule';
    }
    
    public function getActiveEntries(DateTime $start, DateTime $end) : Container{
        $sql = new MysqlBuilder();
        $sql->select('schedule_entries')->where('start', $end->format("Y-m-d H:i:s"), '<=')
                ->and()->where('end', $start->format("Y-m-d H:i:s"), '>=')
                ->and()->where('active', 1);
        $data = $this->select($sql);
        return $data;
    }
    
    public function getActiveUserEntries(int $userId, DateTime $start, DateTime $end){
        $sql = new MysqlBuilder();
        $sql->select('schedule_entries')->where('start', $end->format("Y-m-d H:i:s"), '<=')
                ->and()->where('end', $start->format("Y-m-d H:i:s"), '>=')
                ->and()->where('id_user', $userId)
                ->and()->where('active', 1);
        $data = $this->select($sql);
        return $data;
    }
    
    public function create(Container $data) : int{
        $sql = new MysqlBuilder();
        $sql->insert('schedule')
                ->into('start', $data->getValue('start')->format("Y-m-d H:i:s"))
                ->into('end', $data->getValue('end')->format("Y-m-d H:i:s"))
                ->into('location', $data->getValue('location'))
                ->into('id_user', $data->getValue('id_user'))
                ->into('id_activity', $data->getValue('id_activity'));
        return $this->insert($sql);
    }
    
    
    
}
