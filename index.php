<?php

//Pass trought index
define("_EXE", 1);

//Configuration
include 'config.php';

//Constants
include 'constants.php';

//Composer autoload
require 'vendor/autoload.php';

//Language init
$language = new Language();

//Registry init
$registry = new Registry();

//Router init
$router = new Router();

//Delegate
$router->delegate();

$config = Registry::getConfig();
