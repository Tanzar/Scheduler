<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of DocumentScheduleDetailsDAO
 *
 * @author Tanzar
 */
class DocumentEntriesDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
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
    
    public function getActiveByEntryId(int $entryId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_entries_details')->where('active', 1)
                ->and()->where('id', $entryId);
        return $this->select($sql);
    }
}
