<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Database\DataFilter\DataFilter as DataFilter;
use Tanweb\Database\Database as Database;

/**
 *
 * @author Tanzar
 */
enum DataSet : string {
    case Entries = 'Wpisy harmonogramu';
    case Inspections = 'Wpisy na kontrolach';
    case Tickets = 'Mandaty';
    case Articles = 'Art 41';
    case Decisions = 'Decyzje';
    case Suspensions = 'Zatrzymania';
    case SuspensionsTickets = 'Mandaty dla zatrzymań';
    case SuspensionsArticles = 'Art 41 dla zatrzymań';
    case SuspensionsDecisions = 'Decyzje dla zatrzymań';
    case InstrumentUsages = 'Wykorzystania przyrządów';
    case CourtApplications = 'Wnioski do sądu';
    
    public function getData(DataFilter $filter = null) : Container {
        $database = Database::getInstance('scheduler');
        if($filter === null){
            $sql = $this->getSQL();
        }
        else{
            $sql = $filter->generateSQL();
        }
        return $database->select($sql);
    }
    
    private function getSQL() : MysqlBuilder {
        $view = $this->getViewName();
        $sql = new MysqlBuilder();
        $sql->select($view)->where('active', 1);
        return $sql;
    }
    
    public function getViewName() : string {
        return match($this) {
            Dataset::Entries => 'statistics_schedule_entries',
            Dataset::Inspections => 'document_entries_details',
            Dataset::Tickets => 'suzug_ticket',
            Dataset::Articles => 'suzug_art_41',
            Dataset::Decisions => 'suzug_decision',
            Dataset::Suspensions => 'suzug_suspension',
            Dataset::SuspensionsTickets => 'suzug_suspension_ticket',
            Dataset::SuspensionsArticles => 'suzug_suspension_art_41',
            Dataset::SuspensionsDecisions => 'suzug_suspension_decision',
            Dataset::InstrumentUsages => 'suzug_instrument_usage',
            Dataset::CourtApplications => 'suzug_court_application'
        };
    }
}
