<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups\Factory;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\YearGroup as YearGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\MonthGroup as MonthGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\UserGroup as UserGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\UserWithSuzugGroup as UserWithSuzugGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\UserTypeGroup as UserTypeGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\LocationsGroup as LocationsGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\LocationsGroupsGroup as LocationsGroupsGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\LocationTypeGroup as LocationTypeGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\LevelGroup as LevelGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\ActivityGroup as ActivityGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\ActivityGroupsGroup as ActivityGroupsGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\QuartersGroup as QuartersGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\SuzugGroup as SuzugGroup;
use Custom\Statistics\Engine\Configs\Elements\Groups\InstrumentGroup as InstrumentGroup;
use Custom\Statistics\Engine\Configs\Exceptions\UndefinedGroupTypeException as UndefinedGroupTypeException;

/**
 * Description of GroupsFactory
 *
 * @author Tanzar
 */
class GroupsFactory {
    
    public static function create(Groups $type, $value = '') : Group {
        if($value === ''){
            return self::createUnsetValue($type);
        }
        else{
            return self::createSetValue($type, $value);
        }
    }
    
    private static function createUnsetValue(Groups $type) : Group {
        switch ($type) {
            case Groups::Year:
                return new YearGroup(0);
            case Groups::Month:
                return new MonthGroup(0);
            case Groups::User:
                return new UserGroup("");
            case Groups::UserWithSUZUG:
                return new UserWithSuzugGroup('');
            case Groups::UserType:
                return new UserTypeGroup('');
            case Groups::Location:
                return new LocationsGroup(0);
            case Groups::LocationGroup:
                return new LocationsGroupsGroup(0);
            case Groups::LocationType:
                return new LocationTypeGroup(0);
            case Groups::Level:
                return new LevelGroup(-1);
            case Groups::Activity:
                return new ActivityGroup(0);
            case Groups::ActivityType:
                return new ActivityGroupsGroup('');
            case Groups::Quarters:
                return new QuartersGroup(0);
            case Groups::NumberSUZUG:
                return new SuzugGroup(0);
            case Groups::Instrument:
                return new InstrumentGroup(0);
            default:
                throw new UndefinedGroupTypeException($type->value);
        }
    }
    
    private static function createSetValue(Groups $type, $value) : Group {
        switch ($type) {
            case Groups::Year:
                return new YearGroup((int) $value);
            case Groups::Month:
                return new MonthGroup((int) $value);
            case Groups::User:
                return new UserGroup($value);
            case Groups::UserWithSUZUG:
                return new UserWithSuzugGroup($value);
            case Groups::UserType:
                return new UserTypeGroup($value);
            case Groups::Location:
                return new LocationsGroup((int) $value);
            case Groups::LocationGroup:
                return new LocationsGroupsGroup((int) $value);
            case Groups::LocationType:
                return new LocationTypeGroup((int) $value);
            case Groups::Level:
                return new LevelGroup((int) $value);
            case Groups::Activity:
                return new ActivityGroup((int) $value);
            case Groups::ActivityType:
                return new ActivityGroupsGroup($value);
            case Groups::Quarters:
                return new QuartersGroup((int) $value);
            case Groups::NumberSUZUG:
                return new SuzugGroup((int) $value);
            case Groups::Instrument:
                return new InstrumentGroup((int) $value);
            default:
                throw new UndefinedGroupTypeException($type->value);
        }
    }
}
