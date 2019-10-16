# Generate test suite
```
codecept generate:cest suitename CestName
```
* suitename: for instance, api/acceptance
* CestName: SessionsAPI

# Run
`codecept run tests/api/MyCept.php`: run only MyCept

# Other common commands
## Generate page object
```
codecept g:pageobject SignUpPage
```
## For further [Codeception command](http://codeception.com/docs/reference/Commands)

# Debug
### PHPStorm
1. Edit configurations
2. Add new PHP Script configuration
3. Edit the following information:
```
Configuration
    File: <path to the codecept>, e.g. /test/tests/vendor/codeception/codeception/codecept
    Arguments: e.g. run tests/ui/LoginCest.php --env ui.stage.chrome --html
	
Command Line 
    Custom working directory: <path to the project>, e.g. /test
```

### NetBeans
On Project Properties, configuring as below:

    Run as: Script (run in commmand line)
    Index File: codecept run file, e.g. X:\php_codeception\tests\vendor\codeception\codeception\codecept
    Arguments: codecept run arguments, e.g. run tests/acceptance/SignInCest:tc01SignInWithValidEmailPassword --env chrome --html --xml
    Working Directory: project path, e.g. X:\php_codeception

### Visual Studio code
References:

* https://code.visualstudio.com/docs/languages/php
* https://github.com/jadb/atom-codeception-snippets

Add the following configuration info into the launch.json file, please change the **args** values accordingly:
```
{
    "name": "Listen for XDebug",
    "type": "php",
    "request": "launch",
    "program": "${workspaceFolder}/tests/vendor/codeception/codeception/codecept",
    "args": [
        "run", "tests/api/AvailabilityCest:tc01GetSystemAvailability",
        "--env", "api.dev",
        "-g", "critical",
        "--html"
    ],
    "cwd": "${workspaceFolder}"
}
```

### Configure the Xdebug Extension
Add the following lines at the end of your php.ini file:
```
[Xdebug]
zend_extension=<full_path_to_xdebug_extension/or filename in ext folder>
xdebug.remote_autostart=on
xdebug.remote_enable=on
xdebug.remote_enable=1
xdebug.remote_handler="dbgp"
xdebug.remote_host=127.0.0.1
xdebug.remote_port=<the port to which Xdebug tries to connect on the host(default 9000)>
xdebug.remote_mode=req
xdebug.idekey="netbeans-xdebug"
```

In NetBeans IDE, open Tools-> Options->PHP->Debugging. The values of debugger port and Session Id should match with the port and idekey specified in php.ini

# References
* http://codeception.com/
* https://code.tutsplus.com/courses/modern-testing-in-php-with-codeception
* https://stackoverflow.com/questions/17613726/netbeans-shows-waiting-for-connection-netbeans-xdebug