<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Types;

/**
 *
 * @author Tanzar
 */
enum OutputForms : string {
    case Table = 'Tabela';
    case MarkerPlot = 'Wykres kropkowy';
    case LinesPlot = 'Wykres liniowy';
    case MarkerAndLinesPlot = 'Wykres liniowo-kropkowy';
    case BarPlot = 'Wykres słupkowy';
    case PieChart = 'Wykres kołowy';
    case RingPlot = 'Wykres okręgowy';
    case Pattern = 'Wzór XLSX';
    
    public function getPlotlyType() : string {
        return match ($this) {
            OutputForms::MarkerPlot => 'markers',
            OutputForms::LinesPlot => 'lines',
            OutputForms::MarkerAndLinesPlot => 'markers+lines',
            OutputForms::BarPlot => 'bar',
            OutputForms::PieChart => 'pie',
            OutputForms::RingPlot => 'pie',
            default => 'none'
        };
    }
}
