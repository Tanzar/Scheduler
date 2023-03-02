<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of SuspensionDetailsView
 *
 * @author Tanzar
 */
class SuspensionDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'suspension_details';
    }
    
    public function getActiveByUsernameAndIdDocument(string $username, int $idDocument) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_details')->where('active', 1)->and()
                ->where('username', $username)->and()
                ->where('id_document', $idDocument);
        return $this->select($sql);
    }
    
    public function getAllByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_details')
                ->where('username', $username)->and()
                ->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_details')->where('active', 1)->and()
                ->where('username', $username)->and()
                ->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('suspension_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}
