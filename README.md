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

- PHP 8.4 or higher
- Composer
- Symfony CLI
- MySQL/MariaDB
- Docker and Docker Compose

Use the setup script for the fastest installation:

```bash
chmod +x setup.sh
sh setup.sh
```

The script will:

- create `.env.local` if it does not exist
- build and start the Docker containers
- install Composer dependencies
- clear cache and compile the asset map
- create the database if needed
- run Doctrine migrations
- load development fixtures
- install importmap assets

Check `docker/docker-notes.txt` file for more info.

If you prefer to install manually, follow the Docker steps in `setup.sh`.

## Default User

- username: user@net.com
- password: user1234

Run quality assurance checks:

    ```bash
    sh qa.sh
    ```

Start the Symfony server:

    ```bash
    sh up.sh
    ```

Open your browser and navigate to `http://localhost:8080` or `http://localhost:8080/login`.

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