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
        // routes
        RouteTable::clear();

        RouteTable::addRoute(
            'defaultControllerAndAction', 
            'image-{id}', 
            array(
                'controller' => 'Home', 
                'action' => 'image'
            )
        );

        RouteTable::addRoute(
            'actionIdAfter',
            '{controller}/test-id{id}', 
            array(
                'controller' => 'Home', 
                'action' => 'test'
            )
        );

        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        // test requests
        $requests = array(
            array(
                'uri' => '/',
                'expectPath' => 'home/index',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/home',
                'expectPath' => 'home/index',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/home/index',
                'expectPath' => 'home/index',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/home/test',
                'expectPath' => 'home/test',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/about',
                'expectPath' => 'about/index',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/about/contacts',
                'expectPath' => 'about/contacts',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/arTicles/SeCtIons/1',
                'expectPath' => 'articles/sections/1',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/Articles/sections/5',
                'expectPath' => 'articles/sections/5',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/articles/sections/123?page=10&filter=test',
                'expectPath' => 'articles/sections/123',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/forum/mvc/123',
                'expectPath' => 'forum/mvc/123',
                'expectRoute' => 'default',
            ),
            array(
                'uri' => '/image-123',
                'expectPath' => 'home/image/123',
                'expectRoute' => 'defaultControllerAndAction',
            ),
        );

        // run
        foreach ($requests as $request) {
            ViewContext::$requestContext = new RequestContext(array(
                'REQUEST_URI' => $request['uri']
            ));
    
            $route = RouteTable::getRoute();

            $this->assertNotNull($route);
            $this->assertEquals($request['expectPath'], $route->getUrl());
            $this->assertEquals($request['expectRoute'], $route->name);
        }
    }
}