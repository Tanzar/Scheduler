<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Data\Access\Tables\LocationTypeDAO as LocationTypeDAO;
use Tanweb\Container as Container;

/**
 * Description of LocationTypeGroup
 *
 * @author Tanzar
 */
class LocationTypeGroup extends Group{
    private Container $locationsTypes;
    
    public function __construct(int $value) {
        parent::__construct(Groups::LocationType, $value);
        $dao = new LocationTypeDAO();
        $this->locationsTypes = $dao->getAll();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        foreach($this->locationsTypes->toArray() as $item){
            $locationType = new Container($item);
            $id = (int) $locationType->get('id');
            $results->add(array(
                'title' => $locationType->get('name'),
                'value' => $id
            ));
        }
        return $results;
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('id_location_type')){
            $this->value = $row->get('id_location_type');
        }
    }

    public function getValueVariableName(): string {
        return 'id)location_type';
    }

}
