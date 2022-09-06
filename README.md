

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/eec9b433c6364ff6b9f2e63d51481c89)](https://www.codacy.com/gh/jobafa/OC-Symfony-P7-BileMo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=jobafa/OC-Symfony-P7-BileMo&amp;utm_campaign=Badge_Grade)

# OC-Symfony-P7-BileMo

Developing a REST API to provide mobile phones catalog.

## Environement
* Symfony 5.4
* Composer 2.3.8
* PHP 7.3.33
* MySQL 5.7.36

## Installation
To clone the project run the following command:
```
git clone https://github.com/jobafa/OC-Symfony-P7-BileMo.git
```
Install dependancies :
```
composer install
```
## DataBase
Modify the .env file for the database connection.
```
DATABASE_URL=mysql://root:@127.0.0.1:3306/the-name-of-your-database
```
Create the database:
```
php bin/console doctrine:migrations:migrate
```
Create the database structure:
```
php bin/console doctrine:migrations:migrate
```
Load fictive data:
```
php bin/console doctrine:fixtures:load
```
## To test the endspoints, either run :
```
php bin/console server:run
```
OR

use Postman

## API Documentation  
```
http://127.0.0.1:8000/api/doc
```
## Clients accounts 
```
ROLE_ADMIN :
{
  "username": "admin@apiclientone.com",
  "password": "adminpassword"
}

ROLE_USER :
{
  "username": "user@apiclientone.com",
  "password": "userpassword"
}
```
