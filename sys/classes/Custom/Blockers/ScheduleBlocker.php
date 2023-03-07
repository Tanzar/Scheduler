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

    protected function getInputDate(Container $input): DateTime {
        $date = $input->get('start');
        return new DateTime($date);
    }

}
