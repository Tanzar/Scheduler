<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

/**
 *
 * @author Tanzar
 */
enum Type : string {
    case Single = 'Pojedyncze';
    case Monthly = 'Zestawienie miesięczne';
    case Yearly = 'Zestawienie roczne';
    case Form = 'Ze wzoru';
}
