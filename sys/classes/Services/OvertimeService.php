<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\OvertimeReductionDAO as OvertimeReductionDAO;
use Data\Access\Views\OvertimeReductionDetailsView as OvertimeReductionDetailsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Custom\Parsers\Database\OvertimeReduction as OvertimeReduction;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;

/**
 * Description of OvertimeService
 *
 * @author Tanzar
 */
class OvertimeService {
    private OvertimeReductionDAO $overtimeReduction;
    private OvertimeReductionDetailsView $overtimeReductionDetails;
    private UsersWithoutPasswordsView $users;
    
    public function __construct() {
        $this->overtimeReduction = new OvertimeReductionDAO();
        $this->overtimeReductionDetails = new OvertimeReductionDetailsView();
        $this->users = new UsersWithoutPasswordsView();
    }
    
    public function getAll() : Container {
        return $this->overtimeReductionDetails->getAll();
    }
    
    public function getOptions() : Container {
        $result = new Container();
        $users = $this->users->getActive();
        $result->add($users->toArray(), 'users');
        $appConfig = AppConfig::getInstance();
        $cfg = $appConfig->getAppConfig();
        $year = $cfg->get('yearStart');
        $result->add($year, 'year');
        $language = Languages::getInstance();
        $months = $language->get('months');
        $result->add($months, 'months');
        return $result;
    }
    
    public function save(Container $data) : int {
        $parser = new OvertimeReduction();
        $date = $data->get('year') . '-' . $data->get('month') . '-01';
        $data->add($date, 'date', true);
        $item = $parser->parse($data);
        return $this->overtimeReduction->save($item);
    }
    
    public function changeStatus(int $id) : void {
        $reduction = $this->overtimeReduction->getById($id);
        $active = $reduction->get('active');
        if($active){
            $this->overtimeReduction->disable($id);
        }
        else{
            $this->overtimeReduction->enable($id);
        }
    }
}
