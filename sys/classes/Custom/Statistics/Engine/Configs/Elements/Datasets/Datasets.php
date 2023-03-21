<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Datasets;

use Custom\Statistics\Engine\Configs\Elements\Datasets\Dataset as Dataset;
use Tanweb\Container as Container;

/**
 * Description of Datasets
 *
 * @author Tanzar
 */
class Datasets {
    private Container $items;
    
    public function __construct() {
        $this->items = new Container();
    }
    
    public function add(string $index, Dataset $item) : void {
        $this->items->add($item, $index);
    }
    
    public function get(string $index) : Dataset {
        return $this->items->get($index);
    }
    
    public function toArray() : array {
        return $this->items->toArray();
    }
}
