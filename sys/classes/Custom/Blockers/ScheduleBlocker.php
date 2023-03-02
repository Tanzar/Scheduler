<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Blockers;

use Custom\Blockers\Abstracts\Blockers as Blockers;
use Custom\Blockers\Abstracts\OperationBlocker as OperationBlocker;
use Custom\Dates\DayOffChecker as DayOffChecker;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of ScheduleBlocker
 *
 * @author Tanzar
 */
class ScheduleBlocker extends OperationBlocker {
    
    protected function setBlockerType(): Blockers {
        return Blockers::SCHEDULE();
    }

    protected function setOverwritingPrivilages(): Container {
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('schedule_admin');
        return $privilages;
    }

    protected function shouldBeBlocked(Container $input, int $blockerValue): bool {
        $start = $input->get('start');
        $startDate = new DateTime($start);
        $blockerDate = $this->getBlockerDate($blockerValue);
        if($blockerDate > $startDate){
            return false;
        }
        else{
            return true;
        }
    }

    private function getBlockerDate(int $blockerValue) : DateTime {
        $current = $this->getBlockerDateInCurrentMonth($blockerValue);
        $next = $this->getBlockerDateInNextMonth($blockerValue);
        $today = new DateTime();
        if($today < $current){
            return $current;
        }
        else{
            return $next;
        }
    }
    
    private function getBlockerDateInCurrentMonth(int $blockerValue) : DateTime {
        $current = new DateTime();
        $year = (int) $current->format('Y');
        $month = (int) $current->format('m');
        return $this->findBlockerDateInMonnth($year, $month, $blockerValue);
    }
    
    private function getBlockerDateInNextMonth(int $blockerValue) : DateTime {
        $current = new DateTime();
        $current->modify('+1 months');
        $year = (int) $current->format('Y');
        $month = (int) $current->format('m');
        return $this->findBlockerDateInMonnth($year, $month, $blockerValue);
    }
    
    private function findBlockerDateInMonnth(int $year, int $month, int $blockerValue) : DateTime {
        $blockerDate = new DateTime($year . '-' . $month . '-' . $blockerValue);
        $monthEnd = new DateTime($year . '-' . $month . '-01');
        $monthEnd->modify('+1 months');
        $checker = new DayOffChecker();
        $blocked = true;
        while($blocked && $blockerDate < $monthEnd){
            if(!$checker->isDayOff($blockerDate)){
                $blocked = false;
            }
            $blockerDate->modify('+1 days');
        }
        return $blockerDate;
    }
    
    public function getNextBLockerDate() : DateTime {
        $blockers = Blockers::SCHEDULE();
        $blockerValue = $blockers->getConfigValue();
        $date = $this->getBlockerDate($blockerValue);
        return $date;
    }
}
