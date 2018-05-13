<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\DefaultRouteProvider;
use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;
use PhpMvc\Route;

final class DefaultRouteProviderTest extends TestCase
{

    public function testGetRoute(): void {
        $routes = new DefaultRouteProvider();

        $routes->init();

        $routes->ignore('favicon.ico');
        $routes->ignore('preview{extension}', array('extension' => '\.(png|jpg)'));
        $routes->ignore('content/{*file}');

        $routes->add('defaultControllerAndAction', 'image-{id}', array('controller' => 'Home', 'action' => 'image'));
        $routes->add('actionIdAfter', '{controller}/{action}-id{id}', array('controller' => 'Home', 'action' => 'test'));
        $routes->add('actionIdBefore', '{controller}/{id}-{action}', array('controller' => 'Home', 'action' => 'abc'), array('id' => '\d+'));
        $routes->add(
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
        );
        $routes->add('default', '{controller=Home}/{action=index}/{id?}');

        // test requests
        $requests = array(
            array(
                'uri' => '/content/images/preview.png',
                'expectTemplate' => 'content/{*file}',
                'expectSegments' => array(
                    'file' => 'images/preview.png'
                ),
            ),
            array(
                'uri' => '/content/styles/home.css',
                'expectTemplate' => 'content/{*file}',
                'expectSegments' => array(
                    'file' => 'styles/home.css'
                ),
            ),
            array(
                'uri' => '/content/scripts.js',
                'expectTemplate' => 'content/{*file}',
                'expectSegments' => array(
                    'file' => 'scripts.js'
                ),
            ),
            array(
                'uri' => '/favicon.ico',
                'expectTemplate' => 'favicon.ico',
                'expectSegments' => array(),
            ),
            array(
                'uri' => '/preview.png',
                'expectTemplate' => 'preview{extension}',
                'expectSegments' => array('extension' => '.png'),
            ),
            array(
                'uri' => '/preview.bmp',
                'expectRoute' => 'default',
                'expectSegments' => array(
                    'controller' => 'preview.bmp',
                    'action' => 'index',
                ),
            ),
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

        echo chr(10);

        // run
        foreach ($requests as $request) {
            echo $request['uri'];

            $httpContext = HttpContext::get($request['uri'])->setRoutes($routes);

            $route = $httpContext->getRoute();

            if (!empty($request['expectRoute'])) {
                $this->assertNotNull($route);
                $this->assertEquals($request['expectRoute'], $route->name);
                $this->assertEquals(count($request['expectSegments']), count($route->values));

                foreach ($request['expectSegments'] as $key => $value) {
                    $this->assertEquals($value, $route->values[$key]);
                }
            }
            elseif (!empty($request['expectTemplate'])) {
                $this->assertNotNull($route);
                $this->assertEquals($request['expectTemplate'], $route->template);
                $this->assertEquals(count($request['expectSegments']), count($route->values));
    
                foreach ($request['expectSegments'] as $key => $value) {
                    $this->assertEquals($value, $route->values[$key]);
                }
            }
            else {
                $this->assertNull($route);
            }

            echo ' - OK' . chr(10);
        }
    }

}