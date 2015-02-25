<?php

require_once dirname(__file__) . '/../vendor/autoload.php';

R::setup('mysql:host=localhost;dbname=Empowi', 'Empowi','adminuser');
error_reporting(E_ALL);
ini_set("display_errors", 1);

//TODO: ssl
//TODO: module security by database