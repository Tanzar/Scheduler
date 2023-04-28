<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts;

/**
 *
 * @author Tanzar
 */
enum Groups : string {
    case Year = 'Lata';
    case Month = 'Miesiące';
    case User = 'Użytkownik';
    case UserWithSUZUG = 'Użytkownik z nr. SUZUG';
    case UserType = 'Typ użytkownika';
    case Location = 'Miejsce';
    case InspectableLocation = 'Miejsce kontroli';
    case LocationGroup = 'Grupa Miejsc';
    case LocationType = 'Typ Miejsca';
    case Level = 'Poziom';
    case Activity = 'Czynność';
    case ActivityType = 'Rodzaj Czynności';
    case Quarters = 'Kwartały';
    case NumberSUZUG = 'Numer z SUZUG';
    case Instrument = 'Przyrząd pomiarowy';
}
