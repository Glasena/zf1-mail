# ZF1 Mail — Lab

A study lab focused on **RabbitMQ** integration and **refactoring** practice in a legacy PHP application.

## Goals

- Integrate RabbitMQ as a mail sending queue in a ZF1 application
- Practice test-driven refactoring (SRP, dependency injection, interfaces)
- Write unit tests with mocks for decoupled code

## Stack

| Layer       | Technology                    |
|-------------|-------------------------------|
| Language    | PHP 8.1+                      |
| Framework   | ZF1-Future ^1.23              |
| ORM         | Doctrine ORM ^2.17            |
| Migrations  | Doctrine Migrations ^3.7      |
| Queue       | RabbitMQ via php-amqplib      |
| Database    | MySQL 8.0                     |
| Web Server  | Nginx                         |
| Testing     | PHPUnit ^10.0                 |

## Mail Flow

```
[Form] → MailController → MailService::queueMail() → RabbitMQ (mail_queue)
                                                              ↓
                                                    SendMailCommand (worker)
                                                              ↓
                                               MailService::sendMail(DTO, mailer)
                                                              ↓
                                          ZendMailService + persist Mail entity
```

## Getting Started

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Build and start containers
make build
make up

# 3. Install PHP dependencies
make install

# 4. Run migrations
make migration-migrate
```

App available at [http://localhost:8080](http://localhost:8080).

## Commands

### Docker

| Command         | Description                        |
|-----------------|------------------------------------|
| `make up`       | Start containers                   |
| `make down`     | Stop containers                    |
| `make build`    | Rebuild containers                 |
| `make shell`    | Bash into PHP container            |
| `make db`       | MySQL CLI                          |
| `make logs`     | Follow container logs              |
| `make autoload` | composer dump-autoload -o          |

### Testing

| Command                  | Description                  |
|--------------------------|------------------------------|
| `make test`              | Run all test suites          |
| `make test-unit`         | Run Unit tests only          |
| `make test-integration`  | Run Integration tests only   |

### Migrations

| Command                    | Description                   |
|----------------------------|-------------------------------|
| `make migration-diff`      | Generate migration from diff  |
| `make migration-migrate`   | Apply pending migrations      |
| `make migration-status`    | Show migration status         |
| `make migration-rollback`  | Rollback latest migration     |

## Environment Variables

| Variable          | Description                   |
|-------------------|-------------------------------|
| `DB_HOST`         | Database host                 |
| `DB_USER`         | Database user                 |
| `DB_PASS`         | Database password             |
| `DB_NAME`         | Database name                 |
| `RABBITMQ_HOST`   | RabbitMQ host                 |
| `RABBITMQ_PORT`   | AMQP port (default: 5672)     |
| `RABBITMQ_USER`   | RabbitMQ user                 |
| `RABBITMQ_PASS`   | RabbitMQ password             |
| `MAIL_FROM`       | Sender email address          |

## Debugging

Xdebug is pre-configured for VS Code. Open the **Run & Debug** panel and start **Listen for Xdebug**.

Port: `9003` | IDE Key: `VSCODE`
