# Scheduler Developer Guide

## Prerequisites

**Required**

- [Symfony CLI](https://symfony.com/download)
- [Mysql5.7](https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-20-04)

## Getting started

1. Configure database:
   1. `sudo mysql` OR `mysql -u root -p`
   2. `CREATE DATABASE scheduler;`
   3. `CREATE USER 'symfony'@'localhost' IDENTIFIED BY password;`
   4. `GRANT ALL PRIVILEGES ON scheduler.* TO 'symfony'@'localhost';`
2. Clone repository: `git clone git@github.com:WiseBadgers/Scheduler.git`
3. Run `composer install` to install php dependencies
4. Open project in IDE and configure `.env.local` file:
   1. Create `.env.local` file
   2. Input this line into the `.env.local`: 
      `DATABASE_URL="mysql://symfony:password@127.0.0.1:3306/scheduler?serverVersion=5.7&charset=utf8mb4"`
5. Go to project root: `cd scheduler`
6. Run database migrations: `symfony console doc:mig:mig`
7. Start symfony server: `symfony serve -d`
8. You can access api: `http://127.0.0.1:8000/api`