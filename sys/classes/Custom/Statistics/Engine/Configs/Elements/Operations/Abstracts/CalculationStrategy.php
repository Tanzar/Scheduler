<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts;

use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Tanweb\Container as Container;

/**
 *
 * @author Tanzar
 */
interface CalculationStrategy {
    
    public function calculate(Container $data, GroupsContainer $groups) : ResultSet;
}
