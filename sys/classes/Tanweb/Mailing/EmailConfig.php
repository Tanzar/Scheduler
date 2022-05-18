<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Mailing;

use Tanweb\Container as Container;

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
    
    
    public function __construct(Container $config) {
        $this->port = $config->getValue('port');
        $this->host = $config->getValue('host');
        $this->user = $config->getValue('user');
        $this->pass = $config->getValue('pass');
        $this->displayUser = $config->getValue('displayUser');
        $this->address = $config->getValue('email');
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
