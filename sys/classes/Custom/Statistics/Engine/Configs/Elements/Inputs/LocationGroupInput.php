<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs;

use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\InputFormHTML as InputFormHTML;
use Custom\Statistics\Engine\Configs\Elements\Datasets\DataSources as DataSources;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use Tanweb\Container as Container;

/**
 * Description of LocationGroupInput
 *
 * @author Tanzar
 */
class LocationGroupInput extends Input {
    
    public function __construct(int $id) {
        parent::__construct(Inputs::LocationGroup, $id);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $dao = new LocationGroupDAO();
        $groups = $dao->getAll();
        $result = new Container();
        foreach ($groups->toArray() as $item) {
            $group = new Container($item);
            $option = array(
                'value' => (int) $group->get('id'),
                'title' => $group->get('name')
            );
            $result->add($option);
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        $sql->where('id_location_group', $value);
    }
}
