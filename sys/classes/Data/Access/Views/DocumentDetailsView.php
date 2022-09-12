<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of DocumentDetailsDAO
 *
 * @author Tanzar
 */
class DocumentDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'document_details';
    }
    
    public function getById(int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_details')->where('id', $documentId);
        $documents = $this->select($sql);
        if($documents->length() > 1){
            $this->throwIdColumnException('document');
        }
        if($documents->isEmpty()){
            return new Container();
        }
        else{
            return new Container($documents->get(0));
        }
    }
    
    public function getActiveByMonthAndYear(int $month, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_details')->where('active', 1)
                ->and()->openBracket()->openBracket()
                ->where('month(start)', $month)->and()
                ->where('year(start)', $year)->closeBracket()
                ->or()->openBracket()
                ->where('month(end)', $month)->and()->where('year(end)', $year)
                ->closeBracket()->closeBracket()
                ->orderBy('start');
        return $this->select($sql);
    }
    
    public function getAllByMonthAndYear(int $month, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_details')->openBracket()->openBracket()
                ->where('month(start)', $month)->and()
                ->where('year(start)', $year)->closeBracket()
                ->or()->openBracket()
                ->where('month(end)', $month)->and()->where('year(end)', $year)
                ->closeBracket()->closeBracket()
                ->orderBy('start');
        return $this->select($sql);
    }
    
}
