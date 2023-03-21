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
use Data\Access\Views\LocationDetailsView as LocationDetailsView;
use Tanweb\Container as Container;

/**
 * Description of LocationInput
 *
 * @author Tanzar
 */
class LocationInput extends Input {
    
    public function __construct(int $id) {
        parent::__construct(Inputs::Location, $id);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $view = new LocationDetailsView();
        $locations = $view->getAll();
        $result = new Container();
        foreach ($locations->toArray() as $item) {
            $location = new Container($item);
            $id = (int) $location->get('id');
            $option = array(
                'value' => $id,
                'title' => $location->get('name')
            );
            $result->add($option);
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        $sql->where('id_location', $value);
    }
}
