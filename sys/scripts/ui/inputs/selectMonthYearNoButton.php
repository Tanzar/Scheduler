<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    
    $appconfig = AppConfig::getInstance();
    $cfg = $appconfig->getAppConfig();
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
?>
<label class="standard-text">
    <?php echo $interface->get('select_date'); ?>
</label>
<select class="standard-input" id="selectMonth">
<?php
    $months = $languages->get('months');
    foreach($months as $key => $month){
        echo '<option value="' . $key . '"';
        if($key === (int) date('m')){
            echo ' selected';
        }
        echo '>';
        echo $month;
        echo '</option>';
    }
?>
</select>
<select class="standard-input" id="selectYear">
<?php
    $startYear = (int) $cfg->get('yearStart');
    $endYear = ((int) date('Y')) + 1;
    for($year = $startYear; $year <= $endYear; $year++){
        echo '<option value="' . $year . '"';
        if($year === (int) date('Y')){
            echo ' selected';
        }
        echo '>';
        echo $year;
        echo '</option>';
    }
?>
</select>