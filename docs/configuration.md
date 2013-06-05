How to setup Connections
========================

Local Client
------------

- Rename the config.php.example file with the params of the database connection

```php
$config = array(
	'host' => 'localhost', 
	'port' => '3306', 
	'user' => 'dbuser', 
	'password' => 'dbpassword', 
	'dbname' => 'dbemails_name',

	'email_host' => '74.32.45.201',
	'email_ssl' => 'tls',
	'email_port' => 587,
	'email_username' => 'noreply@example.com',
	'email_password' => 'email_pass',
	'email_from_email' => 'noreply@example.com',
	'email_from_name' => 'Testing - No Reply',

	'mock_test_mail' => false, 
);
```

The 'mock_test_mail' variable is to mockup the sent of email in dev/testing enviroments. If false, then send the email to the specified server. Otherwise, don't send the email (testing purposes)

Daemon
------

Besides configure the client, we must configure the deamon. Edit the senditd.php file and complete the variables:

```php
const SECONDS_BETWEEN_PROCESSES = 5;

/**
 * Default Timezone for logging purposes
 * @var String
 */
const DEFAULT_TIMEZONE = 'America/Buenos_Aires'; 
const DEFAULT_TIME_FORMAT = 'd/m/Y H:i:s';
```

And run the daemon. 

###Ubuntu (upstart)###

If upstart is installed, then is convenient to use it. Copy and edit the senditd.conf.ubuntu file into /etc/init/senditd.conf and complete where the senditd executable is located, for example:

```sh
description "Sendit! Server"
author      "Nicolás Díaz País - http://www.nicodp.com.ar"

# used to be: start on startup
# Wait until all the mounts are executed and stop at shutdown
start on started mountall
stop on shutdown

# Automatically Respawn:
respawn
respawn limit 99 5

script
    # Not sure why $HOME is needed, but we found that it is:
    export HOME="/root"

    exec /where/sendit/bin/senditd >> /var/log/senditd.log 2>&1
end script
```

And finally, can start and stop the server easily by:
```sh
start senditd
stop senditd
```


###Fedora###

For Fedora OS, copy and edit senditd.service file to /usr/lib/systemd/system and complete the with executable:

```sh
[Unit]
Description=Sendit! Server
After=syslog.target network.target

[Service]
Type=simple
ExecStart=/where/sendit/bin/senditd

[Install]
WantedBy=multi-user.target
```

Make symbolic link

```sh
# cd /etc/systemd/system/
# ln -s /lib/systemd/system/senditd.service
```

Make systemd take notice of it
```sh
# systemctl daemon-reload
```

Activate a service immediately
```sh
# systemctl start senditd.service
```

Enable a service to be started on bootup
```sh
# systemctl enable senditd.service
```



