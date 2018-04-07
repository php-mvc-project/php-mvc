<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\Route;
use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;

final class RouteCollectionTest extends TestCase
{

    public function testAddEmptyNameException(): void
    {
        $this->expectExceptionMessageRegExp('/must not be empty/');

        $routes = new RouteCollection();

        $route = new Route();

        $routes->add($route);
    }

    public function testAddEmptyTemplateException(): void
    {
        $this->expectExceptionMessageRegExp('/template/');

        $routes = new RouteCollection();

        $route = new Route();
        $route->name = 'default';

        $routes->add($route);
    }

    public function testAddNonUniqueNameException(): void
    {
        $this->expectExceptionMessageRegExp('/unique name/');

        $routes = new RouteCollection();

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        $routes->add($route);

        $route = new Route();
        $route->name = 'default';
        $route->template = '{controller}';

        $routes->add($route);
    }

    public function testGetRoute(): void {
        // routes
        $routes = new RouteCollection();

        $routes->add(new Route(
            'defaultControllerAndAction', 
            'image-{id}', 
            array(
                'controller' => 'Home', 
                'action' => 'image'
            )
        ));

        $routes->add(new Route(
            'actionIdAfter',
            '{controller}/{action}-id{id}', 
            array(
                'controller' => 'Home', 
                'action' => 'test'
            )
        ));

        $routes->add(new Route(
            'actionIdBefore',
            '{controller}/{id}-{action}', 
            array(
                'controller' => 'Home', 
                'action' => 'abc'
            ),
            array(
                'id' => '\d+'
            )
        ));

        $routes->add(new Route(
            'manyDifferentParameters',
            '{yyyy}-{mm}-{dd}/{id}', 
            array(
                'controller' => 'news', 
                'action' => 'show', 
                'id' => UrlParameter::OPTIONAL
            ),
            array(
                'yyyy' => '\d{4}',
                'mm' => '([0]{1}[1-9]{1})|([1]{1}[0-2]{1})',
                'dd' => '[0-3]{1}[0-9]{1}',
                'id' => '\d+'
            )
        ));

        $routes->add(new Route('default', '{controller=Home}/{action=index}/{id?}'));

        // test requests
        $requests = array(
            array(
                'uri' => '/',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'home',
                    'action' => 'index',
                ),
            ),
            array(
                'uri' => '/home',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'home',
                    'action' => 'index',
                ),
            ),
            array(
                'uri' => '/home/index',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'home',
                    'action' => 'index',
                ),
            ),
            array(
                'uri' => '/home/test',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'home',
                    'action' => 'test',
                ),
            ),
            array(
                'uri' => '/about',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'about',
                    'action' => 'index',
                ),
            ),
            array(
                'uri' => '/about/contacts',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'about',
                    'action' => 'contacts',
                ),
            ),
            array(
                'uri' => '/arTicles/SeCtIons/1',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'articles',
                    'action' => 'sections',
                    'id' => '1',
                ),
            ),
            array(
                'uri' => '/Articles/sections/5',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'articles',
                    'action' => 'sections',
                    'id' => '5',
                ),
            ),
            array(
                'uri' => '/articles/sections/123?page=10&filter=test',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'articles',
                    'action' => 'sections',
                    'id' => '123',
                ),
            ),
            array(
                'uri' => '/forum/mvc/123',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'forum',
                    'action' => 'mvc',
                    'id' => '123',
                ),
            ),
            array(
                'uri' => '/image-123',
                'expectRoute' => 'defaultControllerAndAction',
                'expectSegments' => array(
                    'controller' => 'home',
                    'action' => 'image',
                    'id' => '123',
                ),
            ),
            array(
                'uri' => '/news/show-id100500',
                'expectRoute' => 'actionIdAfter',
                'expectSegments' => array(
                    'controller' => 'news',
                    'action' => 'show',
                    'id' => '100500',
                ),
            ),
            array(
                'uri' => '/news/show-500100',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'news',
                    'action' => 'show-500100',
                ),
            ),
            array(
                'uri' => '/2018-04-03/123',
                'expectRoute' => 'manyDifferentParameters',
                'expectSegments' => array(
                    'controller' => 'news',
                    'action' => 'show',
                    'yyyy' => '2018',
                    'mm' => '04',
                    'dd' => '03',
                    'id' => '123',
                ),
            ),
            array(
                'uri' => '/2018-55-66/123',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => '2018-55-66',
                    'action' => '123',
                ),
            ),
            array(
                'uri' => '/2018-04-03/www',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => '2018-04-03',
                    'action' => 'www',
                ),
            ),
            array(
                'uri' => '/2018/04/03/show/9999/abc',
                'expectRoute' => null,
            ),
        );

        echo "\n";

        // run
        foreach ($requests as $request) {
            echo $request['uri'];

            $httpContext = new HttpContext(array(
                'REQUEST_URI' => $request['uri']
            ));

            $route = $routes->getRoute($httpContext);

            if ($request['expectRoute'] !== null) {
                $this->assertNotNull($route);
                $this->assertEquals($request['expectRoute'], $route->name);
                $this->assertEquals(count($request['expectSegments']), count($route->values));
    
                foreach ($request['expectSegments'] as $key => $value) {
                    $this->assertEquals($value, $route->values[$key]);
                }
            }
            else {
                $this->assertNull($route);
            }

            echo ' - OK' . "\n";
        }
    }
}