<?php

(new \Enpowi\Config())
	//->requireSSL()
	->setupSite('Enpowi', 'http://www.enpowi.com')
	->setupMySql('localhost', 'Enpowi', 'Enpowi', 'Enpowi')
	->setupMail('admin@localhost', 'Admin', 'localhost', 'admin', 'secret');