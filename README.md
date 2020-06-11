# Shortador

Shortador is a simple Url shortener written in PHP and served by a Laravel application. 

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/e70358bed287e0f6e827#?env%5Blocal-shortador%5D=W3sia2V5IjoiYXBwX3VybCIsInZhbHVlIjoic2hvcnRhZG9yLmxvY2FsaG9zdCIsImVuYWJsZWQiOnRydWV9LHsia2V5Ijoic2hvcnRlbmVkX3VybCIsInZhbHVlIjoiIiwiZW5hYmxlZCI6dHJ1ZX1d)

## Documentation

An API documentation is publicly available at on https://documenter.getpostman.com/view/705705/SzzgAekC?version=latest#intro .

## Installation guide

First of all, launch this command from a terminal (assuming the terminal is _Bash_ or some Linux based terminal):

`cp .env.example .env`

this will copy the contents of the example environment file to the correct .env file. 


At the _root_ folder of this project there is a Dockerfile and a docker-compose.yml file. 

Install the docker environment for your platform (https://www.docker.com/products/docker-desktop).
Once Docker is installed, open a terminal and go to the project root folder (the one you will find the README file into) and launch these commands:

- `docker-compose build app` (this command will probably take a couple of minutes to complete.)

- `docker-compose up -d` and all the services will start. 

At this point it's time to install all the dependencies for the Laravel project, to create the env key and to launch all the DB migrations:

- `docker-compose exec app composer install`
- `docker-compose exec app php artisan key:generate`
- `docker-compose exec app php artisan migrate`

Your app is up and running and you can reach it from a browser at the address: `http://localhost:8000`.

It's possible to use the Postman [Collection](https://app.getpostman.com/run-collection/e70358bed287e0f6e827#?env%5Blocal-shortador%5D=W3sia2V5IjoiYXBwX3VybCIsInZhbHVlIjoic2hvcnRhZG9yLmxvY2FsaG9zdCIsImVuYWJsZWQiOnRydWV9LHsia2V5Ijoic2hvcnRlbmVkX3VybCIsInZhbHVlIjoiIiwiZW5hYmxlZCI6dHJ1ZX1d) to test all the APIs. 

Please mind that the Postman environment should be set accordingly with the docker-compose settings. You can find an example enviroment in the folder `docker-compose/collections`, that environment is already set up with the docker settings. 


## Testing

Copy the example environment to the testing environment with the following command (launched in the project folder):

`cp .env.example .env.testing`

From the terminal, just launch:

`docker-compose exec app php artisan test --env=testing`

And artisan will execute all the test located in the _tests_ folder.
