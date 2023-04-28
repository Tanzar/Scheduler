<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\TicketService as TicketService;
use Services\ArticleService as ArticleService;
use Services\DecisionService as DecisionService;
use Services\SuspensionService as SuspensionService;
use Services\InstrumentUsageService as InstrumentUsageService;
use Services\CourtApplicationService as CourtApplicationsService;

/**
 * Description of StatsSanctions
 *
 * @author Tanzar
 */
class StatsSanctions extends Controller{
    private TicketService $tickets;
    private ArticleService $articles;
    private DecisionService $decisions;
    private SuspensionService $suspensions;
    private InstrumentUsageService $usages;
    private CourtApplicationsService $courtApplications;
    
    public function __construct() {
        $this->tickets = new TicketService();
        $this->articles = new ArticleService();
        $this->decisions = new DecisionService();
        $this->suspensions = new SuspensionService();
        $this->usages = new InstrumentUsageService();
        $this->courtApplications = new CourtApplicationsService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('stats_admin');
        $privilages->add('stats_user');
        parent::__construct($privilages);
    }
    
    public function getTickets() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->tickets->getActiveTicketsByMonthAndYear($month, $year);
        $this->addRowNumbers($response);
        $this->setResponse($response);
    }
    
    public function getArticles() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->articles->getActiveArticlesByMonthAndYear($month, $year);
        $this->addRowNumbers($response);
        $this->setResponse($response);
    }
    
    public function getDecisions() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->decisions->getActiveDecisionsByMonthAndYear($month, $year);
        $this->addRowNumbers($response);
        $this->setResponse($response);
    }
    
    public function getSuspensions() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->suspensions->getActiveSuspensionsByMonthAndYearWithDecisionInfo($month, $year);
        $this->addRowNumbers($response);
        $this->setResponse($response);
    }
    
    public function getUsages() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->usages->getActiveInstrumentUsagesByMonthAndYear($month, $year);
        $this->addRowNumbers($response);
        $this->setResponse($response);
    }
    
    public function getCourtApplications() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->courtApplications->getActiveApplicationsByMonthAndYear($month, $year);
        $this->addRowNumbers($response);
        $this->setResponse($response);
    }
    
    private function addRowNumbers(Container $response) : void {
        foreach ($response->toArray() as $index => $item) {
            $item['LP'] = (int) $index + 1;
            $response->add($item, $index, true);
        }
    }
}
