Automated testing with [Codeception](http://codeception.com/).

## 1. Set up environment
### Install composer
Download and install composer from [Composer site](https://getcomposer.org/download/).

To make sure you installed composer successfully, open CMD and type `composer`, output likes this:
```
C:\Users\thuong.dang>composer
You are running composer with xdebug enabled. This has a major impact on runtime
 performance. See https://getcomposer.org/xdebug
   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 1.2.2 2016-11-03 17:43:15

Usage:
  command [options] [arguments]

Options:
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
      --profile                  Display timing and memory usage information
```

### Install dependencies
* Open CMD in /tests folder
* Type `composer install`
* Note: Extension php_curl should be enabled in php.ini before running `composer install`  

It will automatically generated vendor folder which contains dependencies.

## 2. Run
### API suite
```
./vendor/bin/codecept -c codeception.yml run api --env api.test -x notFullyImplementedOrSupported --html
```

### Acceptance suite
**UI browsers: Chrome, Firefox etc**
```
./vendor/bin/codecept run acceptance --env acceptance.test.chrome --html
```

### Parallel running
```
./vendor/bin/robo parallel:all acceptance.test.chrome.parallel codeception.yml skipGroups
```

## Note: For Ubuntu
### Install PHP 7.0
```
sudo apt-get install php7.0
```
### Install PHP modules
```
sudo apt install php7.0-mbstring php7.0-xml php7.0-curl php7.0-zip php-bcmath
```

### Enable PHP modules
```
 sudo phpenmod mbstring dom bcmath
```

### Install Composer
```
sudo apt install composer
```

## 3. Documentation
*guides/WORKING_WITH_FRAMEWORK.md* helps you working with codeception, common IDEs.