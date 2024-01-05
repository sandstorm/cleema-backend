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

}

function start(){
    vendor/bin/sail up -d
    dev migrate
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

# THIS NEEDS TO BE LAST!!!
# this will run your tasks
"$@"

