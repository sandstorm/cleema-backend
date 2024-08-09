#!/bin/bash

    ############################## DEV_SCRIPT_MARKER ##############################
    # This script is used to document and run recurring tasks in development.     #
    #                                                                             #
    # You can run your tasks using the script `./dev some-task`.                  #
    # You can install the Sandstorm Dev Script Runner and run your tasks from any #
    # nested folder using `dev some-task`.                                        #
    # https://github.com/sandstorm/Sandstorm.DevScriptRunner                      #
    ###############################################################################

set -e

##### TASKS #####

function setup(){
    composer install
    npm install
    vendor/bin/sail build
    dev linkStorage
}

function start(){
    vendor/bin/sail up -d
}

function seed(){
    vendor/bin/sail artisan db:seed --class=DatabaseSeeder
}

function seedDev(){
    vendor/bin/sail artisan db:seed --class=DevelopmentSeeder
}

function migrate(){
    vendor/bin/sail artisan migrate
}

function stop(){
    vendor/bin/sail stop
}

function down(){
    vendor/bin/sail down
}

function test() {
    vendor/bin/sail artisan test $1 $2 $3 $4
}

function linkStorage() {
    vendor/bin/sail artisan storage:link
}

function restart(){
    dev down
    dev start
}

function resetDB() {
    vendor/bin/sail artisan db:wipe
    dev migrate
    dev seed
    dev seedDev
}

# THIS NEEDS TO BE LAST!!!
# this will run your tasks
"$@"

