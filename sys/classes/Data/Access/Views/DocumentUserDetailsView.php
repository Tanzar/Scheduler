<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of DocumentUserDetails
 *
 * @author Tanzar
 */
class DocumentUserDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'document_user_details';
    }
    
    public function getByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('id', $documentId);
        return $this->select($sql);
    }
    
    public function getByUsernameAndDocumentId(string $username, int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('username', $username)
                ->and()->where('id', $documentId);
        $results = $this->select($sql);
        if($results->length() > 1){
            $this->throwDataAccessException('multiple versions of same relation '
                    . 'in document_user for document id: ' . $documentId . ' '
                    . 'username: ' . $username);
        }
        if($results->length() === 1){
            return new Container($results->get(0));
        }
        else{
            return new Container();
        }
    }
    
    public function getActiveByMonthYearUsername(int $month, int $year, string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('document_active', 1)
                ->and()->where('username', $username)
                ->and()->openBracket()->openBracket()
                ->where('month(start)', $month)->and()
                ->where('year(start)', $year)->closeBracket()
                ->or()->openBracket()
                ->where('month(end)', $month)->and()->where('year(end)', $year)
                ->closeBracket()->closeBracket();
        return $this->select($sql);
    }
    
    public function getActiveByYearUsername(int $year, string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('document_active', 1)
                ->and()->where('username', $username)
                ->and()->openBracket()
                ->where('year(start)', $year)->or()->where('year(end)', $year)
                ->closeBracket();
        return $this->select($sql);
    }
    
    public function getActiveDocumentByYearUsername(int $year, string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('document_active', 1)
                ->and()->where('username', $username)
                ->and()->openBracket()
                ->where('year(start)', $year)->or()->where('year(end)', $year)
                ->closeBracket();
        return $this->select($sql);
    }
    
    public function getActiveByUsernameLocationIdEntryDates(string $username, int $locationId, string $start, string $end) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('document_active', 1)
                ->and()->where('username', $username)
                ->and()->where('start', $start, '<=')
                ->and()->where('end', $end, '>=')
                ->and()->where('id_location', $locationId);
        return $this->select($sql);
    }
    
}
