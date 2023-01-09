<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\DBFixer\Fixers;

use Data\Access\Tables\DecisionDAO as DecisionDAO;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Tables\SuspensionDecisionDAO as SuspensionDecisionDAO;
use Tanweb\Container as Container;
use Custom\DBFixer\FixerReport as FixerReport;

/**
 * Description of DecisionsFixer
 *
 * @author Tanzar
 */
class DecisionsFixer {
    
    public static function run(FixerReport $report) : void {
        $idsToRemove = self::getAllDecisionsWithoutSuspension();
        $dao = new DecisionDAO();
        foreach ($idsToRemove->toArray() as $id) {
            $dao->remove((int) $id);
            $report->addRemoved();
        }
    }
    
    private static function getAllDecisionsWithoutSuspension() : Container {
        $decisionsView = new DecisionDetailsView();
        $decisions = $decisionsView->getAllRequiringSuspension();
        $suspensionDecisionDAO = new SuspensionDecisionDAO();
        $suspensions = $suspensionDecisionDAO->getAll();
        $result = new Container();
        foreach ($decisions->toArray() as $item) {
            $decision = new Container($item);
            $id = $decision->get('id');
            $notFound = true;
            foreach ($suspensions->toArray() as $item) {
                $suspension = new Container($item);
                if($suspension->get('id_decision') === $id){
                    $notFound = false;
                }
            }
            if($notFound){
                $result->add($id);
            }
        }
        return $result;
    }
}
