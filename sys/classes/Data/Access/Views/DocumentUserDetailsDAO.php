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
 * Description of DocumentUserDetails
 *
 * @author Tanzar
 */
class DocumentUserDetailsDAO extends DAO{
    
    public function __construct() {
        parent::__construct(true);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'document_user_details';
    }
    
    public function getAllByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('id_document', $documentId);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')
                ->where('active', 1)->and()
                ->where('id_document', $documentId);
        return $this->select($sql);
    }
    
    public function getActiveByMonthYearUsername(int $month, int $year, string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('active', 1)
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
        $sql->select('document_user_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->openBracket()
                ->where('year(start)', $year)->or()->where('year(end)', $year)
                ->closeBracket();
        return $this->select($sql);
    }
    
    public function getActiveDocumentByYearUsername(int $year, string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('active_document', 1)
                ->and()->where('active', 1)
                ->and()->where('username', $username)
                ->and()->openBracket()
                ->where('year(start)', $year)->or()->where('year(end)', $year)
                ->closeBracket();
        return $this->select($sql);
    }
    
    public function getActiveByUsernameAndEntryDates(string $username, string $start, string $end) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_user_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('start', $start, '<=')
                ->and()->where('end', $end, '>=');
        return $this->select($sql);
    }
    
}
