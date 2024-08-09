# Cleema BackEnd Laravel / filament
## Requirements
**Mandatory**
* Php 8.1 +
* Composer
* Node + npm

**Optional**
* dev script runner for easy command use [Dev Script Runner](https://github.com/sandstorm/dev-script-runner)

## Initial setup
* rename `.env.example` to `.env`
* add `IOS_LOCAL_ACCESS_TOKEN` to .env (Bitwarden Cleema Collection IOS Access Token)
* run `dev setup` or `composer install`and `npm install`
* run `php artisan key:generate` to generate laravel app key
* run `dev start` to start the docker containers for the first time
* init DB: [Use old database dump](#Use-old-database-dump)

## Link public storage
* automatically executed when running `dev setup`
* can be manually executed with `dev linkStorage` or `vendor/bin/sail artisan storage:link`

## Start development server
* run `dev start` or `./vendor/bin/sail up -d`
* run `dev migrate` or `./vendor/bin/sail migrate`
* Local development server is running on http://localhost/admin/login
* after database seed: 
  * Login to admin terminal with following credentials:
      * Name: admin
      * email: admin@sandstorm.de
      * password: password
  * Login to App with following credentials:
    * Username: Sandstorm
    * password: password

## Proxy
To access the proxy, go to `localhost:8090`

## Testing

We use the build in testing setup from Laravel - [Documentation](https://laravel.com/docs/11.x/testing)

#### Create tests
* use `php artisan make:test [NAME]` to create a new feature test
* use `php artisan make:test [NAME] --unit` to this command to create a unit test

#### Writing tests
We use Pest to run our tests.
Docs:
* [Laralvel (setup)](https://laravel.com/docs/11.x/testing)
* [PEST (setup, fuctional)](https://pestphp.com/)
* [Livewire (Basis for Filament) (UI)](https://livewire.laravel.com/docs/testing)
* [Filament forms (UI)](https://filamentphp.com/docs/3.x/forms/testing)
* [Filament tables (UI)](https://filamentphp.com/docs/3.x/tables/testing)

#### Run tests

Console:
run `dev test` to execute tests

#### Run tests from only one file
Console:
`vendor/bin/sail artisan test tests/[Path]/[YourTestClass].php`

#### Run only one single test function
Console
`sail artisan test --filter [test_yourTestFunction]`

IDE:
TODO


## Develompent links
* http://localhost:8080/admin - admin panel

## Database


#### Manual
* Connect to the Database (required variables in .env)
* Run the database dump sql on the database
* run `dev seed`
* run `dev migrate`

`dev migrate` must be run after `dev seed` as otherwise the SQL files throw errors.

#### Seeder setup
You can also use the database dump to seed your database. 
Save your DbDump in a new folder called `DBDumps` and call is `cleema.sql` to match the path in [the seeder](database/seeders/DatabaseSeeder.php)
now every time your run `dev seed` the database dump will be run on your database.
Note that you should still run `dev migrate` once AFTER running `dev seed`.
To create Development Admin Users, run `dev seedDev`.

Alternatively, can now also use `dev resetDB` to reset the Database aka. wipe, migrate, seed and seedDev the DB.

### Workflow

#### Create/change Model

[Documentation](https://laravel.com/docs/11.x/eloquent#generating-model-classes)

Models are Eloquent ORMs way of depicting you DB and can be found in app/Models

You can freely change models, if you change attributes (collums) you probably want to either 
- create a migration for that change 
- update an existing migration for that model

You can create a new Model with `php artisan make:model [NAME]`
Adding `--migration` or `-m` to automatically also create a migration for the model to use

#### Create/change migration

[Documentation](https://laravel.com/docs/11.x/migrations)

Migrations are currently only really required for tests but probably also prove usefull for migrating the old DB instead of using `updateDbDump`

Migrations are the way we update our DB schema and cna be found in database/migrations.
All migrations have their creation date in their name, which regulates the order in which they are executed

You can create new migrations with `php artisan make:migration [NAME]`

Changing old migrations is risky as newer migration might be based on them, always check for errors when doing so


## Deploy Staging

- run pipeline and run staging-deployment manually
- go to rancher pod and execute `php artisan db:wipe`
- setup [sku](https://sandstorm-media.slack.com/archives/C05AVHJFU13/p1712042384349109?thread_ts=1712041838.963499&cid=C05AVHJFU13) if you haven't already
- use sku to connect to namespace `sku ns cleema-app-staging`
- use beekeeper to connect to database with terminal `sku mysql beekeeper`
- run `OldDbDump.sql` in beekeeper by writing a new query and copy and paste
- run `updateDbDump.sql` in beekeeper by writing a new query and copy and paste
- go to rancher pod and execute
  - `php artisan migrate`
  - `php artisan db:seed --class=ShieldSeeder`
  - `php artisan db:seed --class=MigrateOldAdminRoles`
  - `php artisan storage:link`

Now staging should be running at https://cleema-staging.cloud.sandstorm.de/admin .

### Add super admin user to staging
- go to rancher pod and execute
  - `php artisan make:filament-user`, then insert your name (which doesn't show in db, so who cares), insert `admin@sandstorm.de` and insert the password for it from Bitwarden
      - retrieve user id from database for admin@sandstorm.de: `php artisan db` -> `select id from admin_users where email = "admin@sandstorm.de";`
      - exit db
      - `php artisan shield:super-admin --user=id`

If you want to add a author or editor user, create them in the filament interface and use the passwords in Bitwarden so everyone can use them.


If you want to have all images of existing projects, challenges etc., ask Johannes, Justin or Max to send you the images. 
Then put all images into `storage/app/public/uploads`.

## Deploy Production

- create Tag in Gitlab, this will run pipeline and deploy
- If there are new migrations:
  - go to rancher pod and execute
      - `php artisan migrate`
      - `php artisan db:seed --class=StagingSeeder`
      - `php artisan db:seed --class=MigrateOldAdminRoles`
      - `php artisan storage:link`

Now staging should be running at https://cleema-staging.cloud.sandstorm.de/admin .


## Running schedules (!IMPORTANT)
### Development
- `./vendor/bin/sail artisan schedule:work`

### Production
- prob. with cron job on the server which executes php artisan schedule:run
- help:
  - https://laravel.com/docs/10.x/scheduling#running-the-scheduler
  - https://stackoverflow.com/questions/67795649/how-can-i-configure-supervisor-and-scheduling-with-laravel-sail
  





