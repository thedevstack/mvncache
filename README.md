#mvncache

simple maven cache and proxy in php

## Use in apache

Put the script `src/mvncache.php` in the directory you want to be used as maven proxy.
Next to the script copy the following lines to `.htaccess`:

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^.*$ mvncache.php [L]

Make both files accessible for apache.

## Possible use in lighttpd
Put the script `src/mvncache.php` in the directory you want to be used as maven proxy.
Add the following line to your virtual host configuration:

    server.error-handler-404    = "/mvncache.php"

This path is relative to the value in `server.document-root`.