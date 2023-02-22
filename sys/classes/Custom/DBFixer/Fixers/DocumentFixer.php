<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\DBFixer\Fixers;

use Custom\DBFixer\FixerReport as FixerReport;
use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Views\DocumentEntriesDetailsView as DocumentEntriesDetailsView;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\ArticleDetailsView as ArticleDetailsView;
use Data\Access\Views\SuspensionDetailsView as SuspensionDetailsView;
use Data\Access\Views\CourtApplicationDetailsView as CourtApplicationDetailsView;
use Data\Access\Views\InstrumentUsageDetailsView as InstrumentUsageDetailsView;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of DocumentFixer
 *
 * @author Tanzar
 */
class DocumentFixer {
    
    public static function run(FixerReport $report) : void {
        $dao = new DocumentDAO();
        $documents = $dao->getAll();
        $entries = self::getEntries();
        $tickets = self::getTickets();
        $decisions = self::getDecisions();
        $articles = self::getArticles();
        $suspensions = self::getSuspensions();
        $courtApplications = self::getCourtApplications();
        $usages = self::getInstrumentUsages();
        foreach ($documents->toArray() as $item) {
            $document = new Container($item);
            $start = $document->get('start');
            $end = $document->get('end');
            self::fixDocumentDatesFromEntries($document, $entries);
            self::fixDocumentDates($document, $tickets);
            self::fixDocumentDates($document, $decisions);
            self::fixDocumentDates($document, $articles);
            self::fixDocumentDates($document, $suspensions);
            self::fixDocumentDates($document, $courtApplications);
            self::fixDocumentDates($document, $usages);
            if($document->get('start') !== $start || $document->get('end') !== $end){
                $dao->save($document);
                $report->addDatesChanged();
            }
        }
    }
    
    private static function getEntries() : Container {
        $view = new DocumentEntriesDetailsView();
        return $view->getAll();
    }
    
    private static function getTickets() : Container {
        $view = new TicketDetailsView();
        return $view->getAll();
    }
    
    private static function getDecisions() : Container {
        $view = new DecisionDetailsView();
        return $view->getAll();
    }
    
    private static function getArticles() : Container {
        $view = new ArticleDetailsView();
        return $view->getAll();
    }
    
    private static function getSuspensions() : Container {
        $view = new SuspensionDetailsView();
        return $view->getAll();
    }
    
    private static function getCourtApplications() : Container {
        $view = new CourtApplicationDetailsView();
        return $view->getAll();
    }
    
    private static function getInstrumentUsages() : Container {
        $view = new InstrumentUsageDetailsView();
        return $view->getAll();
    }
    
    private static function fixDocumentDatesFromEntries(Container $document, Container $entries) : void {
        foreach ($entries->toArray() as $item) {
            $entry = new Container($item);
            if((int) $entry->get('id_document') === $document->get('id')){
                $entryStart = new DateTime($entry->get('start'));
                $entryEnd = new DateTime($entry->get('end'));
                $start = new DateTime($document->get('start') . ' 00:00:00');
                $end = new DateTime($document->get('end') . ' 23:59:59');
                $document->add(min($start, $entryStart)->format('Y-m-d'), 'start', true);
                $document->add(max($end, $entryEnd)->format('Y-m-d'), 'end', true);
            }
        }
    }
    
    private static function fixDocumentDates(Container $document, Container $sanctions) : void {
        foreach ($sanctions->toArray() as $item) {
            $sanction = new Container($item);
            if((int) $sanction->get('id_document') === $document->get('id')){
                $date = new DateTime($sanction->get('date'));
                $start = new DateTime($document->get('start'));
                $end = new DateTime($document->get('end'));
                $document->add(min($start, $date)->format('Y-m-d'), 'start', true);
                $document->add(max($end, $date)->format('Y-m-d'), 'end', true);
            }
        }
    }
}
