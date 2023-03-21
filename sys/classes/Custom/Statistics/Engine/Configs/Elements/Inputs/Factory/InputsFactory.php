<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs\Factory;

use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Custom\Statistics\Engine\Configs\Elements\Inputs\YearInput as YearInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\ToYearInput as ToYearInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\SinceYearInput as SinceYearInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\MonthInput as MonthInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\DateInput as DateInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\ToDateInput as ToDateInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\SinceDateInput as SinceDateInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\UserInput as UserInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\InspectorInput as InspectorInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\UserTypeInput as UserTypeInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\LocationInput as LocationInput;
use Custom\Statistics\Engine\Configs\Elements\Inputs\LocationGroupInput as LocationGroupInput;
use Custom\Statistics\Engine\Configs\Exceptions\UndefinedInputTypeException as UndefinedInputTypeException;
use DateTime;

/**
 * Description of InputsFactory
 *
 * @author Tanzar
 */
class InputsFactory {
    
    public static function create(Inputs $type, $value = '') : Input {
        if($value === ''){
            return self::createUnsetValue($type);
        }
        else{
            return self::createSetValue($type, $value);
        }
    }
    
    private static function createUnsetValue(Inputs $type) : Input {
        switch($type){
            case Inputs::Year:
                return new YearInput(0);
            case Inputs::ToYear:
                return new ToYearInput(0);
            case Inputs::SinceYear:
                return new SinceYearInput(0);
            case Inputs::Month:
                return new MonthInput(0);
            case Inputs::Date:
                return new DateInput(new DateTime());
            case Inputs::ToDate:
                return new ToDateInput(new DateTime());
            case Inputs::SinceDate:
                return new SinceDateInput(new DateTime());
            case Inputs::User:
                return new UserInput('');
            case Inputs::Inspector:
                return new InspectorInput('');
            case Inputs::UserType:
                return new UserTypeInput('');
            case Inputs::Location:
                return new LocationInput(0);
            case Inputs::LocationGroup:
                return new LocationGroupInput(0);
            default:
                throw new UndefinedInputTypeException($type->value);
        }
    }
    
    private static function createSetValue(Inputs $type, $value) : Input {
        switch($type){
            case Inputs::Year:
                return new YearInput((int) $value);
            case Inputs::ToYear:
                return new ToYearInput((int) $value);
            case Inputs::SinceYear:
                return new SinceYearInput((int) $value);
            case Inputs::Month:
                return new MonthInput((int) $value);
            case Inputs::Date:
                return new DateInput(new DateTime($value));
            case Inputs::ToDate:
                return new ToDateInput(new DateTime($value));
            case Inputs::SinceDate:
                return new SinceDateInput(new DateTime($value));
            case Inputs::User:
                return new UserInput($value);
            case Inputs::Inspector:
                return new InspectorInput($value);
            case Inputs::UserType:
                return new UserTypeInput($value);
            case Inputs::Location:
                return new LocationInput((int) $value);
            case Inputs::LocationGroup:
                return new LocationGroupInput((int) $value);
            default:
                throw new UndefinedInputTypeException($type->value);
        }
    }
}
