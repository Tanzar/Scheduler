<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\ArticleService as ArticleService;
use Services\DocumentService as DocumentService;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorArticle
 *
 * @author Tanzar
 */
class InspectorArticle extends Controller{
    private DocumentService $documentService;
    private ArticleService $articleService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->articleService = new ArticleService();
        $privilages = new Container(['admin', 'schedule_user_inspector']);
        parent::__construct($privilages);
    }
    
    public function getDocuments() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->documentService->getCurrentUserDocumentsByYear($year);
        $this->setResponse($response);
    }
    
    public function getArticles(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->articleService->getCurrentUserActiveArticlesByDocument($id);
        $this->setResponse($response);
    }
    
    public function getArticlesByYear() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->articleService->getCurrentUserActiveArticlesByYear($year);
        $this->setResponse($response);
    }
    
    public function getNewArticleDetails() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->articleService->getNewArticleDetails($id);
        $this->setResponse($response);
    }
    
    public function saveNewArticle(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->articleService->saveNewArticle($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function updateArticle(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $this->articleService->updateArticle($data);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeArticle(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = (int) $data->get('id');
        $this->articleService->removeArticle($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}
