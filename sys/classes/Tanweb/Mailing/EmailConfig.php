<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Mailing;

use Tanweb\Config\INI\AppConfig as AppConfig;

/**
 * Config for Postman
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class EmailConfig {
    private int $port;
    private string $host;
    private string $user;
    private string $pass;
    private string $displayUser;
    private string $address;
    
    
    public function __construct() {
        $appConfig = AppConfig::getInstance();
        $config = $appConfig->getMailer();
        $this->port = $config->get('port');
        $this->host = $config->get('host');
        $this->user = $config->get('user');
        $this->pass = $config->get('pass');
        $this->displayUser = $config->get('displayUser');
        $this->address = $config->get('email');
    }
    
    public function getPort(): int {
        return $this->port;
    }

    public function getHost(): string {
        return $this->host;
    }

    public function getUser(): string {
        return $this->user;
    }

    public function getPass(): string {
        return $this->pass;
    }

    public function getDisplayUser(): string {
        return $this->displayUser;
    }

    public function getAddress(): string {
        return $this->address;
    }
}
