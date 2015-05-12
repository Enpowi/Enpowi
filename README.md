# Enpowi
web platform, Engineered with power & wisdom

Enpowi is:
* a single page content management system
* very fast
* cutting edge
* very small

Setup in 5 steps:

Step 1:
```
composer install
```

Step 2:
```
bower install
```

Step 3:
```
cp modules/setup/config.default.php modules/setup/config.php
```

Step 4:
```php
//change to fit your site
$config->setupMySql('localhost', 'datbasename', 'user', 'password');
```

Step 5:
```
visit http://yoursite.com/setup.php
```

Enjoy Enpowi
