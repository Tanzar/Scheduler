<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Datasets;

use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Tanweb\Container as Container;

/**
 * Description of ResultSets
 *
 * @author Tanzar
 */
class ResultSets {
    private Container $items;
    
    public function __construct() {
        $this->items = new Container();
    }
    
    public function add(string $index, ResultSet $item) : void {
        $this->items->add($item, $index);
    }
    
    public function get(string $index) : ResultSet {
        return $this->items->get($index);
    }
    
    public function toArray() : array {
        return $this->items->toArray();
    }
}
