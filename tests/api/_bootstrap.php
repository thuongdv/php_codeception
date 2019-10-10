<?php
// Here you can initialize variables that will be available to your tests

$string = file_get_contents(codecept_data_dir('api.account.json'));
$json = json_decode($string, true);
$env = $settings["current_environment"];

define('ACCOUNT_DATA', $json['environments'][explode('.', $env)[1]]);
