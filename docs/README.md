# PHPHT

## Installation

 1. Make sure **composer** is installed
 1. Clone to the folder of your choice (e.g. *your_folder_name*)
 1. On the command line run ```composer install```
 1. Copy /config/config.sample to /config/config.ini
 1. Create the DB (go to **/db/README.md** for instructions)
 1. Make sure folder permissins are good:
    1. Try: ```sudo chgrp -R www-data [your_folder_name]```

### Configure the ```config.ini``` file

As stated above, copy /config/config.sample to /config/config.ini

Modify the config.ini file with at least the below:

 1. Optionally set the **appname** to the title of your app 
    1. This will be what shows up on the browser's tab too
 1. Set the **baseurl** setting to the folder off the web server's document root where your folder is
    1. By default the folder is **/** but you should change this (e.g. */your_folder_name/*)
 1. Set the **dblocation** setting to the folder and filename of the SQLite DB file you created for your app
    1. By default the dblocation is **db/phpht.db** (e.g. **db/your_db.db**)
    1. Note that there's no leading slash (**/**) for this setting

### Configuring Web Server

The most important note is to set up *rewrite* on your web server.

#### Lighttpd

Here's how you configure rewrite in **Lighttpd**.

Edit the 10-rewrite.conf file to something like this:

Change ```[your_folder_name]``` to the name of your folder - excluding the document root (which is probably ```/var/www/html```)

```
# /usr/share/doc/lighttpd/rewrite.txt
# http://redmine.lighttpd.net/projects/lighttpd/wiki/Docs_ConfigurationOptions#mod_rewrite-rewriting

server.modules += ( "mod_rewrite" )

url.rewrite-once = (
   "^/[your_folder_name]/([^\?]*)$" => "/[your_folder_name]/index.php",
   "^/[your_folder_name]/[^?]*(?:(\?)(.*))?$" => "/[your_folder_name]/index.php$1$2"
 )
```

Enable the rewrite module by doing: ```sudo lighty-enable-mod rewrite```

#### Apache

Here's how you configure rewrite in **Apache**.

#### NGINX

Here's how you configure rewrite in **NGINX**.

## Testing

