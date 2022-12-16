<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

use Tanweb\Container as Container;
use Custom\Statistics\Options\DataSet as DataSet;
use Custom\Statistics\Calculation\Calculator as Calculator;
use Custom\Statistics\Calculation\ResultSet as ResultSet;
use Custom\Statistics\Options\Shift as Shift;
/**
 *
 * @author Tanzar
 */
enum Method : string {
    case Sum = 'Suma';
    case Count = 'Zliczanie';
    case CountWorkdays = 'Zliczanie roboczodniówek';
    case CountNightShifts = 'Zliczanie nocnej zmian';
    case CountShiftA = 'Zliczanie zmiany A';
    case CountShiftB = 'Zliczanie zmiany B';
    case CountShiftC = 'Zliczanie zmiany C';
    case CountShiftD = 'Zliczanie zmiany D';
    
    public static function getMethodsForDataSet(DataSet $dataset) : Container {
        return match ($dataset){
            DataSet::Articles => self::formContainer(),
            DataSet::CourtApplications => self::formContainer(Method::Sum),
            DataSet::Decisions => self::formContainer(),
            DataSet::Entries => self::formContainer(Method::CountWorkdays, Method::CountNightShifts, Method::CountShiftA, Method::CountShiftB, Method::CountShiftC, Method::CountShiftD),
            DataSet::Inspections => self::formContainer(Method::CountWorkdays, Method::CountNightShifts, Method::CountShiftA, Method::CountShiftB, Method::CountShiftC, Method::CountShiftD),
            DataSet::InstrumentUsages => self::formContainer(),
            DataSet::Suspensions => self::formContainer(),
            Dataset::SuspensionsWithDecisions => self::formContainer(),
            Dataset::SuspensionsWithoutDecisions => self::formContainer(),
            DataSet::SuspensionsArticles => self::formContainer(),
            DataSet::SuspensionsDecisions => self::formContainer(),
            DataSet::SuspensionsTickets => self::formContainer(Method::Sum),
            DataSet::Tickets => self::formContainer(Method::Sum)
        };
    }
    
    private static function formContainer(Method ...$methods) : Container {
        $result = new Container();
        $result->add(Method::Count->value);
        foreach ($methods as $group) {
            $result->add($group->value);
        }
        return $result;
    }
    
    public function calculate(Container $data, Container $groupingColumns) : ResultSet {
        switch($this) {
            case Method::Sum:
                return Calculator::sum($data, $groupingColumns, 'value');
            case Method::Count:
                return Calculator::count($data, $groupingColumns);
            case Method::CountWorkdays:
                return Calculator::countWorkdays($data, $groupingColumns);
            case Method::CountNightShifts:
                return Calculator::countNightShifts($data, $groupingColumns);
            case Method::CountShiftA:
                return Calculator::countShift($data, $groupingColumns, Shift::A);
            case Method::CountShiftB:
                return Calculator::countShift($data, $groupingColumns, Shift::B);
            case Method::CountShiftC:
                return Calculator::countShift($data, $groupingColumns, Shift::C);
            case Method::CountShiftD:
                return Calculator::countShift($data, $groupingColumns, Shift::D);
            default:
                return new ResultSet();
        }
    }
}
