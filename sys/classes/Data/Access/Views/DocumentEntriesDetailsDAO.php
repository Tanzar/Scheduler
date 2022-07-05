<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of DocumentScheduleDetailsDAO
 *
 * @author Tanzar
 */
class DocumentEntriesDetailsDAO extends DAO{
    
    public function __construct() {
        parent::__construct(true);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'document_entries_details';
    }
    
    public function getActiveByUsernameAndDocumentId(string $username, int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_entries_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_entries_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}
