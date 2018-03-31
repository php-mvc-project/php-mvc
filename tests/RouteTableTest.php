<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PhpMvc\RouteTable;
use PhpMvc\Route;

final class RouteTableTest extends TestCase
{

    public function testAddWithEmptyName(): void
    {
        $this->expectExceptionMessageRegExp('/cannot be empty/');

        RouteTable::clear();

        $route = new Route();

        RouteTable::Add($route);
    }

    public function testAddWithEmptyTemplate(): void
    {
        $this->expectExceptionMessageRegExp('/template/');

        RouteTable::clear();

        $route = new Route();
        $route->name = 'default';

        RouteTable::Add($route);
    }
    
    public function testAddNameDup(): void
    {
        $this->expectExceptionMessageRegExp('/unique name/');

        RouteTable::clear();

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        RouteTable::Add($route);

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        RouteTable::Add($route);
    }
    
    public function testAddOK(): void
    {
        RouteTable::clear();

        $route = new Route();
        $route->name = 'first';
        $route->template = '{controller}/{action}/{id}';

        RouteTable::Add($route);
        
        $route2 = new Route();
        $route2->name = 'default';
        $route2->template = '{controller}';

        RouteTable::Add($route2);
    }

}