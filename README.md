# Symfony project: sondage site

## Short presentation
Little website for surveys

## Project environment
OS: Debian 10  
PHP : version PHP 8.0.3  
Database: MySQL  

## Run Project
### Create database
Two solutions:  
#### 1: 
 * symfony console app:clean-db
#### 2:
 * symfony console doctrine:database:drop  
 * symfony console doctrine:database:create  
 * symfony console doctrine:migrations:migrate  
### Run php server
symfony serve -d
### Run maildev
If you want use register fonctionality. You need maildev  
Install maildev: npm install -g maildev  
Run maildev: maildev --hide-extensions STARTTLS  


## Contributor
Erick Poix \
Arthur Salengro