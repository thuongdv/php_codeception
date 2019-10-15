<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    // HOOK: used after configuration is loaded
    public function _initialize()
    {
        $webdriverConfig = $this->getModule('WebDriver');
        define("WAIT_FOR_CONTROL", $webdriverConfig->_getConfig('wait_for_control'));
        define("WAIT_FOR_PAGE_LOADED", $webdriverConfig->_getConfig('wait_for_page_loaded'));
        define("WAIT_FOR_EMAIL", $webdriverConfig->_getConfig('wait_for_email'));
    }

    // HOOK: before each suite
    public function _beforeSuite($settings = array())
    {
        $string = file_get_contents(codecept_data_dir('acceptance.account.json'));
        $json = json_decode($string, true);
        $env = $settings["current_environment"];
        $envArr = explode('.', $env);
        define("BROWSER", $envArr[2]);
        // account data should be in the second part e.g if the env is acceptance.test.chrome.yml, the account data is test
        // if the env is act.stage.chrome.ubuntu.yml , the account data is stage
        define('ACCOUNT_DATA', $json['environments'][$envArr[1]]);
    }

    public function findElements($selector)
    {
        return $this->getModule('WebDriver')->_findElements($selector);
    }

    public function findElement($selector)
    {
        $els = $this->getModule('WebDriver')->_findElements($selector);
        return count($els) > 0 ? $els[0] : null;
    }
}
