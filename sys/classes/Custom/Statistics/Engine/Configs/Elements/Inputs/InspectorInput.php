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
use Data\Access\Views\SuzugUserDetailsView as SuzugUserDetailsView;
use Tanweb\Container as Container;

/**
 * Description of InspectorInput
 *
 * @author Tanzar
 */
class InspectorInput extends Input {
    
    public function __construct(string $username) {
        parent::__construct(Inputs::Inspector, $username);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $view = new SuzugUserDetailsView();
        $users = $view->getActiveOrderedByNumber();
        $addedUsers = new Container();
        $result = new Container();
        foreach ($users->toArray() as $item) {
            $user = new Container($item);
            $id = (int) $user->get('id_user');
            if(!$addedUsers->contains($id)){
                $addedUsers->add($id);
                $option = array(
                    'value' => $user->get('username'),
                    'title' => $user->get('surname') . ' ' . $user->get('name')
                );
                $result->add($option);
            }
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        $sql->where('username', $value);
    }

}
