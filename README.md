#microphork-tests

[![Build Status](https://travis-ci.org/phork/microphork-tests.svg)](https://travis-ci.org/phork/microphork-tests) [![Coverage Status](https://coveralls.io/repos/phork/microphork-tests/badge.png)](https://coveralls.io/r/phork/microphork-tests)


##Introduction

These tests use [PHPUnit](http://phpunit.de/) and [Composer](http://getcomposer.org/). They are run automatically with [Travis](https://travis-ci.org) but can also be run manually. 


##Usage

```
$ git clone https://github.com/phork/microphork-tests.git
$ cd microphork-tests
$ composer install --prefer-source --no-interaction --dev
$ vendor/bin/phpunit --configuration tests/phpunit.xml
```


##Credits

Built by [Elenor](http://elenor.net) at [Phork Labs](http://phorklabs.com).


##License

Licensed under The MIT License
<http://www.opensource.org/licenses/mit-license.php>