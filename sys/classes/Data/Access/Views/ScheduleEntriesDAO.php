<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of ScheduleEntries
 *
 * @author Tanzar
 */
class ScheduleEntriesDAO extends DAO{
    
    public function __construct() {
        parent::__construct(true);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'schedule_entries';
    }
    
    public function getActive(DateTime $start, DateTime $end) : Container{
        $sql = new MysqlBuilder();
        $sql->select('schedule_entries')->where('start', $end->format("Y-m-d H:i:s"), '<')
                ->and()->where('end', $start->format("Y-m-d H:i:s"), '>')
                ->and()->where('active', 1)
                ->orderBy('start');
        $data = $this->select($sql);
        return $data;
    }
    
    public function getActiveByUserId(int $userId, DateTime $start, DateTime $end) : Container{
        $sql = new MysqlBuilder();
        $sql->select('schedule_entries')->where('start', $end->format("Y-m-d H:i:s"), '<')
                ->and()->where('end', $start->format("Y-m-d H:i:s"), '>')
                ->and()->where('id_user', $userId)
                ->and()->where('active', 1)
                ->orderBy('start');
        $data = $this->select($sql);
        return $data;
    }
    
    public function getByUserId(int $userId, DateTime $start, DateTime $end) : Container{
        $sql = new MysqlBuilder();
        $sql->select('schedule_entries')->where('start', $end->format("Y-m-d H:i:s"), '<')
                ->and()->where('end', $start->format("Y-m-d H:i:s"), '>')
                ->and()->where('id_user', $userId)
                ->and()->where('active', 1);
        $data = $this->select($sql);
        return $data;
    }
    
    public function getAllByUserId(int $userId, DateTime $start, DateTime $end) : Container{
        $sql = new MysqlBuilder();
        $sql->select('schedule_entries')->where('start', $end->format("Y-m-d H:i:s"), '<=')
                ->and()->where('end', $start->format("Y-m-d H:i:s"), '>=')
                ->and()->where('id_user', $userId);
        $data = $this->select($sql);
        return $data;
    }
}
