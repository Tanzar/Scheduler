<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of DosumentUserDAO
 *
 * @author Tanzar
 */
class DocumentUserDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'document_user';
    }

    public function getAllByUserAndDocument(int $userId, int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user')->where('id_document', $documentId)
                ->and()->where('id_user', $userId);
        $relations = $this->select($sql);
        if($relations->length() > 1){
            $this->throwDataAccessException('combinations of id_document and id_user must be unique in document_user');
        }
        if($relations->length() == 1){
            return new Container($relations->get(0));
        }
        else{
            return new Container();
        }
    }
    
}
