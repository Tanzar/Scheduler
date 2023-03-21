<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups\Container;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Factory\GroupsFactory as GroupsFactory;
use Tanweb\Container as Container;

/**
 * Description of GroupsContainer
 *
 * @author Tanzar
 */
class GroupsContainer {
    private Container $items;
    
    public function __construct() {
        $this->items = new Container();
    }
    
    public function add(Group $item) : void {
        $this->items->add($item);
    }
    
    public function get(int $index) : Group {
        return $this->items->get($index);
    }
    
    public function toArray() : array {
        return $this->items->toArray();
    }
    
    public function copy() : GroupsContainer {
        $copy = new GroupsContainer();
        foreach ($this->items->toArray() as $item) {
            $type = $item->getType();
            $value = $item->getValue();
            $groupCopy = GroupsFactory::create($type, $value);
            $copy->add($groupCopy);
        }
        return $copy;
    }
}
