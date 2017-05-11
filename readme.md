## Environment

This project is developed using Laravel 5.4 framework

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
- If you can't find the project folder when you ssh into it, try to run `vagrant provision` and `vagrant halt` to shutdown vm, and repeat the above step. 

## Project Setup

Note: All following command should be run inside your homestead box. Make sure to ssh into it first.

- Run the following script *in* your project root directory. eg. In the configuration file described above, run scripts under `/Code/yechefBack`

```
# set up the project configuration file since its being git ignored
cp .env.example .env 

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

# generate a new project key
php artisan key:generate

# run db migration
php artisan migrate

# run db seed
php artisan db:seed

# or to run both at the same time
 php artisan migrate --seed


# generate passport token
php artisan passport:keys

# Generate Oauth password grant client credential
php artisan passport:client --password
# use the result to env param:
# PASSWORD_CLIENT_ID
# PASSWORD_CLIENT_SECRET


```

- Now the project should be able to run and you can test it by visiting the url `laravel.dev` via the browser on your host machine.

## Code style

- All API routes should be placed in side `routes/api.php` while all web routes should be placed in `routes/web.php`
- All API controller return should be using the custom-defined macro defined in `app/Providers/ResponseServiceProvider.php`, this will be the api standard between the backend and the frontend.
- Try to use resource routes as much as you can.
- Whenever there is a db change, please write a new migration together with your seeder.
- Try to write test before your actual implementation of a specific function/method, this will give you a better view of what you want to achieve.
