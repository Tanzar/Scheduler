<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Blockers\Abstracts;

use Custom\Blockers\Abstracts\Blockers as Blockers;
use Custom\Dates\DayOffChecker as DayOffChecker;
use Tanweb\Security\Security as Security;
use Tanweb\Container as Container;
use DateTime;
/**
 * Description of OperationBlocker
 *
 * @author Tanzar
 */
abstract class OperationBlocker {
    private Blockers $block;
    private Container $privilages;
    private Security $security;
    private int $blockerValue;

    public function __construct() {
        $this->block = $this->setBlockerType();
        $this->blockerValue = (int) $this->block->getConfigValue();
        $this->privilages = $this->setOverwritingPrivilages();
        $this->security = Security::getInstance();
    }
    
    final public function isBLocked(Container $input) : bool {
        if($this->security->userHaveAnyPrivilage($this->privilages)){
            return false;
        }
        else{
            return $this->shouldBeBlocked($input, $this->blockerValue);
        }
    }
    
    private function shouldBeBlocked(Container $input, int $blockerValue): bool {
        $date = $this->getInputDate($input);
        $blockerDate = $this->getBlockerDate($blockerValue);
        $lastUnblockedDate = new DateTime($blockerDate->format('Y-m') . '-01 00:00:00');
        $lastUnblockedDate->modify('-1 months');
        if($date >= $lastUnblockedDate){
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
    
    private function getBlockerDateInPreviousMonth(int $blockerValue) : DateTime {
        $current = new DateTime();
        $current->modify('-1 months');
        $year = (int) $current->format('Y');
        $month = (int) $current->format('m');
        return $this->findBlockerDateInMonnth($year, $month, $blockerValue);
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
        return $this->getBlockerDate($this->blockerValue);
    }
    
    protected abstract function setBlockerType() : Blockers;
    
    /**
     * Privilages whitch allow to ignore blocker
     */
    protected abstract function setOverwritingPrivilages() : Container;
    
    protected abstract function getInputDate(Container $input) : DateTime;
}
