<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of InstrumentUsageDetailsView
 *
 * @author Tanzar
 */
class InstrumentUsageDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'instrument_usage_details';
    }
    
    public function getActiveByDocumentAndUsername(int $documentId, string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('instrument_usage_details')->where('active', 1)
                ->and()->where('id_document', $documentId)
                ->and()->where('document_assigned_username', $username);
        return $this->select($sql);
    }
    
    public function getAllByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('instrument_usage_details')->where('year(date)', $year)
                ->and()->where('document_assigned_username', $username);
        return $this->select($sql);
    }
}
