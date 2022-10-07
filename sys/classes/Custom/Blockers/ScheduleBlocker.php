<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Blockers;

use Custom\Blockers\Abstracts\Blockers as Blockers;
use Custom\Blockers\Abstracts\OperationBlocker as OperationBlocker;
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
        $earliestAllowedDate = $this->getEarliestAllowedDate($blockerValue);
        if($earliestAllowedDate <= $startDate){
            return false;
        }
        else{
            return true;
        }
    }

    private function getEarliestAllowedDate(int $blockerValue) : DateTime {
        $current = new DateTime();
        $blockerDate = $this->findBlockerDay($blockerValue);
        if($current <= $blockerDate){
            return $this->getFirstDayOfPreviousMonth();
        }
        else{
            return $this->getFirstDayOfCurrentMonth();
        }
    }
    
    private function findBlockerDay(int $blockerValue) : DateTime {
        $current = new DateTime();
        $blockerDate = new DateTime($current->format('Y-m-') . $blockerValue . ' 23:59:59');
        if((int) $blockerDate->format('N') === 6){  //saturday
            $blockerDate->modify('+1 day');
        }
        if((int) $blockerDate->format('N') === 7){  //sunday
            $blockerDate->modify('+1 day');
        }
        return $blockerDate;
    }
    
    private function getFirstDayOfPreviousMonth() : DateTime {
        $date = new DateTime();
        $day = ((int) $date->format('j')) + 1;
        $date->modify('-' . $day . ' day');
        return new DateTime($date->format('Y-m-') . '1 00:00:00');
    }
    
    private function getFirstDayOfCurrentMonth() : DateTime {
        $date = new DateTime();
        $day = (int) $date->format('j');
        return new DateTime($date->format('Y-m-') . '1 00:00:00');
    }
}
