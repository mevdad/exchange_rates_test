## Docker Installation Guide

Follow these steps to set up and run the application using Docker:

#### Step 1: Build the Docker Containers

`$ docker-compose build`

#### Step 2: Start the Docker Containers

`$ docker-compose up -d`

#### Step 3: Handle Node Installation Issue

If Node.js was not installed correctly, run the following command to build the Node container separately:

`$ docker build -t my-laravel-node .`

#### step 4: Copy Environment Configuration

Copy the example environment configuration to create your own environment file:

`$ cp .env.example .env`

Register at https://exchangerate.host/ and get an API key.
Paste it into the .env file in the EXCHANGE_RATE_API_KEY variable.

#### Step 5: Install Laravel Packages

Access the Laravel application container and install the necessary PHP packages:

`$ docker exec -it my-laravel-app bash`

Inside the container, run the following commands:

`$ composer install`
`$ php artisan migrate`
`$ php artisan db:seed`
`$ php artisan make:filament-user`

#### Step 6: Install Node Packages

Access the Node container and install the necessary Node.js packages:

`docker exec -it my-laravel-node bash`

Inside the container, run the following command:

`npm install`

#### Step 7: Access the Application

Open your web browser and go to:

http://localhost:8000
