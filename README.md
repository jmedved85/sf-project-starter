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

2. Create a `.env.local` file and configure your database connection:

    ```dotenv
    DATABASE_URL=mysql://root:pass1234@mysql-sf_project_starter:3306/sf-project-starter_dev
    APP_ENV=dev
    APP_DEBUG=true
    ```

3. Create the database and run migrations:

    ```bash
    docker compose exec php php bin/console doctrine:database:create
    docker compose exec php php bin/console doctrine:migrations:migrate
    ```

4. Insert dev users:
    These are just example credentials and passwords are hashed using `bin/console security:hash-password` command.

    ```bash
    DEFAULT ADMIN USER
    username: admin@net.com
    password: admin1234
    ```

    ```sql
    INSERT INTO `sf-project-starter_dev`.`user` (`id`, `email`, `user_name`, `password`, `roles`, `first_name`, `last_name`, `active`) VALUES (1, 'admin@net.com', 'admin', '$2y$13$woWveCWpnhEiWPirdbvZu.nBRaKujD07uaFiJhkI/eEtQs5z9S36e', '["ROLE_ADMIN"]', 'Admin', 'User', 1);
    ```

    ```bash
    DEFAULT USER
    username: user@net.com
    password: user1234
    ```

    ```sql
    INSERT INTO `sf-project-starter_dev`.`user` (`id`, `email`, `user_name`, `password`, `roles`, `first_name`, `last_name`, `active`) VALUES (2, 'user@net.com', 'user', '$2y$13$Yfbvi3rzhcRV4Y3Adw4q3ekiq4R01p0n.tEIpwK7ls7bdVivmHu4e', '["ROLE_USER"]', 'Joe', 'Doe', 1);
    ```

5. Start the Docker containers:

    ```bash
    sh up.sh
    ```

6. Run:

    ```bash
    composer install
    sh asset-map-compile.sh
    ```

7. Open your browser and navigate to `http://localhost:8090` or `http://localhost:8090/login`.

## Running Tests

To run the tests, use the following command:

    ```bash
    ./vendor/bin/phpunit
    ```

## Notes

For more instructions check additional notes in `notes.txt` text file.

## License

This project is licensed under the MIT License.

## Acknowledgements

- [Symfony](https://symfony.com/)
- [Bootstrap](https://getbootstrap.com/)
- [Doctrine](https://www.doctrine-project.org/)