<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of SuspensionArticleDAO
 *
 * @author Tanzar
 */
class SuspensionArticleDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'suspension_art_41';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_art_41')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getBySuspensionAndArticle(int $idSuspension, int $idArticle) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_art_41')->where('id_suspension', $idSuspension)
                ->and()->where('id_art_41', $idArticle);
        $result = $this->select($sql);
        if($result->length() > 1){
            $this->throwDataAccessException('combinations for columns '
                    . 'id_suspension (' . $idSuspension . ') and id_art_41 (' 
                    . $idArticle . ') must be unique, duplicate found.');
        }
        if($result->length() === 0){
            $this->throwNotFoundException('relation not found');
        }
        else{
            return new Container($result->get(0));
        }
    }
}
