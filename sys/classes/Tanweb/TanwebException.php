<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb;

use Exception;

/**
 * Base exception for all of Tanweb
 *
 * @author Grzegorz Spakowski, Tanzar
 */
abstract class TanwebException extends Exception{
    
    public abstract function errorMessage() : string;
}
