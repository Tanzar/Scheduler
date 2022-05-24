<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Entities;

use Data\Entities\Entity as Entity;

/**
 * Description of User
 *
 * @author Tanzar
 */
class User implements Entity{
    private int $id = 0;
    private bool $active = true;
    private string $username = 'noname';
    private string $name = 'noname';
    private string $surname = 'noname';
    private string $password = '';
    
    public function __construct(array $data) {
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        if(isset($data['active'])){
            $this->active = $data['active'];
        }
        if(isset($data['username'])){
            $this->username = $data['username'];
        }
        if(isset($data['name'])){
            $this->name = $data['name'];
        }
        if(isset($data['surname'])){
            $this->surname = $data['surname'];
        }
        if(isset($data['password'])){
            $this->password = $data['password'];
        }
    }
    
    public function getId(): int {
        return $this->id;
    }

    public function getActive(): bool {
        return $this->active;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getSurname(): string {
        return $this->surname;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setSurname(string $surname): void {
        $this->surname = $surname;
    }
    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function toArray(): array {
        $arr = array();
        $arr['id'] = $this->id;
        $arr['active'] = $this->active;
        $arr['username'] = $this->username;
        $arr['name'] = $this->name;
        $arr['surname'] = $this->surname;
        $arr['password'] = $this->password;
        return $arr;
    }

}
