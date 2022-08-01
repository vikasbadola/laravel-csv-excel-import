<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Framework 9.21.3 - Shopping Cart

In this project there is an implementation of Laravel csv/excel file import and then mapping them with the system(database) fields. In the final steps data will be saved into the database.

## Requirements

- Laravel 9.x requires a minimum PHP version of 8.0
- MYSQL DB

## Setup

- Clone or download the code.
- cd to your downloaded code ```cd vikas_case_study```
- run ```composer install```
- rename .env.example to .env
- run ```php artisan key:generate```
- Setup your database details into .env file
- Run migrations. Migrations can be found within database/migrations. 
```
php artisan migrate
```
- Run the project.
```
php artisan serve, open browser and run http://127.0.0.1:8000/
```
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
