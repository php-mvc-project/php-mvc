# PHP MVC

[![PHP from Packagist](https://img.shields.io/packagist/php-v/php-mvc-project/php-mvc.svg?style=flat)](http://php.net)
[![License](https://img.shields.io/github/license/php-mvc-project/php-mvc.svg?style=flat)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/php-mvc-project/php-mvc.svg)](https://github.com/php-mvc-project/php-mvc/releases)
[![Packagist](https://img.shields.io/packagist/dt/php-mvc-project/php-mvc.svg)](https://packagist.org/packages/php-mvc-project/php-mvc)

The best implementation of the **Model-View-Controller** architectural pattern in **PHP**!

## Features

* Templates
* Routing
* Filters
* Cache
* Validation
* Data annotation
* Security

## Requirements

* PHP 7.x

## Installation

```
$ composer require php-mvc-project/php-mvc
```

## Server Configuration

The server must send the entire request to the `./index.php` file.

### Apache

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On

  # redirect /index.php to /
  RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /.*index\.php
  RewriteRule ^index.php/?(.*)$ $1 [R=301,L]

  # process all requests through index.php, except for actually existing files
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1?%{QUERY_STRING} [QSA,L]
</IfModule>
```

### nginx

```nginx
location / {
  try_files $uri $uri/ /index.php?$args;
}
```

## Basic Usage

Create the following structure in the project root directory:

```shell
.
├── controllers           # controllers folder
├─── HomeController.php   # class of the home controller
├─── *Controller.php      # classes of others controllers
├── models                # models folder
├── views                 # views folder
├─── home                 # views folder of the home controller
├─── ...                  # folders for other controllers
├─── shared               # shared views
└── index.php             # index file
```

### ./index.php

```php
<?php
// use aoutoload (recommended)
require_once getcwd() . '/vendor/autoload.php';
// or include the required files
// require_once getcwd() . '/vendor/php-mvc-project/php-mvc/src/index.php';

// import the AppBuilder class
use PhpMvc\AppBuilder;

// be sure to specify the namespace of your application
AppBuilder::useNamespace('RootNamespaceOfYourApp');

// use session if you need
AppBuilder::useSession();

// routes
AppBuilder::routes(function($routes) {
    // skip all the paths that point to the folder /content
    $routes->ignore('content/{*file}');

    // default route
    $routes->add('default', '{controller=Home}/{action=index}/{id?}');
});

// build
AppBuilder::build();
```

> **IMPORTANT**: You must use namespaces in your application.
> Be sure to specify the root namespace of your application using the `AppBuilder::useNamespace(string)` function.

### ./controllers/HomeController.php

```php
<?php
// make sure you add the Controllers child name to the root namespace of your application
namespace RootNamespaceOfYourApp\Controllers;

// import the base class of the controller
use PhpMvc\Controller;

// expand your class with the base controller class
class HomeController extends Controller {

    public function index() {
        // use the content function to return text content:
        return $this->content('Hello, world!');

        // create to the ./view/home/index.php
        // and use view function to return this view:
        // return $this->view();
    }

}
```

> **IMPORTANT**: The names of all controller classes must end with the `Controller` suffix.
> For example: `HomeController`, `AccountController`, `TestController` etc.

## Structure

Your projects must implements the strict structure:

```shell
.
├── content               # static content (images, css, scripts, etc)
├─── ...                  # any files and folders
├── controllers           # controllers folder
├─── HomeController.php   # class of the home controller
├─── *Controller.php      # classes of others controllers
├── filters               # filters folder
├─── *.php                # classes of models
├── models                # models folder
├─── *.php                # classes of models
├── views                 # views folder
├─── ...                  # views for specific controllers
├─── shared               # shared views
├─── ...                  # common view files that are not associated with specific controllers
├── index.php             # index file
└── ...                   # any of your files and folders
```

And adhere to the following rules:

1. Folder names must be in lowercase.

2. The views filenames must be in lowercase.

3. The file names of the controllers must be specified in the camel style, with a capital letter.
   The names must end with the `Controller` suffix. For example: `HomeController.php`.

## Models

The model is just classes. You can create any classes, with any structure.

It is recommended to adhere to the rule: the simpler, the better.

Using the static class `Model`, you can add metadata to a model in the constructor of the controller.

```php
<?php
namespace RootNamespaceOfYourApp\Controllers;

use PhpMvc\Controller;
use PhpMvc\Model;

class AccountController extends Controller {

    public function __construct() {
        Model::set('join', 'join');

        Model::required('join', 'username');
        Model::required('join', 'email');
        Model::required('join', 'password');
        Model::required('join', 'confirmPassword');

        Model::display('join', 'username', 'Username');
        Model::display('join', 'email', 'Email');
        Model::display('join', 'password', 'Password');
        Model::display('join', 'confirmPassword', 'Confirm password');

        Model::compare('join', 'confirmPassword', 'password');
        Model::validation('join', 'email', function($value) {
            return filter_var($value, \FILTER_VALIDATE_EMAIL);
        });
    }
    
    public function join($model) {
      if (!isset($model)) {
        $model = new ModelForExample();
      }

      return $this->view($model);
    }

}
```

## Views

The views files contain markup.
Markup can be complete or partial.

Using the `PhpMvc\Html` class, you can create markup for HTML elements or output some views within other views.

For example:

```php
<?php 
use PhpMvc\Html;
?>
<html>
<head>
  <title><?=Html::getTitle('Hello, world!')?></title>
</head>
<body>
  <h1>Hello, world!</h1>
  <?=Html::actionLink('Go to second view', 'second'))?><br />
  <?=Html::textBox('anyname')?><br />
  <?=Html::checkbox('rememberMe')?><br /><br />

  <!--Render ./view/shared/footer.php-->
  <?php Html::render('footer'); ?>
</body>
</html>
```

Use the helper class `PhpMvc\View` to customize the behavior of the view:

```php
<?php 
use PhpMvc\View;

// model intance (for the convenience of working with the IDE)
$model = new RootNamespaceOfYourApp\Models\AnyClass();

// set layout
View::setLayout('layout.php');

// set title
View::setTitle('Test title');

// iject model (from action)
View::injectModel($model);
?>

<h1><?=isset($model) ? $model->anyProperty : 'empty'?></h1>

<!--...-->
```

## Controllers

The controller classes names must match the controllers filenames.
For example: file name is `TestController.php`, class name is `TestController`.

Each controller class must inherit from the `PhpMvc\Controller` class.

The controller classes must contain action methods.

The action names must match filenames of views.
For example: view file is `index.php`, action name is `index`.

All methods of actions must have the modifier public.

The names of the action methods must not start with the underscore (_).

```php
class HomeController extends PhpMvc\Controller {

  public function index() {
    return $this->view();
  }

  public function hello() {
    return $this->view();
  }

  public function other() {
    return $this->view();
  }

}
```

Each action can take any number of parameters.

The parameters can be received from the query string, or from the POST data.

The following example shows the output of parameters retrieved from the query string:

```php
class TestController extends PhpMvc\Controller {

  public function get($search = '', $page = 1, $limit = 10) {
    return $this->content(
      'search: ' . $search . chr(10) .
      'page: ' . $page . chr(10) .
      'limit: ' . $limit
    );
  }

}
```

```
Request:
GET /test/get?search=hello&page=123&limit=100

Response:
search: hello
page: 123
limit: 100
```

Below is an example of obtaining a model sent by the POST method:

```php
class AnyNameModelClass {

  public $search;

  public $page;

  public $limit;

}
```

```php
class TestController extends PhpMvc\Controller {

  public function post($anyName) {
    return $this->content(
      'search: ' . $anyName->search . chr(10) .
      'page: ' . $anyName->page . chr(10) .
      'limit: ' . $anyName->limit
    );
  }

}
```

```
Request:
POST /test/post

search=hello&page=123&limit=100

Response:
search: hello
page: 123
limit: 100
```

Methods of action can return different results, in addition to views.

You can use the ready-made methods of the base class of the controller to output the data in the required format:

* `$this->view([string $viewOrModel = null[, object $model = null[, string $layout = null]]])`
* `$this->json(mixed $data[, int $options = 0[, int $depth = 512]])`
* `$this->file(string $path[, string $contentType = null[, string|bool $downloadName = null]])`
* `$this->content(string $content[, string $contentType = 'text/plain'])`
* `$this->statusCode(int $statusCode[, string $statusDescription = null])`
* `$this->notFound([string $statusDescription = null])`
* `$this->unauthorized([string $statusDescription = null])`
* `$this->redirect(string $url)`
* `$this->redirectPermanent(string $url)`
* `$this->redirectPreserveMethod(string $url)`
* `$this->redirectPermanentPreserveMethod(string $url)`
* `$this->redirectToAction(string $actionName[, string $controllerName = null[, array $routeValues = null[, string $fragment = null]]])`

Instead of the presented methods, you can independently create instances of the desired results and return them:

```php
class AnyController extends PhpMvc\Controller {

  public function example() {
    $view = new ViewResult();

    // set the name of an existing view file
    $view->viewFile = '~/view/abc/filename.php';
    // set the title
    $view->title = 'The view is created programmatically';
    // set the layout file name
    $view->layout = 'layout.php';

    // create model for view
    $model = new Example();

    $model->title = 'Hello, world!';
    $model->text = 'The model contains text.';
    $model->number = 42;

    // set model to the view
    $view->model = $model;

    // return view
    return $view;
  }

}
```

All result classes implement the `ActionResult` interface.
You can create your own result classes!

## Filters

Filters allow you to add handlers before and after the action.
And also handle errors of the action execution.

The filters must be in the `./Filters` folder.

Each filter must be inherited from the `PhpMvc\ActionFilter` class.

Filters can be global, or work at the level of an individual controller, or action.

Filters for specific controller or action can be set in the controller's constructor:

### ./controllers/TestController.php

```php
class TestController extends Controller {

  public function __construct() {
    // add filter TestFilter to all actions of the controller
    Filter::add('TestFilter');

    // add filter ExceptionToJson to error action
    Filter::add('error', 'ExceptionToJson');
  }

  public function index() {
      return $this->content('Hello, world!');
  }

  public function other() {
      return $this->view('~/views/home/other.php');
  }

  public function anyname() {
      return $this->view();
  }

  public function error() {
      throw new \Exception('Any error here!');
  }

}
```

### ./filters/TestFilter.php

```php
<?php
namespace RootNamespaceOfYourApp\Filters;

use PhpMvc\ActionFilter;
use PhpMvc\ContentResult;

class TestFilter extends ActionFilter {

  // action executed handler
  public function actionExecuted($actionExecutedContext) {
    // check exception result
    if (($ex = $actionExecutedContext->getException()) === null) {
      // no exception, replace result
      $actionExecutedContext->setResult(new ContentResult('test'));
    }
    else {
      // set exception error message to result
      $actionExecutedContext->setResult(new ContentResult($ex->getMessage()));
    }
  }

}
```

### ./filters/ExceptionToJson.php

```php
<?php
namespace RootNamespaceOfYourApp\Filters;

use PhpMvc\ActionFilter;
use PhpMvc\JsonResult;

class ExceptionToJson extends ActionFilter {

  // error handler
  public function exception($exceptionContext) {
    // set JsonResult to action result
    $exceptionContext->setResult(
      new JsonResult(
        array('message' => $exceptionContext->getException()->getMessage())
      )
    );
    // exception is handled
    $exceptionContext->setExceptionHandled(true);
  }

}
```

## Routing

You can set routing rules using the `AppBuilder::routes()` function, which expects the function as a parameter.
When called, an instance of `RouteProvider` will be passed to the function.

The `add(string $name, string $template[, array $defaults = null[, array $constraints = null]])` method allows you to add a routing rule.

The `ignore(string $template[, $constraints = null])` method allows you to add an ignore rule.

```php
AppBuilder::routes(function(RouteProvider $routes) {
  $routes->add('default', '{controller=Home}/{action=index}/{id?}');
});
```

The names of the routing rules must be unique.

The higher the rule in the list (the earlier the rule was added), the higher the priority in the search for a match.

In templates you can use any valid characters in the URL.

Use curly braces to denote the elements of the route.

Each element must contain a name.

A name can point to a controller, action, or any parameter expected by the action method.

For example, template is: `{controller}/{action}/{yyyy}-{mm}-{dd}`.

Action is:

```php
public function index($yyyy, $mm, $dd) {
  return $this->content('Date: ' . $yyyy . '-' . $mm . '-' . $dd);
}
```

```
Request:
GET /home/index/2018-05-26

Response:
Date: 2018-05-26
```

After the element name, you can specify a default value in the template.
The default value is specified using the equals sign.

For example: `{controller=home}/{action=index}` - default controller is `HomeController`, default action is `index()`.
The default values will be used if the address does not have values for the specified path elements.
Simply put, for requests `/home/index`, `/home` and `/` will produce the same result with this template.

If the path element is optional, then the question (?) symbol is followed by the name.
For example: `{controller=home}/{action=index}/{id?}` - id is optional,
`{controller=home}/{action=index}/{id}` - id is required.

Route providers must implement the `RouteProvider` interface.
The default is `DefaultRouteProvider`. If necessary, you can create your own route provider.
Use the `AppBuilder::useRouter(RouteProvider $routeProvider)` method to change the route provider.

## Caching

To use caching, you must call the `AppBuilder::useCache(CacheProvider $cacheProvider)` method, which should be passed to the cache provider instance.

The cache provider must implement the `CacheProvider` interface.

You can use the ready-made `FileCacheProvider`, which performs caching in the file system.

```php
AppBuilder::useCache(new FileCacheProvider());
```

You can access the cache via an instance of `HttpContextBase`.

For example, in the controller:

```php
$cache = $this->getHttpContext()->getCache();
$cache->add('test', 'hello, world!');
var_dump($cache->get('test'));
```

In the view:

```php
$cache = View::getHttpContext()->getCache();
$cache->add('test', 'hello, world!');
var_dump($cache->get('test'));
```

For output caching, you can use the static `OutputCache` class.

Caching rules can be specified for both the controller and for an each action.

Cache rules should be specified in the constructor of the controller.

```php
<?php
namespace RootNamespaceOfYourApp\Controllers;

use PhpMvc\OutputCache;
use PhpMvc\OutputCacheLocation;
use PhpMvc\Controller;

class OutputCacheController extends Controller {

    public function __construct() {
        // caching for 10 seconds of the results of all actions of this controller
        OutputCache::setDuration('.', 10);
        
        // caching for 30 seconds of the results of the action of thirty
        OutputCache::setDuration('thirty', 30);
    }

    public function index($time) {
        return $this->content('time => ' . $time);
    }

    public function thirty($time) {
        return $this->content('time => ' . $time);
    }
 
}
```

## License

The MIT License (MIT)

Copyright © 2018, [@meet-aleksey](https://github.com/meet-aleksey)