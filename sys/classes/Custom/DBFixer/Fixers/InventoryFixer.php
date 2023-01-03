<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\DBFixer\Fixers;

use Tanweb\Container as Container;
use Custom\DBFixer\FixerReport as FixerReport;
use Data\Access\Tables\EquipmentDAO as EquipmentDAO;

/**
 * Description of InventoryFixer
 *
 * @author Tanzar
 */
class InventoryFixer {
    
    public static function run(FixerReport $report) : void {
        $dao = new EquipmentDAO();
        $equipments = $dao->getAll();
        foreach ($equipments->toArray() as $item){
            $equipment = new Container($item);
            $active = $equipment->get('active');
            $state = $equipment->get('state');
            if($active && $state === 'liquidation'){
                $equipment->add(0, 'active', true);
                $report->addDisabled();
                $dao->save($equipment);
            }
            elseif (!$active && $state !== 'liquidation'){
                $equipment->add(1, 'active', true);
                $report->addEnabled();
                $dao->save($equipment);
            }
        }
    }
}
