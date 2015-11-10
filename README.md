SlimRedTwig
===========
[![Unit Test Status](https://travis-ci.org/kiswa/SlimRedTwig.svg?branch=master "Unit Test Status")](https://travis-ci.org/kiswa/SlimRedTwig)

### This is no longer maintained. If anyone wants to take over, please let me know.

A skeleton application bringing together [Slim Framework](http://www.slimframework.com/), [RedbeanPHP](http://www.redbeanphp.com/), and [Twig](http://twig.sensiolabs.org/) templates into the MVC pattern. It also has [jQuery](http://jquery.com/) and [Bootstrap](http://getbootstrap.com/) baked in for an easier start with responsive design.

User Login/Register System
--------------------------

SlimRedTwig also has a simple user system that you can use as-is, update it as a base for your own system, or remove it completely. This also provides examples for the MVC setup used, as well as testing with [PHPUnit](http://phpunit.de/). Which reminds me...

Tests
-----

Unit tests are run with PHPUnit. The `Tests` directory contains them all, and there is a shell script to run them in the `app` directory. It's as easy as `./TestRunner`!

Getting Started
===============

You will need to make sure your server is serving the `public` directory, and that `cache` is writable by the server. An example for Apache would be:

    Alias /srt "path/to/SlimRedTwig/public"
    <Directory "path/to/SlimRedTwig/public">
        AllowOverride All
        Options All
        Require all granted
    </Directory>

You would then be able to access the app at `http://yourhost/srt`. Don't forget to update the `.htaccess` file in the `public` directory to match the base url to the alias you setup for your app.

Questions, Ideas, or Problems?
=============================

Let everyone know on the [GitHub Issues](https://github.com/kiswa/SlimRedTwig/issues) for SlimRedTwig.

I welcome contributions as well; just submit your pull request for review!

License
=======

SlimRedTwig is released under the MIT License, see LICENSE for details.
