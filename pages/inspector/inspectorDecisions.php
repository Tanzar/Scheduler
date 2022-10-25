<?php 
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    use Tanweb\Security\PageAccess as PageAccess;
    use Services\DocumentService as DocumentService;
    
    PageAccess::allowFor(['admin', 'schedule_user_inspector']);   //locks access if failed to pass redirects to index page
    
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
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
                echo $cfg->get('name');
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('inspector');
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
            Resources::linkJS('inspectorDecisions.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php
            Scripts::run('inspectorSideMenu.php');
        ?>
        <div class="page-contents-centered">
            <div class="page-contents-element">
                <?php Scripts::run('selectMonthYear.php'); ?>
            </div>
            <div class="page-contents-element">
                <select class="standard-input" id="documents">
                    <?php 
                        echo '<option selected placeholder disabled value="0">'
                             . $interface->get('select_document') . '</option>';
                        $documentService = new DocumentService();
                        $month = (int) date('m');
                        $year = (int) date('Y');
                        $documents = $documentService->getCurrentUserDocumentsByMonthYear($month, $year);
                        foreach ($documents->toArray() as $item){
                            $document = new Container($item);
                            echo '<option value="' . $document->get('id') . '">';
                            echo $document->get('number') . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="page-contents-element">
                <div id="decisions"></div>
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
