<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;
use PhpMvc\Route;

final class RouteCollectionTest extends TestCase
{

    public function testAddEmptyNameException(): void
    {
        $this->expectExceptionMessageRegExp('/must not be empty/');

        $routes = new RouteCollection('test');

        $route = new Route();

        $routes->add($route);
    }

    public function testAddEmptyTemplateException(): void
    {
        $this->expectExceptionMessageRegExp('/template/');

        $routes = new RouteCollection('test');

        $route = new Route();
        $route->name = 'default';

        $routes->add($route);
    }

    public function testAddNonUniqueNameException(): void
    {
        $this->expectExceptionMessageRegExp('/unique name/');

        $routes = new RouteCollection('test');

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        $routes->add($route);

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        $routes->add($route);
    }

}