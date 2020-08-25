# PHPHT DB

Create the PHPHT schema like this:

```sh
cd db
sqlite3 [your_db_name].db
```

By default PHPHT will try to find a database named *phpht.db*.

If you name it something else, simply specify that database name in the *config.ini* file.

Make sure that .db file is WRITABLE by the CGI. Most likely www-data. Do
this. Yes, remember to give www-data access to the directory:

```sh
chgrp www-data db
chmod g+w db
chgrp www-data db/[your_db_name].db
chmod g+w db/[your_db_name].db
```

## Load Delight Schema First

PHPHT uses **Delight** as the authentication library so you must inject the Delight DB schema into your database.

This is usually pretty expedient:

```sh
cd db
sqlite3 [your_db_name].db < create_auth.sql
sqlite3 [your_db_name].db < create_phpht.sql
```

## Agregate Functions

### Tabulate Expenses by Date

SELECT SUBSTR(date,1,10), SUM(amount) FROM expenses WHERE date <> '' GROUP BY SUBSTR(date,1,10);
