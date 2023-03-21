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
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Tanweb\Container as Container;

/**
 * Description of UserInput
 *
 * @author Tanzar
 */
class UserInput extends Input{
    
    public function __construct(string $username) {
        parent::__construct(Inputs::User, $username);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $view = new UsersWithoutPasswordsView();
        $users = $view->getAllWithoutSystem();
        $result = new Container();
        foreach ($users->toArray() as $item) {
            $user = new Container($item);
            $option = array(
                'value' => $user->get('username'),
                'title' => $user->get('surname') . ' ' . $user->get('name')
            );
            $result->add($option);
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        $sql->where('username', $value);
    }

}
