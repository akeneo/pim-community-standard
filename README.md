Akeneo PIM Community Edition
============================

Welcome to Akeneo PIM.

This document contains information on how to download, install, and start using Akeneo PIM.

Important Note: this application is not production ready and is intendant for evaluation and development only!

Requirements
------------

Akeneo PIM requires Symfony 2, Doctrine 2 and PHP 5.3.3 or above.

Installation instructions
-------------------------

### Using Composer

As both Symfony 2 and Akeneo PIM use [Composer][2] to manage their dependencies, this is the recommended way to install Akeneo PIM.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    $ curl -s https://getcomposer.org/installer | php

Clone Akeneo PIM project with:

    $ git clone git@github.com:akeneo/pim-community-standard.git

Now, you can go to your pim project directory.

Install Akeneo PIM dependencies with composer. If installation process seems too slow you can use "--prefer-dist" option.
Don't forget to create your database before launching this script.

    $ php composer.phar install

Then initialize application with install script:

    $ ./install.sh

Create your Virtual host
------------------------

Go to /var/www and create a symbolic link to your pim web directory:

    /var/www$ sudo ln -s ~/git/pim-community-standard/web akeneo-pim.local

Then, go to your apache sites-available directory (`/etc/apache2/sites-available`) 
and create your virtual host `akeneo-pim.local` with the following lines:

```
<VirtualHost *:80>
    ServerName akeneo-pim.local

    DocumentRoot /var/www/akeneo-pim.local
    <Directory /var/www/akeneo-pim.local/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/akeneo-pim_error.log

    # Possible values include: debug, info, notice, warn, error, crit, alert, emerg.
    LogLevel warn
    CustomLog ${APACHE_LOG_DIR}/akeneo-pim_access.log combined
</VirtualHost>
```

Then add your virtual host to the apache enable sites:

    $ sudo a2ensite akeneo-pim.local

Now, you just have to add your host to hosts file `/etc/hosts`:

```
127.0.0.1 localhost akeneo-pim.local
```

And relaunch apache server.

After this step you can login as application administrator using user name "admin" and password "admin".

Checking your System Configuration
-------------------------------------

Before starting to code, make sure that your local system is properly
configured for a Symfony application.

Execute the `check.php` script from the command line:

    php app/check.php

If you get any warnings or recommendations, fix them before moving on.


[1]:  http://symfony.com/doc/2.1/book/installation.html
[2]:  http://getcomposer.org/
