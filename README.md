iPeer v4 Server
========================

Basics
------------------------
You will need composer: https://getcomposer.org/

Before developing or running, execute this command from the root of this repo to install the dependencies:

    composer install

To run the server:

    php app/console server:run

Once running, check out the auto-generated api documentation at: http://localhost:8000/api/doc/

Database
------------------------

When running for the first time, setup the database parameters in `app/config/parameters.yml`. Also run the following the create the database and tables for you:

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

The tests make use of some "fixture" sample data. To load this into the regular database, run:

    php app/console doctrine:fixtures:load

To reset the database, run:

    php app/console doctrine:database:drop --force
    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

Tests
------------------------

Tests can be run with `phpunit`. They run on a different database, so they don't affect the main one. Change `src` to a more specific path if you want to run a specific test. The `-c app` just loads the test config file from the `app` folder:

    phpunit -c app src
