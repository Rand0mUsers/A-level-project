# A-level project
My Computing A-level project - a website to aid teaching the OCR D1 maths module.

## Platform
The project was originally designed on a Debian/Apache/PHP 7/MariaDB stack. 

## Setup
The database.sql file contains the database structure and test data. It can be imported with mysql d_one < database.sql  
All the student and teacher accounts have the same password of Password123

To set up the web server, you need to copy all the other files in the repo directly to /var/www, leaving a directory structure as follows:

* /var/
  * www/
    * config.php
    * html/
      * css/
      * errors/
      * ...
      * addteacher.php
      * banner.php
      * ...

Note that the config file provided has been sanitised to remove the database login details and the reCAPTCHA secrets - the reCAPTCHA keys provided enable a test mode.
