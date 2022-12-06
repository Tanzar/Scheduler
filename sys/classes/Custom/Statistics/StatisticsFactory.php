<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics;

use Data\Access\Tables\StatisticsDAO as StatisticsDAO;
use Custom\Statistics\Options\Type as Type;
use Custom\Statistics\Statistics as Statistics;
use Custom\Statistics\SingleStats as SingleStats;
use Tanweb\Container as Container;
use Custom\Statistics\Exceptions\UnsupportedStatisticsTypeException as UnsupportedStatisticsTypeException;

/**
 * Description of StatisticsFactory
 *
 * @author Tanzar
 */
class StatisticsFactory {
    
    public static function build(Container $data) : Statistics {
        $id = (int) $data->get('id');
        $data->remove('id');
        $dao = new StatisticsDAO();
        $statistic = $dao->getById($id);
        $type = Type::from($statistic->get('type'));
        switch ($type) {
            case Type::Single:
                return new SingleStats($statistic, $data);
            case Type::Monthly:
                break;
            case Type::Yearly:
                break;
            case Type::Form:
                break;
        }
        throw new UnsupportedStatisticsTypeException($type);
    }
    
    
    
}
