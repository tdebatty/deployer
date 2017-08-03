# Deployer
Simple deployer for GIT projects

## Installation

### User

adduser deployer www-data
ssh-keygen -t rsa

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

DEPLOY_ROOT=/var/www
```

```composer install```

```php artisan key:generate```

If you are using a sqlite database:

```touch storage/db.sqlite```

```php artisan migrate```

```php artisan db:seed```

### Apache

```sudo nano /etc/apache2/sites-available/deployer.conf``` :

```
<VirtualHost *:8080>
        ServerName deployer.be
        ServerAlias deployer.web-d.be

        ServerAdmin webmaster@localhost

        DocumentRoot /var/www/deployer/public

        <Directory /var/www/deployer/public/>
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

### Jobs and queue

Redis:
sudo apt-get install redis
nano .env
QUEUE_DRIVER=redis

Supervisor:
sudo apt-get install supervisor
sudo nano /etc/supervisor/conf.d/deployer.conf

```
[program:deployer]
user=deployer
command=php artisan queue:work --sleep=3 --tries=1 --daemon
directory=/home/www/deployer
process_name=queue_%(process_num)s
numprocs=4
stdout_logfile=/home/www/deployer/storage/logs/supervisord-%(process_num)s-stdout.log
stderr_logfile=/home/www/deployer/storage/logs/supervisord-%(process_num)s-stderr.log
autostart=true
autorestart=true
```

admin@example.com
admin
