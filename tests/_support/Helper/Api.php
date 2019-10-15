<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    // HOOK: before each suite
    public function _beforeSuite($settings = array())
    {
        $string = file_get_contents(codecept_data_dir('api.account.json'));
        $json = json_decode($string, true);
        $env = $settings["current_environment"];
        $envArr = explode('.', $env);
        // account data should be in the second part e.g if the env is acceptance.test.chrome.yml, the account data is test
        // if the env is act.stage.chrome.ubuntu.yml , the account data is stage
        define('ACCOUNT_DATA', $json['environments'][$envArr[1]]);
    }
}
