<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Data\Access\Tables\ActivityTableDAO as ActivityTableDAO;
use Tanweb\Container as Container;

/**
 * Description of ActivityGroup
 *
 * @author Tanzar
 */
class ActivityGroup extends Group{
    private Container $activities;
    
    public function __construct(int $value) {
        parent::__construct(Groups::Activity, $value);
        $dao = new ActivityTableDAO();
        $this->activities = $dao->getAll();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        foreach($this->activities->toArray() as $item){
            $activity = new Container($item);
            $id = (int) $activity->get('id');
            $results->add(array(
                'title' => $activity->get('name'),
                'value' => $id
            ));
        }
        return $results;
    }
    
    public function setValue(Container $row) : void {
        if($row->isValueSet('id_activity')){
            $this->value = $row->get('id_activity');
        }
    }

    public function getValueVariableName(): string {
        return 'id_activity';
    }

}
