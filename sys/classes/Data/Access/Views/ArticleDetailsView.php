<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of ArticleDetailsView
 *
 * @author Tanzar
 */
class ArticleDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'article_details';
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('id', $id);
        $articles = $this->select($sql);
        if($articles->length() > 1){
            $this->throwIdColumnException('document');
        }
        if($articles->isEmpty()){
            return new Container();
        }
        else{
            return new Container($articles->get(0));
        }
    }
    
    public function getAllUserArticlesByDocumentId(string $username, int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('id_document', $documentId)
                ->and()->where('username', $username);
        return $this->select($sql);
    }
    
    public function getAllUserArticlesByYear(string $username, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('year(date)', $year)
                ->and()->where('username', $username)->orderBy('date', false);
        return $this->select($sql);
    }
    
    public function getActiveArticlesByUserAndYear(string $username, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('active', 1)->and()
                ->where('year(date)', $year)->and()->where('username', $username)
                ->orderBy('date', false);
        return $this->select($sql);
    }
    
    public function getUserActiveArticlesByDocumentId(string $username, int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('active', 1)
                ->and()->where('id_document', $documentId)
                ->and()->where('username', $username);
        return $this->select($sql);
    }
    
    public function getUserActiveByYear(string $username, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByMonthAndYear(int $month, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('active', 1)
                ->and()->where('month(date)', $month)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('article_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}
