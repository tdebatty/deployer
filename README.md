# Deployer
Simple deployer for GIT projects

## Installation

### App

```
git clone https://github.com/tdebatty/deployer.git
```

```cp deployer/env.example deployer/.env```


```
APP_ENV=production
APP_DEBUG=false
APP_LOG_LEVEL=error
APP_URL=http://deployer.web-d.be:8080

DEPLOY_ROOT=/home/www
```

```composer install```

```php artisan key:generate```

If you are using a sqlite database:

```touch storage/db.sqlite```

```php artisan migrate```

php artisan db:seed

### Apache

```sudo nano /etc/apache2/sites-available``` :

```
<VirtualHost *:8080>
        ServerName deployer.be
        ServerAlias deployer.web-d.be

        ServerAdmin webmaster@localhost

        DocumentRoot /home/deployer/public

        <Directory /home/deployer/public/>
                AllowOverride All
                Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/deployer.error.log
        CustomLog ${APACHE_LOG_DIR}/deployer.access.log combined
</VirtualHost>
```

```sudo a2ensite deployer```
```sudo service apache2 restart```

mkdir -p deployer/storage/framework/sessions
sudo chgrp -R www-data deployer
sudo chmod -R g+w deployer/storage/

### Jobs queue

nano .env

QUEUE_DRIVER=database

admin@example.com
admin
