<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\NightShiftReportNumberDAO as NightShiftReportNumberDAO;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use Services\Exceptions\YearNotUniqueException as YearNotUniqueException;

/**
 * Description of NightShiftReportService
 *
 * @author Tanzar
 */
class NightShiftReportService {
    private NightShiftReportNumberDAO $nightShiftReportNumberDAO;
    
    public function __construct() {
        $this->nightShiftReportNumberDAO = new NightShiftReportNumberDAO();
    }
    
    public function getReportNumbers() : Container {
        return $this->nightShiftReportNumberDAO->getAll();
    }
    
    public function getStartYear() : int {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        return (int) $cfg->get('yearStart');
    }
    
    public function saveReportNumber(Container $data) : int {
        $year = (int) $data->get('year');
        $entry = $this->nightShiftReportNumberDAO->getByYear($year);
        $id = 0;
        if($data->isValueSet('id')){
            $id = (int) $data->get('id');
        }
        if($entry->length() > 0){
            if($id !== (int) $entry->get('id')){
                throw new YearNotUniqueException();
            }
        }
        return $this->nightShiftReportNumberDAO->save($data);
    }
    
    public function changeReportNumberStatus(int $id) : void {
        $item = $this->nightShiftReportNumberDAO->getById($id);
        $active = $item->get('active');
        if($active) {
            $this->nightShiftReportNumberDAO->disable($id);
        }
        else{
            $this->nightShiftReportNumberDAO->enable($id);
        }
    }
}
