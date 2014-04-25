SensioLabs Jobs
===============

##### Requirements:
* Install and start Redis : [redis quickstart](http://redis.io/topics/quickstart)

##### 1. Install the vendors : `php composer.phar install`

##### 2. Create the folder app/sessions

```
mkdir app/sessions
chmod -R 755 app/sessions
```

##### 3. Setup and launch tests

###### With phpunit :

```
php app/console doctrine:database:create --env=test
php app/console doctrine:schema:create --env=test

phpunit -c app/
```

###### with casperjs :

```
php app/console doctrine:fixtures:load

casperjs test casper-tests/list.js --url=http://yourapp.dev/app_dev.php
```
