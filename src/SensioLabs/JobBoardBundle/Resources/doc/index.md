SensioLabs Jobs Documentation
=============================

## Requirements
* Install and start Redis : [redis quickstart](http://redis.io/topics/quickstart)
* Install and start ElasticSearch : [elasticsearch installation](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/_installation.html)

## Installation

##### 1. Install the vendors : `php composer.phar install`

##### 2. Create the folder app/sessions

```
mkdir app/sessions
chmod -R 755 app/sessions
```

##### 3. Load assets

```
php app/console assets:install --symlink
php app/console assetic:dump
```

## Elasticsearch

After installing ElasticSearch, this project already contains all the default
configuration for ElasticSearch with the [FOSElasticaBundle](https://github.com/FriendsOfSymfony/FOSElasticaBundle)
in `app/config/config.yml`

##### You can configure your default connection, `host` and `port`

```
# app/config/config.yml
fos_elastica:
    clients:
        default: { host: localhost, port: 9200 }
```

##### Configure index and mapping
```
fos_elastica:
    clients:
        default: { host: localhost, port: 9200 }
    indexes:
        jobs:
            client: default
            types:
                Job:
                    mappings:
                        title:        { type: string, boost: 3 }
                        company:      { type: string, boost: 2 }
                        city:         { type: string, boost: 1 }
                        description:  { type: string }
                        country:      { type: string, index: not_analyzed }
                        contractType: { type: string, index: not_analyzed }
                        created_at:   { type: string, index: not_analyzed }
                    persistence:
                        driver: orm
                        model: SensioLabs\JobBoardBundle\Entity\Job
                        provider:
                            # we want indexes specific data from database
                            query_builder_method: createSearchIndexQueryBuilder
                        listener:
                            # the same way, we want indexes specific data
                            # when they are listened (insert, update, delete)
                            is_indexable_callback: isPublic
                        finder: ~
                        elastica_to_model_transformer:
                            # with our own dataTransformer
                            # we can improve request set by Doctrine
                            service: sensiolabs.elastica.job_transformer

```
Index `jobs` use the `default` client.

You can have multi types, here it's `Job` with the mapping definition : `title`,
`company`, `city`, `description`, `contry`, `contractType` and `created_at`
are indexed form model `SensioLabs\JobBoardBundle\Entity\Job`.

For more information see the [documentation here](https://github.com/FriendsOfSymfony/FOSElasticaBundle/blob/master/Resources/doc/setup.md#d-defining-index-types)

##### Populate data in ElasticSearch
Run this command and all your data will be indexes for the first time.
```
php app/console fos:elastica:populate
```

## Tests

Setup test database

```
php app/console doctrine:database:create --env=test
php app/console doctrine:schema:update --env=test
```
Launch tests
```
. tests.sh http://path_to_your_website/app_test.php
```
