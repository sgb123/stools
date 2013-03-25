*******************
Adwords WM4D WEB Application
*******************

*******************
Setup
*******************

Specify base url in file application/config/config.php

    $config['base_url']	= '';

Replace oauth customer key and secret in `application/libraries/adwords_lib.php` file

    $_oauth_consumer_secret = 'anonymous';
    $_oauth_consumer_secret = 'anonymous';

Specify AdWords account credentials in `auth.ini` file

    email = ''
    password = ''
    userAgent = ''
    developerToken = ''
    clientId = ''

    [OAUTH2]
    client_id = ''
    client_secret = ''