<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- **[Codecourse](https://www.codecourse.com)**
- [Fragrantica](https://www.fragrantica.com)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).


## Environtment

This Project is developed using Laravel 5.4 framework

## Homestead setup

- Please download and setup the [homestead vagrant box](https://laravel.com/docs/5.4/homestead) first
- For develop convinience, use *laravel.dev* as the vhost name pointing to the backend code base, please refer to the following configuration file at `~/.homestead/Homestead.yaml`. ie. Laravel require to route the index file into {codebase}/public folder, please do so in the vhost mapping, also, this setup enables you to use phpmyadmin as the database UI, remember to download the phpmyadmin and place it under /Code/pma where Code is the shared folder between your host machine and the vagrant box.

```
---
ip: "192.168.10.10"
memory: 4096
cpus: 1
provider: virtualbox

authorize: ~/.ssh/id_rsa.pub

keys:
    - ~/.ssh/id_rsa

folders:
    - map: C:\Users\jason\Code
      to: /home/vagrant/Code

sites:
    - map: laravel.dev
      to: /home/vagrant/Code/yechefBack/public

    - map: pma.dev
      to: /home/vagrant/Code/pma

databases:
    - homestead
```

- Go to `~/Homestead` folder and run `vagrant up` to start the virtual machine and run `vagrant ssh` to ssh into it.

## Project Setup

Note: All following command should be run inside your homestead box. Make sure to ssh into it first.

- Run the following script *in* your project root directory. eg. In the configuration file described above, run scripts under `/Code/yechefBack`

```
# set up the project configuration file since its being git ignored
cp .env.example .env 

# generate a new project key
php artisan key:generate

# connect to your local db. before doing so, please create a new db first, and change the connection parameters accordingly.
# open up the .env file in a text editor and change the following parameters according to your setup

# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=homestead
# DB_USERNAME=homestead
# DB_PASSWORD=secret

# if you are using homestead box, the only thing you need to change is the DB_DATABASE and point it to the new db you just created.

# install project dependency
composer update

# run db migration
php artisan db:migrate

# run db seed
php artisan db:seed

```

- Now the project should be able to run and you can test it by visiting the url `laravel.dev` via the browser on your host machine.

## Code style

- All API routes should be placed in side `/routes/api.php` while all web routes should be placed in `/routes/web.php`
- All API controller return should be using the custom-defined macro defined in `/app/Providers/ResponseServiceProvider.php`, this will be the api standard between the backend and the frontend.
- Try to use resource routes as much as you can.
- Whenever there is a db change, please write a new migration together with your seeder.
- Try to write test before your actual implementation of a specific function/method, this will give you a better view of what you want to achieve.