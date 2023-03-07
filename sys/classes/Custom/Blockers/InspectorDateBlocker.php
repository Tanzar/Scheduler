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
 * Description of InspectorDateBlocker
 *
 * @author Tanzar
 */
class InspectorDateBlocker extends OperationBlocker{
    
    protected function setBlockerType(): Blockers {
        return Blockers::INSPECTOR();
    }

    protected function setOverwritingPrivilages(): Container {
        $privilages = new Container();
        $privilages->add('admin');
        return $privilages;
    }

    protected function getInputDate(Container $input): DateTime {
        $date = $input->get('date');
        return new DateTime($date);
    }

}
