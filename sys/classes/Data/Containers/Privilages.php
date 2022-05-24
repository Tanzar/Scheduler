<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Containers;

use Data\Containers\DataContainer as DataContainer;

/**
 * Description of Privilages
 *
 * @author Tanzar
 */
class Privilages extends DataContainer{
    
    protected function setItemsType(): string {
        return 'Data\\Entities\\Privilage';
    }

}
