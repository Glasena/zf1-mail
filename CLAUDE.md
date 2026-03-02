# ZF1 Mail — Claude Notes

## Stack
- **Framework:** Zend Framework 1 (via `shardj/zf1-future`) + PHP 8.x
- **ORM:** Doctrine ORM with PHP Attributes (not XML/YAML)
- **Migrations:** Doctrine Migrations
- **Infra:** Docker Compose (nginx + php + mysql:8.0)
- **App URL:** http://localhost:8080

## Directory Structure (PascalCase)

```
application/
  Bootstrap.php                        # Global bootstrap — initializes Doctrine, routes, dispatcher, ACL
  Controller/Dispatcher.php            # Custom dispatcher bridging ZF1 conventions with PSR-4 namespaced controllers
  Modules/
    Default/
      Bootstrap.php
      Controllers/
      Entities/
        AbstractEntity.php             # Base entity: id, createdAt, updatedAt + lifecycle callbacks
    Mail/
      Bootstrap.php
      Controllers/MailController.php
      DTOs/SendMailDTO.php
      Entities/Mail.php
      Forms/Mail.php
      Services/MailService.php
      Views/scripts/mail/index.phtml   # mapped from indexAction()
  configs/
    application.ini                    # Main ZF1 config
    routes.php                         # Manual routes (array)
  plugins/
    Acl.php                            # Access control plugin
config/
  doctrine/
    doctrine-orm.php   # Unified CLI: ORM + Migrations (use this for everything)
    migrations.php     # Migrations config (paths, version table)
    load-env.php
database/
  migrations/          # Namespace: Database\Migrations
```

## Namespaces
```
Application\Modules\{Module}\Controllers\{Name}Controller
Application\Modules\{Module}\Entities\{Name}
Application\Modules\{Module}\Forms\{Name}
Application\Modules\{Module}\Services\{Name}Service
Application\Modules\{Module}\DTOs\{Name}DTO
```
- Module folders are **PascalCase** on disk and in namespace
- `appnamespace = "Application"` defined in `application.ini`

## Custom Dispatcher
`Application\Controller\Dispatcher` bridges ZF1 (expecting `Mail_MailController`) with
PSR-4 (`Application\Modules\Mail\Controllers\MailController`). Registered in Bootstrap via `_initDispatcher()`.
**Do not remove.**

## Routes
Defined in `application/configs/routes.php` as an array, registered in Bootstrap via `_initRoutes()`.
```php
'mail' => ['route' => '/mail', 'module' => 'Mail', 'controller' => 'mail', 'action' => 'index'],
```
To add a route: edit `routes.php` and add a new entry.

## ZF1 Patterns

### Controller
```php
namespace Application\Modules\Mail\Controllers;

class MailController extends Zend_Controller_Action
{
    public function init(): void
    {
        $em = Zend_Registry::get('doctrine.em');
        $this->myService = new MyService($em);
    }

    public function indexAction(): void
    {
        $form = new MyForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            // process POST
            $this->view->message = 'Result';
        }
    }
}
```

### Form
```php
namespace Application\Modules\Mail\Forms;

class Mail extends Zend_Form
{
    public function init(): void
    {
        $this->setMethod('post');
        $this->addElement('text', 'field', [
            'label'      => 'Label:',
            'required'   => true,
            'validators' => [['EmailAddress']], // or just string 'EmailAddress'
        ]);
        $this->addElement('textarea', 'body', ['label' => 'Body:', 'required' => true]);
        $this->addElement('submit', 'submit', ['label' => 'Send']);
    }
}
```

### Doctrine Entity
```php
namespace Application\Modules\Mail\Entities;

use Application\Modules\Default\Entities\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'mails')]
class Mail extends AbstractEntity
{
    #[ORM\Column(name: 'subject', type: 'string')]
    private string $subject;

    // getters and setters returning self for fluency
}
```
- `AbstractEntity` provides `id`, `createdAt`, `updatedAt`
- Doctrine scans `application/Modules/*/Entities`

### DTO
```php
namespace Application\Modules\Mail\DTOs;

class SendMailDTO
{
    public readonly string $field;

    public function __construct(array $data)
    {
        $value = $data['field'] ?? null;
        if (!is_string($value) || empty($value)) {
            throw new \InvalidArgumentException('...');
        }
        $this->field = $value;
    }
}
```
- `public readonly` = externally readable + immutable (no getters needed)
- Use `filter_var($email, FILTER_VALIDATE_EMAIL)` for email validation (no ZF1 dependency)

### Service
```php
namespace Application\Modules\Mail\Services;

class MailService
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function sendMail(SendMailDTO $dto): void
    {
        $entity = new Mail();
        // populate entity...
        $this->em->persist($entity);

        try {
            // business logic
            $entity->setStatus(...);
        } catch (\Throwable $th) {
            $entity->setStatus(...);
            throw $th;
        } finally {
            $this->em->flush(); // always runs
        }
    }
}
```

### View (.phtml)
```php
<?php if (isset($this->message)): ?>
    <p><?= $this->escape($this->message) ?></p>
<?php endif; ?>
<?= $this->form ?>
```

## Doctrine / Migrations

Always use the unified runner `config/doctrine/doctrine-orm.php` — it includes both ORM and Migrations commands.

```bash
make migration-diff      # generates migration by comparing entities vs database
make migration-migrate   # applies pending migrations
make migration-status    # checks migration state
make migration-rollback  # reverts latest migration
```

## Useful Commands
```bash
make up          # start containers
make down        # stop containers
make shell       # bash into PHP container
make db          # mysql client in container
make autoload    # composer dump-autoload -o (run after creating new classes)
make logs        # container logs
```

## Database (Docker)
- Host: `mysql` (internal) / `localhost:3306` (external)
- Database: `zf1_app`
- User: `zf1_user` / Password: `zf1_pass`

## Known Pitfalls
- **Autoload:** After creating a class in a new folder, run `make autoload`
- **Namespace required:** Every PHP file in the project (except `.phtml`) needs a namespace
- **Folder casing:** Module folders are PascalCase (`Modules/Mail/`, not `modules/mail/`)
- **Zend_Mail without transport:** In Docker without sendmail/SMTP, `send()` throws — expected in dev
- **EntityManager:** Available via `Zend_Registry::get('doctrine.em')` after bootstrap
- **`isPost()` IDE warning:** IDE complains about abstract type, works fine at runtime
- **Custom Dispatcher:** Enables namespaced controllers — do not remove or bypass
