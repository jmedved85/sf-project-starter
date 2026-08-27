# SF Project Starter

This is a starter installation for full-stack project development with the latest Symfony (version 7). 
Backend is run by Symfony and Doctrine components.
For frontend it uses Twig and Bootstrap and some custom CSS and JavaScript and is set also for optional use of Stimulus/Turbo for better and one-page performance.

This open-source project is intended for use in the development of my full-stack projects and is actively maintained and improved. It uses default Symfony components as well as some recommended by Symfony, and I will follow Symfony's recommendations as much as possible in the future.

## Features

- User administration
- Display flash messages for actions
- Responsive design with Bootstrap

## Requirements

- PHP 8.2 or higher
- Composer
- Symfony CLI
- MySQL
- Docker and Docker Compose

## Prerequisites

Ensure you have the following installed on your machine:

- Docker
- Docker Compose
- Composer

## Docker Installation

- Check `docker/docker-notes.txt` file for more info

1. Build and start the containers:

    ```bash
    docker compose up -d --build
    ```

2. Install dependencies:

    ```bash
    docker compose exec php composer install
    ```

3. Create a `.env.local` file and configure your database connection:

    ```dotenv
    DATABASE_URL=mysql://root:pass1234@mariadb-sf_project_starter:3306/sf-project-starter_dev
    APP_ENV=dev
    APP_DEBUG=true
    ```

4. Create the database and run migrations:

    ```bash
    docker compose exec php php bin/console doctrine:database:create
    docker compose exec php php bin/console doctrine:migrations:migrate
    ```

5. Insert dev users and load fixtures:
    These demo users are created through fixtures and their passwords are hashed automatically.

    ```bash
    docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
    ```

    ```bash
    DEFAULT ADMIN USER
    username: admin@net.com
    password: admin1234
    ```

    ```bash
    DEFAULT USER
    username: user@net.com
    password: user1234
    ```

6. Install importmap:

    ```bash
    docker compose exec php php bin/console importmap:install
    ```


7. Run quality assurance checks:

    ```bash
    sh qa.sh
    ```

8. Start the Symfony server:

    ```bash
    sh up.sh
    ```

9. Open your browser and navigate to `http://localhost:8080` or `http://localhost:8080/login`.

## Running Tests

To run the tests, use the following command:

    ```bash
    sh test.sh
    ```

## Notes

For more instructions check additional notes in `docker/docker-notes.txt` text file.

## License

This project is licensed under the MIT License.

## Acknowledgements

- [Symfony](https://symfony.com/)
- [Bootstrap](https://getbootstrap.com/)
- [Doctrine](https://www.doctrine-project.org/)