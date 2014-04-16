SensioLabs Jobs
===============

##### 1. Install the vendors : `php composer.phar install`

##### 2. Create the folder app/sessions

```
mkdir app/sessions
chmod -R 755 app/sessions
```

##### 3. Setup test environment

```
php app/console doctrine:database:create --env=test
php app/console doctrine:schema:create --env=test

phpunit -c app/
```
