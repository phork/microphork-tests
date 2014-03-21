#microphork-tests

* [By Phork Labs](http://phorklabs.com/)
* Version: 0.1


##Introduction

These tests use [PHPUnit](http://phpunit.de/) and [Composer](http://getcomposer.org/). They are run automatically with [Travis](https://travis-ci.org) but can also be run manually. 


##Usage

```
$ git clone https://github.com/phork/microphork-tests.git
$ cd microphork-tests
$ composer install --prefer-source --no-interaction --dev
$ vendor/phpunit/phpunit/phpunit --configuration tests/phpunit.xml
```


##License

Licensed under The MIT License
<http://www.opensource.org/licenses/mit-license.php>