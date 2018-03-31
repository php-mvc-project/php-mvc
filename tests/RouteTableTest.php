<?php
declare(strict_types=1);

require_once 'RequestContext.php';

use PHPUnit\Framework\TestCase;
use PhpMvc\RouteTable;
use PhpMvc\Route;
use PhpMvc\UrlParameter;
use PhpMvc\ViewContext;

final class RouteTableTest extends TestCase
{

    public function testAddEmptyNameException(): void
    {
        $this->expectExceptionMessageRegExp('/cannot be empty/');

        RouteTable::clear();

        $route = new Route();

        RouteTable::add($route);
    }

    public function testAddEmptyTemplateException(): void
    {
        $this->expectExceptionMessageRegExp('/template/');

        $route = new Route();
        $route->name = 'default';

        RouteTable::add($route);
    }

    public function testAddNonUniqueNameException(): void
    {
        $this->expectExceptionMessageRegExp('/unique name/');

        RouteTable::clear();

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        RouteTable::add($route);

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        RouteTable::add($route);
    }

    public function testAddOK(): void
    {
        RouteTable::clear();

        $route = new Route();
        $route->name = 'first';
        $route->template = '{controller}/{action}/{id}';

        RouteTable::add($route);
        
        $route2 = new Route();
        $route2->name = 'default';
        $route2->template = '{controller}';

        RouteTable::add($route2);
    }

    public function testAddRouteEmptyNameException(): void
    {
        $this->expectExceptionMessageRegExp('/cannot be empty/');

        RouteTable::clear();

        RouteTable::addRoute('', '{controller}/{action}');
    }

    public function testAddRouteEmptyTemplateException(): void
    {
        $this->expectExceptionMessageRegExp('/template/');
        RouteTable::addRoute('default', null);

        $this->expectExceptionMessageRegExp('/template/');
        RouteTable::addRoute('default', '');
    }

    public function testAddRouteNonUniqueNameException(): void
    {
        $this->expectExceptionMessageRegExp('/unique name/');

        RouteTable::clear();

        RouteTable::addRoute('default', 'first');
        RouteTable::addRoute('default', 'second');
    }

    public function testAddRouteOK(): void
    {
        RouteTable::clear();

        RouteTable::addRoute('test', 'test');
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');
    }

    public function testGetRoute(): void {
        ViewContext::$requestContext = new RequestContext(array(
            'REQUEST_URI' => '/?controller=home&action=index&id=123',
            'QUERY_STRING' => 'controller=home&action=index&id=123'
        ));
        RouteTable::clear();
        
        RouteTable::addRoute(
            'image', 
            'image', 
            array(
                'controller' => 'Home', 
                'action' => 'image', 
                'id' => UrlParameter::OPTIONAL
            )
        );

        RouteTable::addRoute(
            'actionId', 
            '{controller}/{action}-id{id}', 
            array(
                'controller' => 'Home', 
                'action' => 'index'
            )
        );

        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        $route = RouteTable::getRoute();

        $this->assertNotNull($route);
    }
}