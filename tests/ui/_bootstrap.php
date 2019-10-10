<?php
require_once(__DIR__ . "/AutoSplitTestExclude/Base/BaseCest.php");

define("WAIT_FOR_CONTROL", $settings["modules"]["enabled"][1]["WebDriver"]["wait_for_control"]);
define("WAIT_FOR_PAGE_LOADED", $settings["modules"]["enabled"][1]["WebDriver"]["wait_for_page_loaded"]);
define("WAIT_FOR_EMAIL", $settings["modules"]["enabled"][1]["WebDriver"]["wait_for_email"]);

$string = file_get_contents(codecept_data_dir('acceptance.account.json'));
$json = json_decode($string, true);
$env = $settings["current_environment"];
$envArr = explode('.', $env);
define("BROWSER", $envArr[2]);
// account data should be in the second element e.g if the env is ui.dev.chrome.yml, the account data is dev
// if the env is ui.stage.chrome.ubuntu.yml , the account data is stage
define('ACCOUNT_DATA', $json['environments'][$envArr[1]]);
