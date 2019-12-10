<?php
require_once __DIR__ . '/../lib/WebRouter.php';

class ARouter extends WebRouter
{

    protected function log(array $data) {
        @file_put_contents(SETTING_LOGFILE, json_encode($data) . PHP_EOL, FILE_APPEND);
    }

    protected final function html($jsFile, $container = "app", $windowData = null)
    {
        ?><!DOCTYPE html>
        <html>

        <head>
            <meta charset='utf-8'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <title><?php echo SETTING_TITLE; ?></title>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon" />
        </head>

        <body>
            <div data-role="__LOADING__" style="text-align: center;">
                <img style="vertical-align:middle" src="assets/img/loading.gif"><br/>
                <b><?php echo SETTING_PAGE_LOADING; ?></b>
            </div>
            <div id="<?php echo $container ?>"></div>
            <script>
                <?php
                        if (is_array($windowData)) {
                            foreach ($windowData as $k => $v) {
                                echo "window." . $k . "=" . json_encode($v) . ";" . PHP_EOL;
                            }
                        }
                        echo file_get_contents($jsFile);
                        ?>
                        document.querySelector('[ data-role=__LOADING__ ]').style.display = "none";                        
            </script>            
        </body>

        </html>
<?php


    }
}
