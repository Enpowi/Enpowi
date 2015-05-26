<?php

(new \Enpowi\Config())
	->setupSite('Enpowi', 'http://www.enpowi.com')
	->setupMySql('localhost', 'Enpowi', 'Enpowi', 'Enpowi')
	->setupMail('admin@localhost', 'Admin', 'localhost', 'admin', 'secret');