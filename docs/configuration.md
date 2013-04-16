### How to setup Connections

- Rename the config.php.example file with the params of the database connection

```php
$config = array(
	'params' => array(
		'host' => 'localhost', 
		'port' => '3306', 
		'user' => 'username', 
		'password' => 'password', 
		'dbname' => 'database'
	)
);
```