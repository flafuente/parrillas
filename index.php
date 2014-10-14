<?php

//Pass trought index
define("_EXE", 1);

//Configuration
include 'config.php';

//Constants
include 'constants.php';

//Composer autoload
require 'vendor/autoload.php';

// Api definition
Api::init("http://tribo.local/", "dfg34g45gdfgdfg");

//Language init
$language = new Language();

//Registry init
$registry = new Registry();

//Router init
$router = new Router();

//Delegate
$router->delegate();

$config = Registry::getConfig();
