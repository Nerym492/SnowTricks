# Snowtricks

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/4f3aab542fbc472782c54a5208eca4e9)](https://app.codacy.com/gh/Nerym492/SnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

Project 6 of my application developer course - PHP/Symfony on [Openclassrooms](https://openclassrooms.com/).\
Creation of a website presenting snowboard tricks using the symfony framework.

## Informations

*   Symfony 6.3.1
*   PHP 8.2

## Installation

1.  Open a Terminal on the server root or localhost (git bash on Windows).
2.  Run the following command, replacing "FolderName" with the name you want to give to the Project :
    ```sh
    git clone https://github.com/Nerym492/SnowTricks FolderName
    ```
3.  Install Symfony CLI (https://symfony.com/download), composer (https://getcomposer.org/download/) and
    nodeJs (https://nodejs.org/en)
4.  Create an .env.local file at the root of your project.  
    Copy the following lines and complete them according to your database / mailer :\
    MAILER_DSN=sendinblue+api://**yourKey**@default  
    DATABASE\_URL="mysql://**databaseUser**:**password**@127.0.0.1:3306/**databaseName**?serverVersion=8&charset=utf8mb4"
    <br>  
    If you are not using [sendinblue](https://app.brevo.com/) to send mails : https://symfony.com/doc/current/mailer.html#using-built-in-transports

5.  Install the project's back-end dependencies with Composer :
    ```sh
    composer install
    ```
6.  Install the project's front-end dependencies with npm :
    ```sh
    npm install
    ```
7.  Create assets build with Webpack Encore :
    ```sh
    npx encore prod
    ```
8.  Launch wamp, mamp or lamp depending on your operating system.
9.  Create the database :
    ```sh
    php bin/console doctrine:database:create
    ```
10. Create database tables by applying the migrations :
    ```sh
    php bin/console doctrine:migrations:migrate
    ```
11. Add a base dataset by loading the fixtures :
    ```sh
    php bin/console doctrine:fixtures:load
    ```
12. Start the Symfony Local Web Server :
    ```sh
    symfony server:start
    ```
13. The site is now ready to use !
