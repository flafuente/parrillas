<?php
//PHP
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Madrid');
ini_set("session.gc_maxlifetime","140000");

//Config
$_config['title'] = "Tribo.tv";
$_config['defaultLang'] = "en_GB";
$_config['template'] = "bootstrap";
$_config['defaultApp'] = "parrilla";
$_config['defaultLimit'] = 10;
$_config['debug'] = false;
$_config['cookie'] = "authtriboparrillas";

//Mail
$_config['mailHost'] = "smtp.mandrillapp.com";
$_config['mailPort'] = "587";
$_config['mailSecure'] = "";
$_config['mailUsername'] = "soporte@spmedia.es";
$_config['mailPassword'] = "2RQHrMm4FLMIMklYfI8LfA";
$_config['mailFromAdress'] = "noreply@tribo.tv";
$_config['mailFromName'] = "TriboTV";

//Database
$_config['dbHost'] = "localhost";
$_config['dbUser'] = "root";
$_config['dbPass'] = "";
$_config['dbName'] = "triboParrillas";

//Urls/Paths
$_config['path'] = dirname(__FILE__);
$_config['host'] = $_SERVER["SERVER_NAME"];
$_config['dir'] = str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]);
$_config['url'] = "http://".$_config['host'].$_config['dir'];
