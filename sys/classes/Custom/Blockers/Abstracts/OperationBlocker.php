<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Blockers\Abstracts;

use Custom\Blockers\Abstracts\Blockers as Blockers;
use Tanweb\Security\Security as Security;
use Tanweb\Container as Container;
/**
 * Description of OperationBlocker
 *
 * @author Tanzar
 */
abstract class OperationBlocker {
    private Blockers $block;
    private Container $privilages;
    private Security $security;

    public function __construct() {
        $this->block = $this->setBlockerType();
        $this->privilages = $this->setOverwritingPrivilages();
        $this->security = Security::getInstance();
    }
    
    final public function isBLocked(Container $input) : bool {
        if($this->security->userHaveAnyPrivilage($this->privilages)){
            return false;
        }
        else{
            $blockerValue = (int) $this->block->getConfigValue();
            return $this->shouldBeBlocked($input, $blockerValue);
        }
    }
    
    protected abstract function setBlockerType() : Blockers;
    
    /**
     * Privilages whitch allow to ignore blocker
     */
    protected abstract function setOverwritingPrivilages() : Container;
    
    protected abstract function shouldBeBlocked(Container $input, int $blockerValue) : bool;
}
