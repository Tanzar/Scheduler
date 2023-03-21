<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Statistics\Engine\Configs\Elements\Datasets;

use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\Operations as Operations;

/**
 *
 * @author Tanzar
 */
enum DataSources : string {
    case Entries = 'Wpisy na harmonogramie';
    case Inspections = 'Wpisy na kontrolach';
    case Tickets = 'Mandaty';
    case Articles = 'Art 41';
    case Decisions = 'Decyzje';
    case Suspensions = 'Zatrzymania';
    case SuspensionsWithDecisions = 'Zatrzymania z decyzjami';
    case SuspensionsWithoutDecisions = 'Zatrzymania bez decyzji';
    case SuspensionsTickets = 'Mandaty dla zatrzymań';
    case SuspensionsArticles = 'Art 41 dla zatrzymań';
    case SuspensionsDecisions = 'Decyzje dla zatrzymań';
    case InstrumentUsages = 'Wykorzystania przyrządów';
    case CourtApplications = 'Wnioski do sądu';
    
    public function getViewName() : string {
        return match($this) {
            DataSources::Entries => 'statistics_schedule_entries',
            DataSources::Inspections => 'statistics_document_entries',
            DataSources::Tickets => 'suzug_ticket',
            DataSources::Articles => 'suzug_art_41',
            DataSources::Decisions => 'suzug_decision',
            DataSources::Suspensions => 'suzug_suspension',
            DataSources::SuspensionsTickets => 'suzug_suspension_ticket',
            DataSources::SuspensionsArticles => 'suzug_suspension_art_41',
            DataSources::SuspensionsWithDecisions => 'suzug_suspension_with_decision',
            DataSources::SuspensionsWithoutDecisions => 'suzug_suspension_without_decision',
            DataSources::SuspensionsDecisions => 'suzug_suspension_decision',
            DataSources::InstrumentUsages => 'suzug_instrument_usage',
            DataSources::CourtApplications => 'suzug_court_application'
        };
    }
    
    public function getOpeartions() : array {
        return match($this) {
            DataSources::Entries => $this->formOperationsWorkdays(),
            DataSources::Inspections => $this->formOperationsWorkdays(),
            DataSources::Tickets => $this->formOperations(Operations::Sum),
            DataSources::Articles => $this->formOperations(),
            DataSources::Decisions => $this->formOperations(),
            DataSources::Suspensions => $this->formOperations(),
            DataSources::SuspensionsTickets => $this->formOperations(),
            DataSources::SuspensionsArticles => $this->formOperations(),
            DataSources::SuspensionsWithDecisions => $this->formOperations(),
            DataSources::SuspensionsWithoutDecisions => $this->formOperations(),
            DataSources::SuspensionsDecisions => $this->formOperations(),
            DataSources::InstrumentUsages => $this->formOperations(),
            DataSources::CourtApplications => $this->formOperations(Operations::Sum)
        };
    }
    
    private function formOperationsWorkdays(){
        return $this->formOperations(Operations::CountWorkdays, Operations::CountShiftA, 
                Operations::CountShiftB, Operations::CountShiftC, Operations::CountShiftD);
    }
    
    private function formOperations(Operations ... $operations) : array {
        $result = array(Operations::Count);
        foreach ($operations as $item) {
            $result[] = $item;
        }
        return $result;
    }
}
