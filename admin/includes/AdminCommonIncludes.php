<?php

/*if(isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
ob_start('ob_gzhandler');
else
ob_start();*/
session_start();
require_once('../controllers/Controller.php');
require_once('../models/Database.php');
require_once('../config/db_config.php');

global $globalDbManager;
$globalDbManager = new Database();
$globalDbManager->dbConnect = $globalDbManager->connect($dbConfig['hostName'], $dbConfig['userName'], $dbConfig['passWord'], $dbConfig['dataBase']);

require_once('../models/Model.php');
require_once('../config/config.php');
require_once('../includes/AdminTemplates.php');
require_once('../includes/CommonFunctions.php');
?>