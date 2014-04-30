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

##### 4. Load assets

```
php app/console assets:install --symlink
php app/console assetic:dump
```

##### 5. Setup and launch tests

###### With phpunit :

```
php app/console doctrine:database:create --env=test
php app/console doctrine:schema:create --env=test

. tests.sh http://yourapp.dev/app_test.php
```
