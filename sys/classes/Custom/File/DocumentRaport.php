<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Data\Access\Views\DocumentDetailsView as DocumentDetailsView;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Data\Access\Views\DocumentEntriesDetailsView as DocumentEntriesDetailsView;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Data\Access\Views\ArticleDetailsView as ArticleDetailsView;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\SuspensionDetailsView as SuspensionDetailsView;
use Data\Access\Views\SuspensionTicketDetailsView as SuspensionTicketDetailsView;
use Data\Access\Views\SuspensionArticleDetailsView as SuspensionArticleDetailsView;
use Data\Access\Views\SuspensionDecisionDetailsView as SuspensionDecisionDetailsView;
use Data\Access\Views\InstrumentUsageDetailsView as InstrumentUsageDetailsView;
use Data\Access\Views\CourtApplicationDetailsView as CourtApplicationDetailsView;
use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Session as Session;
use DateTime;

/**
 * Description of DocumentRaport
 *
 * @author Tanzar
 */
class DocumentRaport extends PDFMaker{
    private string $title;
    private int $documentId;
    private int $margin;
    private int $currentLeftMargin;
    private int $font;
    private Container $config;
    
    private string $userFullName;
    
    private string $number;
    private string $start;
    private string $end;
    private string $location;
    private string $description;
    private Container $assignedUsers;
    private Container $entries;
    private Container $tickets;
    private Container $articles;
    private Container $decisions;
    private Container $suspensions;
    private Container $suspensionsTickets;
    private Container $suspensionsArticles;
    private Container $suspensionsDecisions;
    private Container $usages;
    private Container $courts;


    private function __construct(int $documentId) {
        parent::__construct('P', 'A4');
        $this->SetFillColor(224, 224, 224);
        $this->font = 10;
        $this->setCurrentSize($this->font);
        $this->margin = 10;
        $this->currentLeftMargin = $this->margin;
        $this->setMargin('all', $this->margin);
        $this->documentId = $documentId;
        $appconfig = AppConfig::getInstance();
        $this->config = $appconfig->getAppConfig();
        $this->SetAuthor($this->config->get('name') . ' web app');
        $this->loadData();
        $this->title = 'Raport dla dokumentu: ' . $this->number;
        $this->SetTitle($this->title, true);
    }
    
    private function loadData() : void {
        $this->loadUser();
        $this->loadDocumentDetails();
        $this->loadDocumentUsers();
        $this->loadEntries();
        $this->loadTickets();
        $this->loadArticles();
        $this->loadDecisions();
        $this->loadSuspensions();
        $this->loadUsages();
        $this->loadCourtApplications();
    }
    
    private function loadUser() : void {
        $view = new UsersWithoutPasswordsView();
        $username = Session::getUsername();
        $user = $view->getByUsername($username);
        $this->userFullName = $user->get('name') . ' ' . $user->get('surname');
    }
    
    private function loadDocumentDetails() : void {
        $view = new DocumentDetailsView();
        $document = $view->getById($this->documentId);
        $this->number = $document->get('number');
        $this->location = $document->get('location');
        $this->start = $document->get('start');
        $this->end = $document->get('end');
        $this->description = $document->get('description');
    }
    
    private function loadDocumentUsers() : void {
        $view = new DocumentUserDetailsView();
        $this->assignedUsers = $view->getByDocumentId($this->documentId);
    }
    
    private function loadEntries() : void {
        $view = new DocumentEntriesDetailsView();
        $this->entries = $view->getActiveByDocumentId($this->documentId);
    }
    
    private function loadTickets() : void {
        $view = new TicketDetailsView();
        $this->tickets = $view->getActiveByDocumentId($this->documentId);
    }
    
    private function loadArticles() : void {
        $view = new ArticleDetailsView();
        $this->articles = $view->getActiveByDocumentId($this->documentId);
    }
    
    private function loadDecisions() : void {
        $view = new DecisionDetailsView();
        $this->decisions = $view->getActiveByDocumentId($this->documentId);
    }

    private function loadSuspensions() : void {
        $suspensionsView = new SuspensionDetailsView();
        $this->suspensions = $suspensionsView->getActiveByDocumentId($this->documentId);
        $suspensionsTickets = new SuspensionTicketDetailsView();
        $this->suspensionsTickets = $suspensionsTickets->getActiveByDocumentId($this->documentId);
        $suspensionArticles = new SuspensionArticleDetailsView();
        $this->suspensionsArticles = $suspensionArticles->getActiveByDocumentId($this->documentId);
        $suspensionDecisions = new SuspensionDecisionDetailsView();
        $this->suspensionsDecisions = $suspensionDecisions->getActiveByDocumentId($this->documentId);
    }

    private function loadUsages() : void {
        $view = new InstrumentUsageDetailsView();
        $this->usages = $view->getActiveByDocumentId($this->documentId);
    }

    private function loadCourtApplications() : void {
        $view = new CourtApplicationDetailsView();
        $this->courts = $view->getActiveByDocumentId($this->documentId);
    }

    public static function generate(int $documentId) : void {
        $pdf = new DocumentRaport($documentId);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    public function getTitle() : string {
        return $this->title;
    }
    
    private function print() : void {
        $this->printRaportHead();
        $this->printDocumentDetails();
        $this->printGroup('1. Wpisy w harmonogramie', 'entries', $this->entries);
        $this->printGroup('2. Mandaty', 'ticket', $this->tickets);
        $this->printGroup('3. Art. 41', 'article', $this->articles);
        $this->printGroup('4. Decyzje', 'decision', $this->decisions);
        $this->printGroup('5. Zatrzymania', 'suspension', $this->suspensions);
        $this->printGroup('6. Wykorzystanie przyrządu', 'usage', $this->usages);
        $this->printGroup('7. Wnioski do sądu', 'court', $this->courts);
    }
    
    private function printRaportHead() : void {
        $this->setCurrentSize(12);
        $this->writeCell(0, 16, 'Raport z dokumentu: ' . $this->number, 1, 'C', true);
        $this->Ln(16);
        $this->setCurrentSize($this->font);
        $this->writeCell(20, 5, 'Stan na:', 1);
        $this->writeCell(40, 5, date('d-m-Y H:i:s'), 1);
        $this->writeCell(40, 5, 'Wygenerowany dla:', 1);
        $this->writeCell(0, 5, $this->userFullName, 1);
        $this->Ln(5);
    }
    
    private function printDocumentDetails() : void {
        $this->Ln(10);
        $this->printDocumentDetailsTitle();
        $this->printDocumentGeneralDetails();
        $this->printAssignedUsersDetails();
        $this->printDocumentDescription();
    }
    
    private function printDocumentDetailsTitle() : void {
        $this->setCurrentSize(10);
        $this->writeCell(0, 12, 'Informacje o dokumnecie', 1, 'C', true);
        $this->Ln(12);
        $this->setCurrentSize($this->font);
    }
    
    private function printDocumentGeneralDetails() : void {
        $width = ($this->w - 2 * $this->margin) / 2;
        $height = 8;
        $this->writeCell($width, $height, 'Numer dokumentu', 1, 'R');
        $this->writeCell($width, $height, $this->number, 1, 'L');
        $this->Ln($height);
        $this->writeCell($width, $height, 'Miejsce', 1, 'R');
        $this->writeCell($width, $height, $this->location, 1, 'L');
        $this->Ln($height);
        $this->writeCell($width / 2, $height, 'Data początkowa', 1, 'C');
        $this->writeCell($width / 2, $height, $this->start, 1, 'C');
        $this->writeCell($width / 2, $height, 'Data końcowa', 1, 'C');
        $this->writeCell($width / 2, $height, $this->end, 1, 'C');
        $this->Ln($height);
    }

    private function printAssignedUsersDetails() : void {
        $this->writeCell(0, 6, 'Przypisane osoby', 1, 'C');
        $this->Ln(6);
        $count = $this->assignedUsers->length();
        $x = $this->GetX();
        $y = $this->GetY();
        $height = 5;
        $this->writeCell(0, ((2 + $count) * $height), '', 1);
        $this->SetXY($x, $y);
        $this->writeCell(0, $height, '');
        $this->Ln($height);
        foreach ($this->assignedUsers->toArray() as $item) {
            $user = new Container($item);
            $fullUsername = $user->get('name') . ' ' . $user->get('surname');
            $this->writeCell(0, $height, $fullUsername, 0, 'C');
            $this->Ln($height);
        }
        $this->writeCell(0, $height, '');
        $this->Ln($height);
    }
    
    private function printDocumentDescription() : void {
        $width = ($this->w - 2 * $this->margin) / 4;
        $height = 36;
        $this->writeCell($width, $height, 'Opis', 1, 'C');
        $cMargin = $this->cMargin;
        $this->cMargin = 30;
        $x = $this->getX();
        $y = $this->GetY();
        $this->writeCell(3 * $width, $height, '', 1);
        $this->SetXY($x, $y + 3);
        $this->writeMulticell(3 * $width, 5, $this->description, 0, 'C');
        $this->cMargin = $cMargin;
        $this->SetXY($this->margin, $y + $height);
    }
    
    private function printGroup(string $title, string $code, Container $container) : void {
        $this->Ln(10);
        $this->setCurrentSize(10);
        if($this->GetY() + 20 >= $this->h - $this->bMargin){
            $this->AddPage();
        }
        $this->writeCell(0, 12, $title, 1, 'C', true);
        $this->Ln(12);
        $this->setCurrentSize($this->font);
        if($container->length() > 0){
            $this->printByCode($code);
        }
        else{
            $this->writeCell(0, 10, 'Brak wpisów', 1, 'C');
            $this->Ln(10);
        }
    }

    private function printByCode(string $code) : void {
        switch ($code){
            case 'entries':
                $this->printUsersEntries();
                break;
            case 'ticket':
                $this->printUsersTickets();
                break;
            case 'article':
                $this->printUsersArticles();
                break;
            case 'decision':
                $this->printUsersDecisions();
                break;
            case 'suspension':
                $this->printUsersSuspensions();
                break;
            case 'usage':
                $this->printUsersUsages();
                break;
            case 'court':
                $this->printUsersCourtApplications();
                break;
        }
    }
    
    private function printUsersEntries() : void {
        $users = $this->getUsernames();
        foreach ($users->toArray() as $username) {
            $this->printUserEntries($username);
        }
    }
    
    private function printUserEntries(string $username) : void {
        $userEntries = $this->filterByUsername($this->entries, $username);
        $marginsTopBottom = 2;
        $height = max(5, $userEntries->length() * 5) + 2 * $marginsTopBottom;
        $this->writeCell(40, $height, $this->getFullUsername($username), 1, 'C');
        $x = $this->GetX();
        $y = $this->GetY();
        $width = $this->w - (2 * $this->margin) - 40;
        $this->writeCell($width, $height, '', 1);
        $this->SetXY($x, $y);
        $this->writeCell($width, $marginsTopBottom, '');
        $this->Ln($marginsTopBottom);
        foreach ($userEntries->toArray() as $index =>$item) {
            $entry = new Container($item);
            $this->SetXY($x, $y + (5 * (int) $index) + $marginsTopBottom);
            $text = '' .  $entry->get('start') . ' - ' . $entry->get('end') . 
                    ' : ' . $entry->get('activity') . ' / ';
            $text .= ($entry->get('underground')) ? 'Podziemne' : 'Powierzchniowe';
            $this->writeCell($width, 5, $text);
        }
        $this->SetXY($this->margin, $y + $height);
    }

    private function printUsersTickets() : void {
        $usernames = $this->getUsernames();
        foreach ($usernames->toArray() as $username) {
            $this->printUserTickets($username);
        }
    }

    private function printUserTickets(string $username) : void {
        $fullUsername = $this->getFullUsername($username);
        $userTickets = $this->filterByUsername($this->tickets, $username);
        $this->SetFillColor(234, 234, 234);
        $this->writeCell(0, 9, $fullUsername, 1, 'C', true);
        $this->Ln(9);
        if($userTickets->length() > 0){
            $this->printUserTicketsTable($userTickets);
        }
        else{
            $this->writeCell(0, 9, 'Brak mandatów', 1, 'C');
            $this->Ln(7);
        }
        $this->SetFillColor(224, 224, 224);
    }
    
    private function printUserTicketsTable(Container $tickets) : void {
        $this->SetFillColor(244, 244, 244);
        foreach ($tickets->toArray() as $i => $item) {
            $ticket = new Container($item);
            $height = $this->calculateTicketHeight($ticket);
            $this->writeCell(7, $height, $i + 1, 1, 'C', true);
            $this->moveLeftMargin(7);
            $this->printUserTicketsTablePartOne($ticket);
            $this->printUserTicketsTablePartTwo($ticket);
            $this->resetLeftMargin();
        }
    }
   
    private function calculateTicketHeight(Container $ticket) : int {
        $widths = array(40, 35, 30, $this->w - $this->margin - $this->currentLeftMargin - 105);
        $texts = array(
            'Numer: ' . $ticket->get('number'),
            'Data: ' . $ticket->get('date'),
            'Kwota: ' . $ticket->get('value') . ' zł',
            'Stanowisko: ' . $ticket->get('position')
        );
        $height = 2 + max($this->calculateHeights($widths, $texts));
        $widthsTwo = array(75, $this->w - $this->margin - $this->currentLeftMargin - 75);
        $textsTwo = array(
            'Podstawa prawna mandatu: ' . $ticket->get('ticket_law'),
            'Naruszone przepisy: ' . $ticket->get('violated_rules')
        );
        $height += 2 + max($this->calculateHeights($widthsTwo, $textsTwo));
        $height += $this->calculateExternalCompanyHeight($ticket);
        $height += $this->calcuateRemarksHeight($ticket);
        return $height;
    }
    
    private function printUserTicketsTablePartOne(Container $ticket) : void {
        $widths = array(40, 35, 30, $this->w - $this->margin - $this->currentLeftMargin - 105);
        $texts = array(
            'Numer: ' . $ticket->get('number'),
            'Data: ' . $ticket->get('date'),
            'Kwota: ' . $ticket->get('value') . ' zł',
            'Stanowisko: ' . $ticket->get('position')
        );
        $this->printMulticellRow($widths, $texts, true);
    }
    
    private function printUserTicketsTablePartTwo(Container $ticket) : void {
        $widths = array(75, $this->w - $this->margin - $this->currentLeftMargin - 75);
        $texts = array(
            'Podstawa prawna mandatu: ' . $ticket->get('ticket_law'),
            'Naruszone przepisy: ' . $ticket->get('violated_rules')
        );
        $this->printMulticellRow($widths , $texts);
        $this->printExternalCompany($ticket);
        $this->printRemarks($ticket);
    }
    
    private function printUsersArticles() : void {
        $usernames = $this->getUsernames();
        foreach ($usernames->toArray() as $username) {
            $this->printUserArticles($username);
        }
    }
    
    private function printUserArticles(string $username) : void {
        $fullUsername = $this->getFullUsername($username);
        $userArticles = $this->filterByUsername($this->articles, $username);
        $this->SetFillColor(234, 234, 234);
        $this->writeCell(0, 9, $fullUsername, 1, 'C', true);
        $this->Ln(9);
        if($userArticles->length() > 0){
            $this->printUserArticlesTable($userArticles);
        }
        else{
            $this->writeCell(0, 9, 'Brak wpisów', 1, 'C');
            $this->Ln(7);
        }
        $this->SetFillColor(224, 224, 224);
    }
    
    private function printUserArticlesTable(Container $articles) : void {
        $this->SetFillColor(244, 244, 244);
        foreach ($articles->toArray() as $i => $item) {
            $article = new Container($item);
            $height = $this->calculateArticleHeight($article);
            $this->writeCell(7, $height, $i + 1, 1, 'C', true);
            $this->moveLeftMargin(7);
            $this->printUserArticlesTablePartOne($article);
            $this->printUserArtclesTablePartTwo($article);
            $this->resetLeftMargin();
        }
    }
    
    private function calculateArticleHeight(Container $article) : int {
        $firstWidths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $firstTexts = array(
            'Data: ' . $article->get('date'), 
            'Forma: ' . $article->get('art_41_form_name')
        );
        $height = 2 + max($this->calculateHeights($firstWidths, $firstTexts));
        $secondWidths = array(75, $this->w - $this->margin - $this->currentLeftMargin - 75);
        $secondTexts = array(
            'Grupa stanowisk: ' . $article->get('position_group'),
            'Stanowisko: ' . $article->get('position')
        );
        $height += 2 + max($this->calculateHeights($secondWidths, $secondTexts));
        if($article->get('applicant') !== ''){
            $thirdWidths = array(50, 75, $this->w - $this->margin - $this->currentLeftMargin - 125);
            $thirdTexts = array(
                'Nr. wniosku: ' . $article->get('application_number'),
                $article->get('applicant'),
                'Data wnoisku: ' . $article->get('application_date')
            );
            $height += 2 + max($this->calculateHeights($thirdWidths, $thirdTexts));
        }
        $height += $this->calculateExternalCompanyHeight($article);
        $height += $this->calcuateRemarksHeight($article);
        return $height;
    }
    
    private function printUserArticlesTablePartOne(Container $article) : void {
        $firstWidths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $firstTexts = array(
            'Data: ' . $article->get('date'), 
            'Forma: ' . $article->get('art_41_form_name')
        );
        $this->printMulticellRow($firstWidths, $firstTexts, true);
        $secondWidths = array(75, $this->w - $this->margin - $this->currentLeftMargin - 75);
        $secondTexts = array(
            'Grupa stanowisk: ' . $article->get('position_group'),
            'Stanowisko: ' . $article->get('position')
        );
        $this->printMulticellRow($secondWidths, $secondTexts);
    }
    
    private function printUserArtclesTablePartTwo(Container $article) : void {
        if($article->get('applicant') !== ''){
            $thirdWidths = array(50, 75, $this->w - $this->margin - $this->currentLeftMargin - 125);
            $thirdTexts = array(
                'Nr. wniosku: ' . $article->get('application_number'),
                $article->get('applicant'),
                'Data wnoisku: ' . $article->get('application_date')
            );
            $this->printMulticellRow($thirdWidths, $thirdTexts);
        }
        $this->printExternalCompany($article);
        $this->printRemarks($article);
    }
    
    private function printUsersDecisions() : void {
        $usernames = $this->getUsernames();
        foreach ($usernames->toArray() as $username) {
            $this->printUserDecisions($username);
        }
    }
    
    private function printUserDecisions(string $username) : void {
        $fullUsername = $this->getFullUsername($username);
        $userDecisions = $this->filterByUsername($this->decisions, $username);
        $this->SetFillColor(234, 234, 234);
        $this->writeCell(0, 9, $fullUsername, 1, 'C', true);
        $this->Ln(9);
        if($userDecisions->length() > 0){
            $this->printUserDecisionsTable($userDecisions);
        }
        else{
            $this->writeCell(0, 9, 'Brak wpisów', 1, 'C');
            $this->Ln(7);
        }
        $this->SetFillColor(224, 224, 224);
    }
    
    private function printUserDecisionsTable(Container $decisions) : void {
        $this->SetFillColor(244, 244, 244);
        foreach ($decisions->toArray() as $i => $item) {
            $decision = new Container($item);
            $height = $this->calculateDecisionHeight($decision);
            $this->writeCell(7, $height, $i + 1, 1, 'C', true);
            $this->moveLeftMargin(7);
            $this->printUserDecision($decision);
            $this->resetLeftMargin();
        }
    }

    private function calculateDecisionHeight(Container $decision) : float {
        $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $texts = array(
            'Data: ' . $decision->get('date'),
            'Podstawa prawna: ' . $decision->get('law')
        );
        $height  = 2 + max($this->calculateHeights($widths , $texts));
        $height += $this->calculateDescriptionHeight($decision);
        $height += $this->calcuateRemarksHeight($decision);
        return $height;
    }
    
    private function printUserDecision(Container $decision) : void {
        $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $texts = array(
            'Data: ' . $decision->get('date'),
            'Podstawa prawna: ' . $decision->get('law')
        );
        $this->printMulticellRow($widths , $texts, true);
        $this->printDescription($decision);
        $this->printRemarks($decision);
    }


    private function printUsersSuspensions() : void {
        $usernames = $this->getUsernames();
        foreach ($usernames->toArray() as $username) {
            $this->printUserSuspensions($username);
        }
    }
    
    private function printUserSuspensions(string $username) : void {
        $fullUsername = $this->getFullUsername($username);
        $userSuspensions = $this->filterByUsername($this->suspensions, $username);
        $this->SetFillColor(234, 234, 234);
        $this->writeCell(0, 9, $fullUsername, 1, 'C', true);
        $this->Ln(9);
        if($userSuspensions->length() > 0){
            $this->printUserSuspensionsTable($userSuspensions);
        }
        else{
            $this->writeCell(0, 9, 'Brak wpisów', 1, 'C');
            $this->Ln(7);
        }
        $this->SetFillColor(224, 224, 224);
    }
    
    private function printUserSuspensionsTable(Container $suspensions) : void {
        $this->SetFillColor(244, 244, 244);
        foreach ($suspensions->toArray() as $i => $item) {
            $suspension = new Container($item);
            $height = $this->calculateSuspensionHeight($suspension);
            $this->writeCell(7, $height, $i + 1, 1, 'C', true);
            $this->moveLeftMargin(7);
            $this->printUserSuspensionTable($suspension);
            $this->printUserSuspensionSanctions($suspension);
            $this->resetLeftMargin();
        }
    }
    
    private function calculateSuspensionHeight(Container $suspension) : float {
        $firstWidths = array(35, 100, $this->w - $this->margin - $this->currentLeftMargin - 135);
        $firstTexts = array(
            'Data: ' . $suspension->get('date'), 
            'Zatrzymanie: ' . $suspension->get('object_name'),
            'Zmiana zatrzymania: ' . $suspension->get('shift')
        );
        $height = 2 + max($this->calculateHeights($firstWidths, $firstTexts));
        $secondWidths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $secondTexts = array('Rejon', $suspension->get('region'));
        $height += 2 + max($this->calculateHeights($secondWidths, $secondTexts));
        $thirdWidths = array(65, $this->w - $this->margin - $this->currentLeftMargin - 65);
        $thirdTexts = array(
            'Data usunięcia nieprawidłowości: ' . $suspension->get('correction_date'), 
            'Zmiana usunięcia nieprawidłowości: ' . $suspension->get('correction_shift')
        );
        $height += 2 + max($this->calculateHeights($thirdWidths, $thirdTexts));
        $height += $this->calculateDescriptionHeight($suspension);
        $height += $this->calcuateRemarksHeight($suspension);
        $tickets = $this->filterBySuspension($this->suspensionsTickets, $suspension);
        if($tickets->length() > 0){
            $height += 7 + (7 * $tickets->length());
        }
        $articles = $this->filterBySuspension($this->suspensionsArticles, $suspension);
        if($articles->length() > 0){
            $height += 7;
            foreach ($articles->toArray() as $item){
                $article = new Container($item);
                $widths = array(35, 75, $this->w - $this->margin - $this->currentLeftMargin - 110);
                $texts = array(
                    'Data: ' . $article->get('art_41_date'),
                    'Forma: ' . $article->get('art_41_form_name'),
                    'Stanowisko: ' . $article->get('art_41_position')
                );
                $height += 2 + max($this->calculateHeights($widths, $texts));
            }
        }
        $decisions = $this->filterBySuspension($this->suspensionsDecisions, $suspension);
        if($decisions->length() > 0){
            $height += 7;
            foreach ($decisions->toArray() as $item){
                $decision = new Container($item);
                $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
                $texts = array(
                    'Data: ' . $decision->get('decision_date'),
                    $decision->get('decision_description')
                );
                $height += 2 + max($this->calculateHeights($widths, $texts));
            }
        }
        return $height;
    }


    private function printUserSuspensionTable(Container $suspension) : void {
        $firstWidths = array(35, 100, $this->w - $this->margin - $this->currentLeftMargin - 135);
        $firstTexts = array(
            'Data: ' . $suspension->get('date'), 
            'Zatrzymanie: ' . $suspension->get('object_name'),
            'Zmiana zatrzymania: ' . $suspension->get('shift')
        );
        $this->printMulticellRow($firstWidths, $firstTexts, true);
        $secondWidths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $secondTexts = array('Rejon', $suspension->get('region'));
        $this->printMulticellRow($secondWidths, $secondTexts);
        $thirdWidths = array(65, $this->w - $this->margin - $this->currentLeftMargin - 65);
        $thirdTexts = array(
            'Data usunięcia nieprawidłowości: ' . $suspension->get('correction_date'), 
            'Zmiana usunięcia nieprawidłowości: ' . $suspension->get('correction_shift')
        );
        $this->printMulticellRow($thirdWidths, $thirdTexts);
        $this->printDescription($suspension);
        $this->printRemarks($suspension);
    }
    
    private function printUserSuspensionSanctions(Container $suspension) : void {
        $this->printUserSuspensionTickets($suspension);
        $this->printUserSuspensionArticles($suspension);
        $this->printUserSuspensionDecisions($suspension);
    }
    
    private function printUserSuspensionTickets(Container $suspension) : void {
        $tickets = $this->filterBySuspension($this->suspensionsTickets, $suspension);
        if($tickets->length() > 0){
            $this->writeCell(0, 7, 'Mandaty', 1, 'C', true);
            $this->Ln(7);
            foreach ($tickets->toArray() as $item){
                $ticket = new Container($item);
                $this->writeCell(35, 7, 'Data: ' . $ticket->get('ticket_date'), 1, 'C');
                $this->writeCell($this->w - $this->margin - $this->currentLeftMargin - 35, 7, 'Nr. ' . $ticket->get('ticket_number'), 1, 'C');
                $this->Ln(7);
            }
        }
    }

    private function printUserSuspensionArticles(Container $suspension) : void {
        $articles = $this->filterBySuspension($this->suspensionsArticles, $suspension);
        if($articles->length() > 0){
            $this->writeCell(0, 7, 'Art. 41', 1, 'C', true);
            $this->Ln(7);
            foreach ($articles->toArray() as $item){
                $article = new Container($item);
                $widths = array(35, 75, $this->w - $this->margin - $this->currentLeftMargin - 110);
                $texts = array(
                    'Data: ' . $article->get('art_41_date'),
                    'Forma: ' . $article->get('art_41_form_name'),
                    'Stanowisko: ' . $article->get('art_41_position')
                );
                $this->printMulticellRow($widths, $texts);
            }
        }
    }

    private function printUserSuspensionDecisions(Container $suspension) : void {
        $decisions = $this->filterBySuspension($this->suspensionsDecisions, $suspension);
        if($decisions->length() > 0){
            $this->writeCell(0, 7, 'Decyzje', 1, 'C', true);
            $this->Ln(7);
            foreach ($decisions->toArray() as $item){
                $decision = new Container($item);
                $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
                $texts = array(
                    'Data: ' . $decision->get('decision_date'),
                    $decision->get('decision_description')
                );
                $this->printMulticellRow($widths, $texts);
            }
        }
    }
    
    private function filterBySuspension(Container $data, Container $suspension) : Container {
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            if( (int) $element->get('id_suspension') === (int) $suspension->get('id')){
                $result->add($item);
            }
        }
        return $result;
    }


    private function printUsersUsages() : void {
        $usernames = $this->getUsernames();
        foreach ($usernames->toArray() as $username) {
            $this->printUserUsages($username);
        }
    }
    
    private function printUserUsages(string $username) : void {
        $fullUsername = $this->getFullUsername($username);
        $userUsages = new Container();
        foreach ($this->usages->toArray() as $item) {
            $container = new Container($item);
            if($container->get('document_assigned_username') === $username){
                $userUsages->add($item);
            }
        }
        $this->SetFillColor(234, 234, 234);
        $this->writeCell(0, 9, $fullUsername, 1, 'C', true);
        $this->Ln(9);
        if($userUsages->length() > 0){
            $this->printUserUsagesTable($userUsages);
        }
        else{
            $this->writeCell(0, 9, 'Brak wpisów', 1, 'C');
            $this->Ln(7);
        }
        $this->SetFillColor(224, 224, 224);
    }
    
    private function printUserUsagesTable(Container $usages) : void {
        $this->SetFillColor(244, 244, 244);
        foreach ($usages->toArray() as $i => $item) {
            $usage = new Container($item);
            $height = $this->calculateUsageHeight($usage);
            $this->writeCell(7, $height, $i + 1, 1, 'C', true);
            $this->moveLeftMargin(7);
            $this->printUserUsage($usage);
            $this->resetLeftMargin();
        }
    }
    
    private function calculateUsageHeight(Container $usage) : float {
        $widths = array(35, 100, $this->w - $this->margin - $this->currentLeftMargin - 135);
        $texts = array(
            'Data: ' . $usage->get('date'),
            'Przyrząd: ' . $usage->get('equipment_name'),
            'Nr. ' . $usage->get('inventory_number')
        );
        $height = 2 + max($this->calculateHeights($widths, $texts));
        if($usage->get('recommendation_decision')){
            $height += 5;
        }
        $height += $this->calcuateRemarksHeight($usage);
        return $height;
    }

    private function printUserUsage(Container $usage) : void {
        $widths = array(35, 100, $this->w - $this->margin - $this->currentLeftMargin - 135);
        $texts = array(
            'Data: ' . $usage->get('date'),
            'Przyrząd: ' . $usage->get('equipment_name'),
            'Nr. ' . $usage->get('inventory_number')
        );
        $this->printMulticellRow($widths, $texts, true);
        if($usage->get('recommendation_decision')){
            $this->writeCell(0, 5, 'Wydano decyzję / zalecenie', 1, 'C');
            $this->Ln(5);
        }
        $this->printRemarks($usage);
    }
    
    private function printUsersCourtApplications() : void {
        $usernames = $this->getUsernames();
        foreach ($usernames->toArray() as $username) {
            $this->printUserCourtApplications($username);
        }
    }
    
    private function printUserCourtApplications(string $username) : void {
        $fullUsername = $this->getFullUsername($username);
        $userCourtApplications = $this->filterByUsername($this->courts, $username);
        $this->SetFillColor(234, 234, 234);
        $this->writeCell(0, 9, $fullUsername, 1, 'C', true);
        $this->Ln(9);
        if($userCourtApplications->length() > 0){
            $this->printUserCourtApplicationsTable($userCourtApplications);
        }
        else{
            $this->writeCell(0, 9, 'Brak wpisów', 1, 'C');
            $this->Ln(7);
        }
        $this->SetFillColor(224, 224, 224);
    }
    
    private function printUserCourtApplicationsTable(Container $applications) : void {
        $this->SetFillColor(244, 244, 244);
        foreach ($applications->toArray() as $i => $item) {
            $application = new Container($item);
            $height = $this->calculateCourtApplicationHeight($application);
            $this->writeCell(7, $height, $i + 1, 1, 'C', true);
            $this->moveLeftMargin(7);
            $this->printUserCourtApplication($application);
            $this->resetLeftMargin();
        }
    }
    
    private function calculateCourtApplicationHeight(Container $application) : float {
        $firstWidths = array(35, 110, $this->w - $this->margin - $this->currentLeftMargin - 145);
        $firstTexts = array(
            'Data: ' . $application->get('date'), 
            'Stanowisko ukaranego: ' . $application->get('position'),
            'Wysokość kary: ' . $application->get('value') . ' zł.'
        );
        $height = 2 + max($this->calculateHeights($firstWidths, $firstTexts));
        $secondWidths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $secondTexts = array('Treść oskarżenia', $application->get('accusation'));
        $height += 2 + max($this->calculateHeights($secondWidths, $secondTexts));
        $height += $this->calculateExternalCompanyHeight($application);
        $height += $this->calcuateRemarksHeight($application);
        return $height;
    }
    
    private function printUserCourtApplication(Container $application) : void {
        $firstWidths = array(35, 110, $this->w - $this->margin - $this->currentLeftMargin - 145);
        $firstTexts = array(
            'Data: ' . $application->get('date'), 
            'Stanowisko ukaranego: ' . $application->get('position'),
            'Wysokość kary: ' . $application->get('value') . ' zł.'
        );
        $this->printMulticellRow($firstWidths, $firstTexts, true);
        $secondWidths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $secondTexts = array('Treść oskarżenia', $application->get('accusation'));
        $this->printMulticellRow($secondWidths, $secondTexts);
        $this->printExternalCompany($application);
        $this->printRemarks($application);
    }
    
    private function printMulticellRow(array $widths, array $texts, bool $fill = false) : void {
        $heights = $this->calculateHeights($widths, $texts);
        $totalHeight = 2 + max($heights);
        $this->checkPageBreak($totalHeight);
        $startX = $this->GetX();
        $y = $this->GetY();
        for ($i = 0; $i < count($widths) && $i < count($texts); $i++) {
            $x = $this->GetX();
            $width = $widths[$i];
            $height = $heights[$i];
            $text = $texts[$i];
            if($fill){
                $this->Rect($x,$y,$width,$totalHeight, 'F');
            }
            $this->Rect($x,$y,$width,$totalHeight, 'D');
            $pushDown = ($totalHeight - $height) / 2;
            $this->SetXY($x, $y + $pushDown);
            $this->writeMulticell($width, 5, $text, 0, 'C');
            $this->SetXY($x + $width, $y);
        }
        $this->SetXY($startX, $y);
        $this->Ln($totalHeight);
    }
    
    private function calculateHeights(array $widths, array $texts) : array {
        $heights = array();
        for ($i = 0; $i < count($widths) && $i < count($texts); $i++) {
            $width = $widths[$i];
            $text = $texts[$i];
            $h = 5 * $this->NbLines($width, $text);
            $heights[$i] = $h;
        }
        return $heights;
    }
    
    private function printExternalCompany(Container $item) : void {
        if($item->get('external_company')) {
            $this->writeMulticell(0, 6, 'Ukarany pracownik firmy: ' . $item->get('company_name'), 1, 'C');
        }
    }

    private function calculateExternalCompanyHeight(Container $item) : float {
        if($item->get('external_company')) {
            return 6 * $this->NbLines($this->w - $this->margin - $this->currentLeftMargin, 'Ukarany pracownik firmy: ' . $item->get('company_name'));
        }
        return 0;
    }
    
    private function printRemarks(Container $item) : void {
        $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $texts = array('Uwagi ', $item->get('remarks'));
        if($item->get('remarks') !== '') {
            $this->printMulticellRow($widths, $texts);
        }
    }
    
    private function calcuateRemarksHeight(Container $item) : float {
        if($item->get('remarks') !== '') {
            $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 7 - 35);
            $texts = array('Uwagi ', $item->get('remarks'));
            return 2 + max($this->calculateHeights($widths, $texts));
        }
        return 0;
    }
    
    private function printDescription(Container $item) : void {
        $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 35);
        $texts = array('Opis ', $item->get('description'));
        if($item->get('description') !== '') {
            $this->printMulticellRow($widths, $texts);
        }
    }
    
    private function calculateDescriptionHeight(Container $item) : float {
        if($item->get('description') !== '') {
            $widths = array(35, $this->w - $this->margin - $this->currentLeftMargin - 7 - 35);
            $texts = array('Opis ', $item->get('description'));
            return 2 + max($this->calculateHeights($widths, $texts));
        }
        return 0;
    }
    
    private function getUsernames() : Container {
        $usernames = new Container();
        foreach ($this->assignedUsers->toArray() as $item) {
            $entry = new Container($item);
            $usernames->add($entry->get('username'));
        }
        return $usernames;
    }
    
    private function getFullUsername(string $username) : string {
        foreach ($this->assignedUsers->toArray() as $item) {
            $user = new Container($item);
            if($user->get('username') === $username){
                return $user->get('name') . ' ' . $user->get('surname');
            }
        }
        return '';
    }
    
    private function filterByUsername(Container $data, string $username) : Container {
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $container = new Container($item);
            if($container->get('username') === $username){
                $result->add($item);
            }
        }
        return $result;
    }
    
    function footer() {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $appname = $cfg->get('name');
        $y = $this->h - 5;
        $fontSize = $this->getCurrentFontSize();
        $this->setCurrentSize(8);
        $this->SetY($y);
        $this->writeCell(0, 5, "Plik wygenerowany przez system " . $appname . ", strona " . $this->PageNo() . "/{nb}", 0, 'R');
        $this->setCurrentSize($fontSize);
    }
    
    private function moveLeftMargin(float $value) : void {
        $this->currentLeftMargin = $this->margin + $value;
        $this->setMargin('left', $this->currentLeftMargin);
    }
    
    private function resetLeftMargin() : void {
        $this->currentLeftMargin = $this->margin;
        $this->setMargin('left', $this->currentLeftMargin);
        $this->SetX($this->currentLeftMargin);
    }
}
