AddHandler x-httpd-php82 .php
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteRule ^(.*)$ public/$1 [L]
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} !^public [L,R=301]
</IfModule>
# php -- BEGIN cPanel-generated handler, do not edit
# This domain inherits the “PHP” package.
# php -- END cPanel-generated handler, do not edit