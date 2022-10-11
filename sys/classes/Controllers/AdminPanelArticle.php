<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ArticleService as ArticleService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelArticle
 *
 * @author Tanzar
 */
class AdminPanelArticle extends Controller{
    private ArticleService $articleService;
    
    public function __construct() {
        $this->articleService = new ArticleService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getAllUserArticles(){
        $data = $this->getRequestData();
        $username = $data->get('username');
        $year = (int) $data->get('year');
        $response = $this->articleService->getUserActiveArticlesByYear($username, $year);
        $this->setResponse($response);
    }
    
    public function getAllArticleForms(){
        $response = $this->articleService->getAllArticleForms();
        $this->setResponse($response);
    }
    
    public function getEditArticleDetails() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_document');
        $response = $this->articleService->getNewArticleDetails($id);
        $this->setResponse($response);
    }
    
    public function saveArticle(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $this->articleService->updateArticle($data);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveArticleForm(){
        $data = $this->getRequestData();
        $id = $this->articleService->saveArticleForm($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeArticleFormStatus(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->articleService->changeArticleFormStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeArticleStatus(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->articleService->changeArticleStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}
