<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts;

/**
 *
 * @author Tanzar
 */
enum Operations : string {
    case Count = 'Zliczanie';
    case CountWorkdays = 'Zliczanie roboczodniówek';
    case CountShiftA = 'Zliczanie roboczodniówek na zmianie A';
    case CountShiftB = 'Zliczanie roboczodniówek na zmianie B';
    case CountShiftC = 'Zliczanie roboczodniówek na zmianie C';
    case CountShiftD = 'Zliczanie roboczodniówek na zmianie D';
    case Sum = 'Sumowanie';
}
