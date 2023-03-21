<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Statistics\Engine;

use Custom\Statistics\Engine\Configs\Types\OutputForms as OutputForms;
use Custom\Statistics\Engine\Generators\TableGenerator as TableGenerator;
use Custom\Statistics\Engine\Generators\PlotGenerator as PlotGenerator;
use Custom\Statistics\Engine\Generators\PiePlotGenerator as PiePlotGenerator;
use Custom\Statistics\Engine\Generators\PatternGenerator as PatternGenerator;
use Custom\Statistics\Engine\File\TablePDF as TablePDF;
use Custom\Statistics\Engine\File\TableXLSX as TableXLSX;
use Tanweb\Container as Container;

/**
 * Description of Stats
 *
 * @author Tanzar
 */
class Stats {
    
    public static function generateOutput(Container $config, Container $inputs) : Container {
        $form = $config->get('output_form');
        $type = OutputForms::from($form);
        return self::generateByType($type, $config, $inputs);
    }
    
    private static function generateByType(OutputForms $type, Container $config, Container $inputs) : Container {
        if($type === OutputForms::Table){
            $generator = new TableGenerator($config, $inputs);
            return $generator->generate();
        }
        elseif($type === OutputForms::MarkerPlot || $type === OutputForms::MarkerAndLinesPlot ||
                $type === OutputForms::LinesPlot || $type === OutputForms::BarPlot){
            $generator = new PlotGenerator($config, $inputs);
            return $generator->generate();
        }
        elseif($type === OutputForms::PieChart || $type === OutputForms::RingPlot){
            $generator = new PiePlotGenerator($config, $inputs);
            return $generator->generate();
        }
        elseif($type === OutputForms::Pattern){
            $generator = new PatternGenerator($config, $inputs);
            return $generator->generate();
        }
        else{
            return new Container();
        }
    }
    
    public static function generateTablePDF(Container $config, Container $inputs) : void {
        $generator = new TableGenerator($config, $inputs);
        $generator->setToGenerateTable();
        $output = $generator->generate();
        $data = new Container($output->get('table'));
        TablePDF::generate($data);
    }
    
    public static function generateTableXLSX(Container $config, Container $inputs) : void {
        $generator = new TableGenerator($config, $inputs);
        $generator->setToGenerateTable();
        $output = $generator->generate();
        $data = new Container($output->get('table'));
        TableXLSX::generate($data);
    }
}
