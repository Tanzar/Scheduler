<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Session as Session;

/**
 * Base for all log entries, 
 * extend this if you want to have custom entries in logger
 *
 * @author Grzegorz Spakowski, Tanzar
 */
abstract class LogEntry {
    private $timestamp;
    private string $user;
    private string $type;
    private string $message;
    private string $ip;
    private string $proxy;
    
    public function __construct(string $msg){
        $this->timestamp = date('Y-m-d  H:i:s');
        $this->user = Session::getUsername();
        $this->type = $this->setType();
        $this->message = $msg;
        $this->ip = Session::getIP();
        $this->proxy = Session::getProxy();
    }
    
    protected abstract function setType() : string;
    
    public function getTimestamp(){
        return $this->timestamp;
    }
    
    public function getUser(): string {
        return $this->user;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getMessage(): string {
        return $this->message;
    }
    
    public function getIp(): string {
        return $this->ip;
    }

    public function getProxy(): string {
        return $this->proxy;
    }
    
    public function setUser(string $user) : void {
        $this->user = $user;
    }
}
