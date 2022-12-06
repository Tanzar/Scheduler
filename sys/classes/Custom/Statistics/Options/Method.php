<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

use Tanweb\Container as Container;
use Custom\Statistics\Options\DataSet as DataSet;
/**
 *
 * @author Tanzar
 */
enum Method : string {
    case Sum = 'Suma';
    case Count = 'Zliczanie';
    case CountWorkdays = 'Zliczanie roboczodniówek';
    case CountShifts = 'Zliczanie zmian';
    case AveragePerUserType = 'Średnia na typ użytkownika';
    case CountAverageWorkdayPerUserType = 'Średnia ilość roboczodniówek na typ użytkownika';
    
    public static function getMethodsForDataSet(DataSet $dataset) : Container {
        return match ($dataset){
            DataSet::Articles => self::formContainer(Method::AveragePerUserType),
            DataSet::CourtApplications => self::formContainer(Method::Sum, Method::AveragePerUserType),
            DataSet::Decisions => self::formContainer(Method::AveragePerUserType),
            DataSet::Entries => self::formContainer(Method::CountWorkdays, Method::CountShifts, Method::CountAverageWorkdayPerUserType),
            DataSet::Inspections => self::formContainer(Method::CountWorkdays, Method::CountShifts, Method::CountAverageWorkdayPerUserType),
            DataSet::InstrumentUsages => self::formContainer(Method::AveragePerUserType),
            DataSet::Suspensions => self::formContainer(Method::AveragePerUserType),
            DataSet::SuspensionsArticles => self::formContainer(Method::AveragePerUserType),
            DataSet::SuspensionsDecisions => self::formContainer(Method::AveragePerUserType),
            DataSet::SuspensionsTickets => self::formContainer(Method::Sum, Method::AveragePerUserType),
            DataSet::Tickets => self::formContainer(Method::Sum, Method::AveragePerUserType)
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
}
