<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Tables\ArticleDAO as ArticleDAO;
use Data\Access\Tables\ArticleFormDAO as ArticleFormDAO;
use Data\Access\Tables\PositionGroupsDAO as PositionGroupsDAO;
use Data\Access\Tables\DocumentUserDAO as DocumentUserDAO;
use Data\Access\Views\ArticleDetailsView as ArticleDetailsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;

/**
 * Description of ArticleService (art. 41)
 *
 * @author Tanzar
 */
class ArticleService {
    private DocumentDAO $document;
    private ArticleDAO $article;
    private ArticleFormDAO $articleForm;
    private PositionGroupsDAO $positionGroups;
    private DocumentUserDAO $documentUser;
    private ArticleDetailsView $articleDetails;
    private UsersWithoutPasswordsView $users;
    
    public function __construct(){
        $this->document = new DocumentDAO();
        $this->article = new ArticleDAO();
        $this->articleForm = new ArticleFormDAO();
        $this->positionGroups = new PositionGroupsDAO();
        $this->documentUser = new DocumentUserDAO();
        $this->articleDetails = new ArticleDetailsView();
        $this->users = new UsersWithoutPasswordsView();
    }
    
    public function getAllArticleForms() : Container {
        return $this->articleForm->getAll();
    }
    
    public function getActiveArticleForms() : Container {
        return $this->articleForm->getActive();
    }
    
    public function getCurrentUserActiveArticlesByDocument(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->articleDetails->getUserActiveArticlesByDocumentId($username, $documentId);
    }
    
    public function getUserActiveArticlesByYear(string $username, int $year) : Container {
        return $this->articleDetails->getAllUserArticlesByYear($username, $year);
    }
    
    public function saveArticleForm(Container $data) : int {
        return $this->articleForm->save($data);
    }
    
    public function changeArticleFormStatus(int $id) : void {
        $form = $this->articleForm->getById($id);
        $active = $form->get('active');
        if($active){
            $this->articleForm->disable($id);
        }
        else{
            $this->articleForm->enable($id);
        }
    }
    
    public function changeArticleStatus(int $id) : void {
        $form = $this->article->getById($id);
        $active = $form->get('active');
        if($active){
            $this->article->disable($id);
        }
        else{
            $this->article->enable($id);
        }
    }
    
    public function getNewArticleDetails(int $documentId) : Container {
        $document = $this->document->getById($documentId);
        $start = $document->get('start');
        $end = $document->get('end');
        $forms = $this->articleForm->getActive();
        $groups = $this->positionGroups->getActive();
        $result = new Container();
        $result->add($start, 'start');
        $result->add($end, 'end');
        $result->add($forms->toArray(), 'forms');
        $result->add($groups->toArray(), 'position_groups');
        return $result;
    }
    
    public function saveNewArticle(Container $data) : int {
        $username = Session::getUsername();
        $documentId = (int) $data->get('id_document');
        $documentUserId = $this->getDocumentUserId($username, $documentId);
        $data->add($documentUserId, 'id_document_user');
        $article = $this->formArticle($data);
        return $this->article->save($article);
    }
    
    private function getDocumentUserId(string $username, int $documentId) : int {
        $user = $this->users->getByUsername($username);
        $userId = (int) $user->get('id');
        $relation = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        return (int) $relation->get('id');
    }
    
    public function updateArticle(Container $data) : void {
        $article = $this->formArticle($data);
        $this->article->save($article);
    }
    
    public function removeArticle(int $id) : void {
        $this->article->disable($id);
    }
    
    private function formArticle(Container $data) : Container {
        $art = new Container();
        if($data->isValueSet('id')){
            $art->add($data->get('id'), 'id');
        }
        $art->add($data->get('position'), 'position');
        $art->add($data->get('applicant'), 'applicant');
        $art->add($data->get('application_number'), 'application_number');
        $art->add($data->get('application_date'), 'application_date');
        $art->add($data->get('date'), 'date');
        $art->add($data->get('external_company'), 'external_company');
        $art->add($data->get('company_name'), 'company_name');
        $art->add($data->get('remarks'), 'remarks');
        $art->add($data->get('id_art_41_form'), 'id_art_41_form');
        $art->add($data->get('id_position_groups'), 'id_position_groups');
        $art->add($data->get('id_document_user'), 'id_document_user');
        return $art;
    }
}
