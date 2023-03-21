<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts;

/**
 *
 * @author Tanzar
 */
enum Inputs : string {
    case Year = 'Rok';
    case SinceYear = 'Od Roku';
    case ToYear = 'Do Roku';
    case Month = 'Miesiąc';
    case Date = 'Data';
    case SinceDate = 'Od daty';
    case ToDate = 'Do daty';
    case User = 'Użytkownik';
    case Inspector = 'Inspektor';
    case UserType = 'Typ użytkownika';
    case Location = 'Miejsce';
    case LocationGroup = 'Grupa miejsc';
}
