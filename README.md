Haggard
=======

Digital Task Board Made In Oulu With Passion! -> Web App, LAMP (Linux, Apache, MySQL, PHP) 

the service requires LAMP/WAMP server setup on Linux/Windows machine.
Tested as working on Ubuntu desktop(14.10) & server(14.04 LTS) with Apache2, Php5.5.

###Web application setup in 10 steps:

1. git clone project to your ubuntu home dir
2. Execute bash script **setup.sh**
3. MySQL config: create *haggard* user and *haggard* database (easily done with *phpmyadmin* extension)
4. Import db schema **./doc/SQL/haggard.sql** into *haggard* database
5. Create/insert a *admin* user entry into *user* table, set type to *SYSTEM_ADMIN*, password: sha1 hash value.
6. Configure db username & password in /var/www/html/haggard/base/config/**database.config.php** (setup.sh default)
7. Configure global params in /var/www/html/haggard/base/config/**global.config.php** - optional step at initial setup
8. Execute **sudo ./manage.sh board_name** at www root directory (/var/www/html/haggard)
9. Open http://your_haggard_url/board_name and input initial board data, press Create.
10. When logged as **admin** (*SYSTEM_ADMIN* type in db), you can configure board, add users grant permissions, etc.
