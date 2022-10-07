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
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;

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
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->documentService->getCurrentUserDocumentsByMonthYear($month, $year);
        $this->setResponse($response);
    }
    
    public function getArticles(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->articleService->getCurrentUserActiveArticlesByDocument($id);
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
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        else{
            $id = $this->articleService->saveNewArticle($data);
        }
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function updateArticle(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        else{
            $this->articleService->updateArticle($data);
        }
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeArticle(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = (int) $data->get('id');
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        else{
            $this->articleService->removeArticle($id);
        }
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}
