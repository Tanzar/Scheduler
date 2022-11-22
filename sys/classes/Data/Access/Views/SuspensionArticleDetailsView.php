<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of SuspensionArticleView
 *
 * @author Tanzar
 */
class SuspensionArticleDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'suspension_art_41_details';
    }
    
    public function getActiveByUsernameAndIdSuspension(string $username, int $idSuspension) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_art_41_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('id_suspension', $idSuspension);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('suspension_art_41_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}
