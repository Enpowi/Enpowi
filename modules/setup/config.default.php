<?php
global $config;

$config = new \Enpowi\Config();
$config->setupMySql('localhost', 'Enpowi', 'Enpowi', 'Enpowi');