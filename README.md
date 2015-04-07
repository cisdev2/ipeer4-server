iPeer v4 Server
========================

**Master branch**

[![Build Status](https://travis-ci.org/cisdev2/ipeer4-server.svg?branch=master)](https://travis-ci.org/cisdev2/ipeer4-server)

**Dev branch**

[![Build Status](https://travis-ci.org/cisdev2/ipeer4-server.svg?branch=dev)](https://travis-ci.org/cisdev2/ipeer4-server)

**Code Analytics**

[![Code Climate](https://codeclimate.com/github/cisdev2/ipeer4-server/badges/gpa.svg)](https://codeclimate.com/github/cisdev2/ipeer4-server)
[![Test Coverage](https://codeclimate.com/github/cisdev2/ipeer4-server/badges/coverage.svg)](https://codeclimate.com/github/cisdev2/ipeer4-server)

Development Notes
------------------------

See the [/doc](/doc) directory for documentation and development notes.

General debug tip: Delete the `/app/cache` folder if you change routing, database structure, or entity-ORM definitions.

Knowledge of the following topics is needed to work on the server-side iPeer4 code. UBC staff can get access to some nice tutorials on these topics at [Lynda.com](http://www.lynda.com/):

- Git
- PHP
- MySQL (or another relational database)
- Symfony Framework
- RESTful Services & HTTP

Resources for Symfony and main bundles/technologies used in this application:

- [The Symfony Book](http://symfony.com/doc/current/book/index.html)
- [DoctrineBundle](http://symfony.com/doc/master/bundles/DoctrineBundle/index.html)
- [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html)
- [DoctrineFixturesBundle](http://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html)
- [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle)
- [FOSRestBundle](http://symfony.com/doc/master/bundles/FOSRestBundle/index.html)
- [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle)
- [JMSSerializer](http://jmsyst.com/libs/serializer) and its [annotations](http://jmsyst.com/libs/serializer/master/reference/annotations)
- [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)

Setup and Usage
------------------------
You will need composer: https://getcomposer.org/

Before developing or running, execute this command from the root of this repo to install the dependencies:

    composer install

Once the database is setup (see below), to start/stop the server:

    php app/console server:start
    php app/console server:stop

Once running, check out the auto-generated api documentation at: [http://localhost:8000/api/doc/](http://localhost:8000/api/doc/). Change the port as needed.

For tinkering, you can use the sandbox at `/api/doc`, a command line HTTP client, or a browser extension ([Postman - REST Client](https://chrome.google.com/webstore/detail/postman-rest-client/fdmmgilgnpjigdojojpjoooidkmcomcm?hl=en) is quite nice).

Database
------------------------

When running for the first time, copy `app/config/parameters.yml.dist` to `app/config/parameters.yml` and setup the database credentials.

Also run the following the create the database and tables for you:

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

The tests make use of some "fixture" sample data. To load this into the regular database, run:

    php app/console doctrine:fixtures:load

To reset the database and load the fixtures, use the `reset` shell script (uses the commands above after running `doctrine:database:drop --force`):

    ./app/reset

Tests
------------------------

Tests are automatically run at Travis CI: [https://travis-ci.org/cisdev2/ipeer4-server](https://travis-ci.org/cisdev2/ipeer4-server)

Travis can take a while to run, so manually testing the code as you write is recommend.

Tests can be run with `phpunit`. They run on a different database (separate SQLite), so they don't affect the main one. Change `src` to a more specific path/file if you want to run a specific test. The `-c app` loads the required test config file from the `app` folder:

    phpunit -c app src
