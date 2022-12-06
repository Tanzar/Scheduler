<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

/**
 *
 * @author Tanzar
 */
enum ResultForm : string {
    case Table = 'Tabela';
    case MarkerPlot = 'Wykres kropkowy';
    case LinesPlot = 'Wykres liniowy';
    case MarkerAndLinesPlot = 'Wykres liniowo-kropkowy';
    case BarPlot = 'Wykres słupkowy';
    case PieChart = 'Wykres kołowy';
    case RingPlot = 'Wykres okręgowy';
    
    public function getPlotlyMode() : string {
        return match($this) {
            ResultForm::Table => 'table',
            ResultForm::MarkerPlot => 'markers',
            ResultForm::LinesPlot => 'lines',
            ResultForm::MarkerAndLinesPlot => 'lines+markers',
            ResultForm::BarPlot => '',
            ResultForm::PieChart => '',
            ResultForm::RingPlot => ''
        }; 
    }
    
    public function getPlotlyType() : string {
        return match($this) {
            ResultForm::Table => 'table',
            ResultForm::MarkerPlot => 'scatter',
            ResultForm::LinesPlot => 'scatter',
            ResultForm::MarkerAndLinesPlot => 'scatter',
            ResultForm::BarPlot => 'bar',
            ResultForm::PieChart => 'pie',
            ResultForm::RingPlot => 'pie'
        }; 
    }
}
