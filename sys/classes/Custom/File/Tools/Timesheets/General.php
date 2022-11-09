<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Data\Access\Tables\UserDAO as UserDAO;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView; 
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;

/**
 * Description of General
 *
 * @author Tanzar
 */
class General {
    private int $month;
    private int $year;
    private string $username;
    private string $fullUserName;
    private int $standardFullTime;
    private string $organization;
    
    public function __construct(int $month, int $year, string $username) {
        $appConfig = AppConfig::getInstance();
        $config = $appConfig->getAppConfig();
        $this->organization = $config->get('organization');
        $this->month = $month;
        $this->year = $year;
        $this->username = $username;
        $this->loadUser();
        $this->loadStandardFullTime();
    }
    
    private function loadUser() : void {
        $dao = new UserDAO();
        $this->user = $dao->getByUsername($this->username);
        $this->fullUserName = $this->user->get('name') . ' ' . $this->user->get('surname');
    }
    
    private function loadStandardFullTime() : void {
        $view = new UsersEmploymentPeriodsView();
        $periods = $view->getByUserMonthYear($this->username, $this->month, $this->year);
        $time = 8 * 60 * 60 * 1000;
        foreach ($periods->toArray() as $item) {
            $period = new Container($item);
            $start = $period->get('standard_day_start');
            $end = $period->get('standard_day_end');
            $worktime = (int) (strtotime($end) - strtotime($start)) * 1000;
            if($worktime < $time){
                $time = $worktime;
            }
        }
        $this->standardFullTime = $time;
    }
    
    public function getMonth(): int {
        return $this->month;
    }

    public function getYear(): int {
        return $this->year;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getFullUserName(): string {
        return $this->fullUserName;
    }

    public function getStandardFullTime(): int {
        return $this->standardFullTime;
    }

    public function getOrganization(): string {
        return $this->organization;
    }

}
