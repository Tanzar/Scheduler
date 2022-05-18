<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Mailing;

/**
 * Element serving as destination dor emails send by Postman class
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Email {
    private string $address;
    private string $title;
    private string $contents;
    private bool $isHTML;
    
    public function __construct(string $address, string $title, string $contents, bool $isHTML = false) {
        $this->address = $address;
        $this->title = $title;
        $this->contents = $contents;
        $this->isHTML = $isHTML;
    }
    
    public function getAddress(): string {
        return $this->address;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getContents(): string {
        return $this->contents;
    }

    public function getIsHTML(): bool {
        return $this->isHTML;
    }
}
