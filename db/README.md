# PHPHT DB

Create the PHPHT schema like this:

```sh
cd db
sqlite3 phpht.db
```

Make sure that .db file is WRITABLE by the CGI. Most likely www-data. Do
this. Yes, remember to give www-data access to the directory:

```sh
chgrp www-data db
chmod g+w db
chgrp www-data db/phpht.db
chmod g+w db/phpht.db
```

phpht2 schema relies upon and auth schema. Make sure that's built first.

e.g.: in SQLITE create user tables. Paste the auth schema in first.

Then paste the phpht2 expenses and domains tables in next.

## Shortcut

You could even just do this for greater expediency:

```sh
cd db
sqlite3 phpht.db < create_auth.sql
sqlite3 phpht.db < create_phpht.sql
```

## Agregate Functions

### Tabulate Expenses by Date

SELECT SUBSTR(date,1,10), SUM(amount) FROM expenses WHERE date <> '' GROUP BY SUBSTR(date,1,10);
