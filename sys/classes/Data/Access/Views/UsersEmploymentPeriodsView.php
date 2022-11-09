<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of UserEmploymentPeriodsDAO
 *
 * @author Tanzar
 */
class UsersEmploymentPeriodsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'users_employment_periods';
    }
    
    public function getByUserAndDate(string $username, string $date) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('username', $username)
                ->and()->where('start', $date, '<')
                ->and()->where('end', $date, '>');
        return $this->select($sql);
    }
    
    public function getByUserMonthYear(string $username, int $month, int $year) : Container{
        $monthStart = date($year . '-' . $month . '-1');
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('username', $username)
                ->and()->where('start', $monthEnd, '<=')->and()->where('end', $monthStart, '>=');
        return $this->select($sql);
    }
    
    public function getOrderedActiveByDate(string $date) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('active', 1)
                ->and()->where('start', $date, '<=')
                ->and()->where('end', $date, '>=')
                ->orderBy('sort_priority', true);
        return $this->select($sql);
    }
    
    public function getOrderedActiveByMonthAndYear(int $month, int $year) : Container{
        $monthStart = date($year . '-' . $month . '-1');
        $monthEnd = date("Y-m-t", strtotime($monthStart));
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('active', 1)
                ->and()->openBracket()->openBracket()
                ->where('start', $monthStart, '<=')->and()->where('end', $monthStart, '>=')
                ->closeBracket()->or()->openBracket()
                ->where('start', $monthEnd, '<=')->and()->where('end', $monthEnd, '>=')
                ->closeBracket()->closeBracket()
                ->orderBy('sort_priority', true)->orderBy('surname', true);
        return $this->select($sql);
    }
    
    public function getByUsernameAndDatesRange(string $username, DateTime $start, DateTime $end) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('active', 1)
                ->and()->where('start', $end->format("Y-m-d"), '<=')
                ->and()->where('end', $start->format("Y-m-d"), '>=')
                ->and()->where('username', $username)
                ->orderBy('start');
        $data = $this->select($sql);
        return $data;
    }
    
    public function getByUsernameToDate(string $username, DateTime $date) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_employment_periods')->where('active', 1)
                ->and()->where('start', $date->format("Y-m-d"), '<=')
                ->and()->where('username', $username)
                ->orderBy('start');
        $data = $this->select($sql);
        return $data;
    }
}
