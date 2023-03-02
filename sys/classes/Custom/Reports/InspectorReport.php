<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Reports;

use Data\Access\Views\ArticleDetailsView as ArticleDetailsView;
use Data\Access\Views\CourtApplicationDetailsView as CourtApplicationDetailsView;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\InstrumentUsageDetailsView as InstrumentUsageDetailsView;
use Data\Access\Views\SuspensionDetailsView as SuspensionDetailsView;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use DateTime;
/**
 * Description of InspectorReport
 *
 * @author Tanzar
 */
class InspectorReport {
    
    public static function generate(int $year, string $username) : Container {
        $result = new Container();
        $languages = Languages::getInstance();
        $modules = new Container($languages->get('modules'));
        $title = $modules->get('inspector');
        $result->add($title, 'title');
        $cells = self::formCells($year, $username);
        $result->add($cells, 'cells');
        return $result;
    }
    
    private static function formCells(int $year, string $username) : array {
        $cells = array();
        $languages = Languages::getInstance();
        $months = $languages->get('months');
        $inspector = new Container($languages->get('inspector'));
        $cells[] = self::formHeaders($months);
        $cells[] = self::formArticleRow($inspector, $year, $username);
        $cells[] = self::formCourtRow($inspector, $year, $username);
        $cells[] = self::formDecisionRow($inspector, $year, $username);
        $cells[] = self::formUsageRow($inspector, $year, $username);
        $cells[] = self::formSuspensionRow($inspector, $year, $username);
        $cells[] = self::formTicketRow($inspector, $year, $username);
        return $cells;
    }
    
    private static function formHeaders(array $months) : array {
        $cells = array('');
        foreach ($months as $value) {
            $cells[] = $value;
        }
        return $cells;
    }
    
    private static function formArticleRow(Container $inspector, int $year, string $username) : array {
        $view = new ArticleDetailsView();
        $data = $view->getActiveArticlesByUserAndYear($username, $year);
        $title = $inspector->get('article_41');
        return self::formDataRow($title, $data);
    }
    
    private static function formCourtRow(Container $inspector, int $year, string $username) : array {
        $view = new CourtApplicationDetailsView();
        $data = $view->getActiveByUsernameAndYear($username, $year);
        $title = $inspector->get('court_applications');
        return self::formDataRow($title, $data);
    }
    
    private static function formDecisionRow(Container $inspector, int $year, string $username) : array {
        $view = new DecisionDetailsView();
        $data = $view->getActiveByUsernameAndYear($username, $year);
        $title = $inspector->get('decisions');
        return self::formDataRow($title, $data);
    }
    
    private static function formUsageRow(Container $inspector, int $year, string $username) : array {
        $view = new InstrumentUsageDetailsView();
        $data = $view->getActiveByUsernameAndYear($username, $year);
        $title = $inspector->get('instruments');
        return self::formDataRow($title, $data);
    }
    
    private static function formSuspensionRow(Container $inspector, int $year, string $username) : array {
        $view = new SuspensionDetailsView();
        $data = $view->getActiveByUsernameAndYear($username, $year);
        $title = $inspector->get('suspensions');
        return self::formDataRow($title, $data);
    }
    
    private static function formTicketRow(Container $inspector, int $year, string $username) : array {
        $view = new TicketDetailsView();
        $data = $view->getActiveUserTicketsByYear($username, $year);
        $title = $inspector->get('tickets');
        return self::formDataRow($title, $data);
    }
    
    private static function formDataRow(string $title, Container $data) : array {
        $cells = array($title, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        foreach ($data->toArray() as $item) {
            $date = new DateTime($item['date']);
            $month = (int) $date->format('m');
            $cells[$month]++;
        }
        return $cells;
    }
}
