<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Entities;

/**
 *
 * @author Tanzar
 */
interface Entity {
    
    public function __construct(array $data);
    
    public function toArray() : array;
}
