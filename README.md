## Phalcon 5 Middleware
[![Packagist License](https://poser.pugx.org/nin/phalcon-middleware/license.png)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://poser.pugx.org/nin/phalcon-middleware/version.png)](https://packagist.org/packages/nin/phalcon-middleware)
[![Total Downloads](https://poser.pugx.org/nin/phalcon-middleware/d/total.png)](https://packagist.org/packages/nin/phalcon-middleware)

This is a package to integrate middleware with Phalcon 5.
Middleware provide a convenient mechanism for inspecting and filtering HTTP requests entering your application.

### Installation:

Require this package with composer.

```php
composer require nin/phalcon-middleware 
```

Register a Provider in `index.php`

```php
$container = new \Phalcon\Di\FactoryDefault();
$container->register(new \Nin\Middleware\ServiceProvider());

```

OR `app/config/providers.php`

```php
return [
    \Nin\Middleware\ServiceProvider::class,
];   
```

### Usage:

#### Config:

Make config file `config/middleware.php`

```php
<?php

return [
    // The application's global HTTP middleware stack
    // These middleware are run during every request to your application.
    'middleware_global' => [
         \App\Middleware\TrimStrings::class
    ],
    // The application's route middleware groups.
    'middleware_groups' => [
        'web' => [
            \App\Middleware\StartSession::class
        ],
        'api' => [
            \App\Middleware\ApiMiddleware::class,
            \App\Middleware\AdminApiMiddleware::class
        ]
    ],
];

```

#### Make Middleware Class:

```php
<?php

namespace App\Middleware;

use Nin\Middleware\Middleware;

class TestMiddleware extends Middleware
{
    public function handle($request)
    {
        if ($request->get('token') !== 'valid-token') {
            throw new \Exception('Token invalid.');
        }
        return parent::handle($request);
    }
}

```

#### Assigning Middleware To Routes:

```php
use Phalcon\Mvc\Router;

$router = new Router();
$router->addGet(
    '/invoices/edit/{id}',
    [
        'controller' => 'invoices',
        'action' => 'edit',
        'middleware' => ['web', 'auth'],
    ]
);
```

For groups

```php

new \Phalcon\Mvc\Router\Group([
    'module' => 'backend',
    'middleware' => 'web',
]);
```


#### Assigning Middleware To Controller:

```php
class InvoicesController extends Controller
{
    public function initialize()
    {
        $this->middleware->attach('auth');
    }

    public function editAction()
    {
    // OR:  $this->middleware->attach('auth');
    }

}
```

#### Middleware Parameters:

```php

$this->middleware->attach( ['web', 'auth:admin']);

class AuthMiddleware  extends \Nin\Middleware\Middleware
{
    public function handle($request)
    {
        $param = $this->getParam();
        if ($param !== 'admin') {
            throw new \Exception('Authenticate Fail.');
        }
        return parent::handle($request);
    }
}
```

#### Redirecting:

Redirect to route:

```php
public function handle($request)
{
    if ($this->getParam() !== 'admin') {
        return $this->redirectToRoute('login');
    }
    return parent::handle($request);
}
```

Redirect to url:

```php
public function handle($request)
{
    if ($this->getParam() !== 'admin') {
        return $this->redirectToUrl('/login');
    }
    return parent::handle($request);
}
```
