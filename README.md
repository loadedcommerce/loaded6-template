# loaded6-template
Template development for Loaded 6 CE 


How to setup the project

First clone the full Website repo into your localhost. Then clone this Template repo into a different location.

    Install xCode
    Install Home Brew
    Install Grunt

Configurating on your machine

In terminal run the follow command and then look for a file called package.son.json and rename is to package.json (The file may already be called this):

npm init

Make sure these values are inside the package.json file and they are correct:

"theme_name": "theme_folder_name",
"theme_path": "local_dev_theme_path",

Now add Grunt to the project by running:

npm install grunt --save-dev

Now install SASS by running:

npm install grunt-contrib-sass --save-dev

Now install Grunt watch:

npm install grunt-contrib-watch --save-dev

Now install Grunt Copy:

npm install grunt-contrib-copy --save-dev
Workflow

Before you do any work make sure you have terminal open and you have run this command:

grunt watch
