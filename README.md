SensioLabs Jobs
===============

##### Requirements:
* Install and start Redis : [redis quickstart](http://redis.io/topics/quickstart)
* Install and start ElasticSearch : [elasticsearch installation](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/_installation.html)

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

###### With phpunit and CapserJS:

```
php app/console doctrine:database:create --env=test
php app/console doctrine:schema:update --env=test

. tests.sh http://yourapp.dev/app_test.php
```
