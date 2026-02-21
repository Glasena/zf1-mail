# ZF1 Future Skeleton

A modern PHP 8.1+ application skeleton built on top of [ZF1-Future](https://github.com/Shardj/zf1-future) — a maintained fork of Zend Framework 1 — with Doctrine ORM, Docker, and PHPUnit out of the box.

## Requirements

- Docker & Docker Compose
- Make

## Stack

| Layer       | Technology                   |
|-------------|------------------------------|
| Language    | PHP 8.1+                     |
| Framework   | ZF1-Future ^1.23             |
| ORM         | Doctrine ORM ^2.17           |
| Migrations  | Doctrine Migrations ^3.7     |
| Database    | MySQL 8.0                    |
| Web Server  | Nginx 1.25                   |
| Testing     | PHPUnit ^10.0                |
| Debugging   | Xdebug (VS Code ready)       |

## Getting Started

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Build and start containers
make build
make up

# 3. Install PHP dependencies
make install

# 4. Create the database schema
make doctrine-schema-create
```

The application will be available at [http://localhost:8080](http://localhost:8080).

## Project Structure

```
.
├── application/
│   ├── modules/          # MVC modules
│   │   └── default/
│   │       ├── controllers/
│   │       ├── models/
│   │       └── views/
│   ├── configs/
│   │   └── application.ini
│   ├── layouts/
│   ├── Bootstrap.php
├── config/               # Doctrine & CLI configuration
├── database/
│   └── migrations/
├── docker/
│   ├── nginx/
│   └── php/
├── public/
│   └── index.php         # Application entry point
├── tests/
│   ├── Unit/
│   └── Integration/
├── docker-compose.yml
├── Makefile
└── .env.example
```

## Available Commands

### Docker

| Command           | Description                         |
|-------------------|-------------------------------------|
| `make up`         | Start containers                    |
| `make down`       | Stop containers                     |
| `make build`      | Rebuild containers (no cache)       |
| `make restart`    | Restart all services                |
| `make install`    | Run `composer install` in container |
| `make shell`      | Open a bash shell in PHP container  |
| `make db`         | Open MySQL CLI                      |
| `make logs`       | Follow container logs               |
| `make ps`         | List running services               |

### Testing

| Command                  | Description                  |
|--------------------------|------------------------------|
| `make test`              | Run all test suites          |
| `make test-unit`         | Run Unit tests only          |
| `make test-integration`  | Run Integration tests only   |

### Doctrine Schema

| Command                       | Description             |
|-------------------------------|-------------------------|
| `make doctrine-validate`      | Validate entity mapping |
| `make doctrine-schema-create` | Create database schema  |
| `make doctrine-schema-update` | Update schema (force)   |
| `make doctrine-schema-drop`   | Drop schema (force)     |

### Migrations

| Command                    | Description                     |
|----------------------------|---------------------------------|
| `make migration-diff`      | Generate a new migration diff   |
| `make migration-migrate`   | Run pending migrations          |
| `make migration-status`    | Show migration status           |
| `make migration-rollback`  | Rollback the latest migration   |

## Adding a New Module

1. Create the module directory under `application/modules/<module-name>/`
2. Add `controllers/`, `models/`, `views/`, and `entities/` subdirectories
3. Create a `Bootstrap.php` extending `Zend_Application_Module_Bootstrap`
4. Doctrine will auto-discover entities in `*/entities` paths

## Environment Variables

| Variable          | Default       | Description               |
|-------------------|---------------|---------------------------|
| `APPLICATION_ENV` | `development` | App environment           |
| `DB_DRIVER`       | `pdo_mysql`   | Doctrine driver           |
| `DB_HOST`         | `mysql`       | Database host             |
| `DB_PORT`         | `3306`        | Database port             |
| `DB_USER`         |               | Database user             |
| `DB_PASS`         |               | Database password         |
| `DB_NAME`         |               | Database name             |
| `DB_CHARSET`      | `utf8mb4`     | Connection charset        |

## Debugging

Xdebug is pre-configured for VS Code. Open the **Run & Debug** panel and start the **Listen for Xdebug** configuration. Breakpoints will be hit automatically on incoming requests.

Port: `9003` | IDE Key: `VSCODE`

## License

MIT
