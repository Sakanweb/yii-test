INSTALLATION
------------

### Clone project from repository

~~~
git clone https://github.com/Sakanweb/yii-test.git
~~~

~~~
cd yii-test
~~~

~~~
composer install --optimize-autoloader --no-dev
~~~

~~~
composer update
~~~


### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```
### Migration
~~~
yii migrate --interactive=0
~~~

### Notice
```
    use this path for the domain connection
    path/to/root/yii-test/web
```