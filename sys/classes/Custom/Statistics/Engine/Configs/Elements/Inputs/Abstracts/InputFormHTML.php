<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts;

/**
 *
 * @author Tanzar
 */
enum InputFormHTML : string {
    case Select = 'select';
    case Date = 'date';
    case Number = 'number';
    case Text = 'text';
}
