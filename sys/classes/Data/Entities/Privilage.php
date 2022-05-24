<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Entities;

use Data\Entities\Entity as Entity;

/**
 * Description of Privilage
 *
 * @author Tanzar
 */
class Privilage implements Entity{
    private int $id = 0;
    private bool $active = true;
    private string $privilage = '';
    private int $idUser = 0;
    
    
    public function __construct(array $data) {
        if(isset($data['id'])){
            $this->id = $data['id'];
        }
        if(isset($data['active'])){
            $this->active = $data['active'];
        }
        if(isset($data['privilage'])){
            $this->privilage = $data['privilage'];
        }
        if(isset($data['id_user'])){
            $this->idUser = $data['id_user'];
        }
    }
    
    public function getId(): int {
        return $this->id;
    }

    public function getActive(): bool {
        return $this->active;
    }

    public function getPrivilage(): string {
        return $this->privilage;
    }

    public function getIdUser(): int {
        return $this->idUser;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function setPrivilage(string $privilage): void {
        $this->privilage = $privilage;
    }

    public function setIdUser(int $idUser): void {
        $this->idUser = $idUser;
    }

    public function is(string $privilage) : bool {
        if($this->privilage === $privilage){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function toArray(): array {
        $arr = array();
        $arr['id'] = $this->id;
        $arr['active'] = $this->active;
        $arr['privilage'] = $this->privilage;
        $arr['id_user'] = $this->idUser;
        return $arr;
    }

}
