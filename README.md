<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Laravel Shop

API Shop in Laravel

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

# Getting started

Clone the repository

    git clone https://github.com/fittipaldi/php-shop.git

Switch to the repo folder

    cd php-shop - this is the Project Root

# Installation

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file (**PS.: Check the database credentials**)

    cp .env.example .env 
    
    Please create a Database and set up the credential in this variables
    
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laravel
    DB_USERNAME=root
    DB_PASSWORD=

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Run the database seed

    php artisan db:seed

Start the local development server

    php artisan serve --port=8000

You can now access the server at http://localhost:8000

To run the command line to add all UK post code from the postcode service (https://data.freemaptools.com/download/full-uk-postcodes/ukpostcodes.zip)

    php artisan command:post-codes

### Run Test

    ./vendor/bin/phpunit

# API

### API Authorization

    Header
        Authorization: Bearer iXf6omDZmOYheWw3TGxz054rhLfEjgD75KvdzXtWgqZMcKAkZia9e39WJs9EIByS
        
API Actions
-------

##### POST /api/v1/add-store - Add store params: [name, latitude, longitude, status, store_type, max_distance]

    curl --location 'http://127.0.0.1:8000/api/v1/add-store' \
    --header 'Authorization: Bearer iXf6omDZmOYheWw3TGxz054rhLfEjgD75KvdzXtWgqZMcKAkZia9e39WJs9EIByS' \
    --header 'Content-Type: application/json' \
    --data '{
    "name": "The Best Food",
    "latitude": 55.9409192,
    "longitude": -3.1973917,
    "status": "open",
    "store_type": "Restaurant",
    "max_distance": 5
    }'

##### GET /api/v1/store - Get stores by postcode params: [postcode]

    curl --location 'http://127.0.0.1:8000/api/v1/store?postcode=EH39GU' \
    --header 'Authorization: Bearer iXf6omDZmOYheWw3TGxz054rhLfEjgD75KvdzXtWgqZMcKAkZia9e39WJs9EIByS'
