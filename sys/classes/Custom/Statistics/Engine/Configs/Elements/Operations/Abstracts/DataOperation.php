<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts;

use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\Operations as Operations;
use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\CalculationStrategy as CalculationStrategy;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Operations\Count as Count;
use Custom\Statistics\Engine\Configs\Elements\Operations\CountWorkdays as CountWorkdays;
use Custom\Statistics\Engine\Configs\Elements\Operations\CountShiftA as CountShiftA;
use Custom\Statistics\Engine\Configs\Elements\Operations\CountShiftB as CountShiftB;
use Custom\Statistics\Engine\Configs\Elements\Operations\CountShiftC as CountShiftC;
use Custom\Statistics\Engine\Configs\Elements\Operations\CountShiftD as CountShiftD;
use Custom\Statistics\Engine\Configs\Elements\Operations\Sum as Sum;
use Tanweb\Container as Container;

/**
 * Description of DataOperation
 *
 * @author Tanzar
 */
class DataOperation {
    private CalculationStrategy $strategy;
    
    public function __construct(Operations $operation) {
        switch ($operation) {
            case Operations::Count:
                $this->strategy = new Count();
                break;
            case Operations::CountWorkdays:
                $this->strategy = new CountWorkdays();
                break;
            case Operations::CountShiftA:
                $this->strategy = new CountShiftA();
                break;
            case Operations::CountShiftB:
                $this->strategy = new CountShiftB();
                break;
            case Operations::CountShiftC:
                $this->strategy = new CountShiftC();
                break;
            case Operations::CountShiftD:
                $this->strategy = new CountShiftD();
                break;
            case Operations::Sum:
                $this->strategy = new Sum();
                break;
            default:
                $this->strategy = new Count();
                break;
        }
    }
    
    public function formResult(Container $data, GroupsContainer $groups) : ResultSet {
        return $this->strategy->calculate($data, $groups);
    }
}
