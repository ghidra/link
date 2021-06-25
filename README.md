# link
***Link*** is a simple Delicious (del.icio.us) replacement. Meant as a simple central repository for bookmarking links. Another feature from Delicious that has been implement is **Tagging** of posted links.
![link screenshot](https://user-images.githubusercontent.com/5643219/123357944-8e774c80-d538-11eb-9609-5d550a86f83a.png)

# setup
Tested with:  
* PHP Version 7.3.19-1~deb10u1
* mysql  Ver 15.1 Distrib 10.3.27-MariaDB, for debian-linux-gnueabihf (armv8l) using readline 5.2

Have a mysql database named **link** ( this can be something else if desired * ).  
Duplicate or rename **/backend/mysql_login.ini.php** as **/backend/mysql_login.php**

Fill in the relavant fields for:
```php
$mysql_host = 'host'; // this might be simply 'localhost'
$mysql_user = 'user';
$mysql_pass = 'pass';
```

If you prefer to name your database something other than **link**, edit this line in **mysql_login.php**
```php
$mysql_database_name = 'link'; // rename link to whatever
```
The relevant tables can also be renamed is desired *.  
*Note: none of this renaming has been tested, but should theoretically work.

# todo
* Editing posted links
* Delete posted links
* Mark posted links as dead
* Automatic image linking to dress up link
