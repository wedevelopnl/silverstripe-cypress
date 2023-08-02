# Silverstripe Cypress
Helper module to allow cypress to be ran against a functional Silverstripe webiste
inside the context of an CI.

## Note!
This module might be usefull inside various CI configuration/implementations so feel free
to use it when applicable. But note that this module is designed specifically to run inside
our internal GitLab CI default configuration/setup.

We will **not** provide support or implement features that are not supported by our configuration.

## Tools
The module provides various tools to ease testing in a CI context.

### Generate TinyMCE configuration
Silverstripe will generate its TinyMCE configuration on the fly when requested. But when running a
seperate webserver container as service inside the GitLab CI these files are generated on the service
running php-fpm preventing the webserver from accessing it. We provide a build task which generates
the tinymce bundle files so they can be build into the webserver container during the build stage.

To run this task:
`./vendor/bin/sake dev/tasks/generate-tinyMCE-combined-task --no-database`