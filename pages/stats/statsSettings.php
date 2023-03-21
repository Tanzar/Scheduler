<?php
    //--- use this atop every page for security if in PageAccess::allowFor() you pass empty array all will be allowed in
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Custom\Statistics\Engine\Configs\Types\OutputForms as OutputForms;
    use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
    use Custom\Statistics\Engine\Configs\Elements\Inputs\Factory\InputsFactory as InputsFactory;
    use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\InputFormHTML as InputFormHTML;
    
    PageAccess::allowFor(['admin', 'stats_admin']);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $interfaceText = new Container($languages->get('interface'));
?>
<!DOCTYPE html>
<!--
This code is free to use, just remember to give credit.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>
            <?php
                $appconfig = AppConfig::getInstance();
                $cfg = $appconfig->getAppConfig();
                echo $cfg->get('name') . ': ';
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('stats');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkCSS('datatable.css');
            Resources::linkCSS('modal-box.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('modalBox.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('statsSettings.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('statsSideMenu.php'); ?>
        <div class="page-contents" id="display">
            <div class="horizontal-container">
                <div id="stats"></div>
                <div>
                    <div class="standard-text-title">
                        <?php echo $interfaceText->get('generation_settings'); ?>
                    </div>
                    <br>
                    <button id="saveStats" class="standard-button"><?php echo $interfaceText->get('save'); ?></button>
                    <br>
                    <input type="text" id="title" class="standard-input" placeholder="<?php echo $interfaceText->get('title'); ?>" required>
                    <input type="number" id="sortPriority" class="standard-input" placeholder="<?php echo $interfaceText->get('sort_priority'); ?>" min="1" max="100" value="100" required>
                    <select id="outputForm" class="standard-input" required>
                        <option value="" selected disabled><?php echo $interfaceText->get('select_output_type'); ?></option>
                        <?php 
                            $forms = OutputForms::cases();
                            foreach ($forms as $item) {
                                echo '<option value="' . $item->value . '">' . $item->value . '</option>';
                            }
                        ?>
                    </select>
                    <input type="file" name="uploadPatternFile" id="uploadPatternFile" accept=".xlsx" class="standard-input" required>
                    <button id="uploadPattern" class="standard-button">
                        <?php echo $interfaceText->get('send'); ?>
                    </button>
                    <div class="horizontal-container">
                        <div class="centered-contents">
                            <div class="standard-text"><?php echo $interfaceText->get('inputs'); ?></div>
                            <table id="inputsOptions" class="centered-contents-bordered">
                                <?php 
                                    $inputTypes = Inputs::cases();
                                    foreach ($inputTypes as $item){
                                        $input = InputsFactory::create($item);
                                        echo '<tr>';
                                        echo '<td style="min-width: 140px"><input type="checkbox" id="input_' . $item->value . '" name="input_' . $item->value . '"><label for="input_' . $item->value . '">' . $item->value . '</td>';
                                        echo '<td>';
                                        $inputForm = $input->getFormHTML();
                                        switch ($inputForm) {
                                            case InputFormHTML::Text:
                                                echo '<input id="input_' . $item->value . '_value" class="standard-input" type="text" placeholder="' . $interfaceText->get('undefined') . '">';
                                                break;
                                            case InputFormHTML::Select:
                                                $options = $input->getOptions();
                                                echo '<select id="input_' . $item->value . '_value" class="standard-input">';
                                                echo '<option value="">' . $interfaceText->get('undefined') . '</option>';
                                                foreach ($options->toArray() as $item) {
                                                    echo '<option value="' . $item['value'] . '">' . $item['title'] . '</option>';
                                                }
                                                echo '</select>';
                                                break;
                                            case InputFormHTML::Date:
                                                echo '<input id="input_' . $item->value . '_value" class="standard-input" type="date">';
                                                break;
                                            case InputFormHTML::Number:
                                                echo '<input id="input_' . $item->value . '_value" class="standard-input" type="number" placeholder="' . $interfaceText->get('undefined') . '">';
                                                break;
                                            default:
                                                break;
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    $k = 0;
                                    
                                ?>
                            </table>
                        </div>
                        <div class="centered-contents">
                            <div class="standard-text"><?php echo $interfaceText->get('datasets'); ?></div>
                            <div id="datasets"></div>
                        </div>
                        <div class="centered-contents">
                            <div class="standard-text"><?php echo $interfaceText->get('output_config'); ?></div>
                            <div id="outputs" class="centered-contents-bordered" style="min-width: 200px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        init();
    </script>
</html>
