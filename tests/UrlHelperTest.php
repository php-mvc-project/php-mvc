<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\UrlHelper;
use PhpMvc\ActionContext;
use PhpMvc\ViewContext;
use PhpMvc\UrlParameter;
use PhpMvc\RouteTable;
use PhpMvc\RouteCollection;
use PhpMvc\Route;

final class UrlHelperTest extends TestCase
{

    public function testAction(): void
    {
        echo chr(10);

        $routes = new RouteCollection();

        $routes->add(new Route('strange', 'test/abc/aaa/{action=1123}/ffff', array('controller' => 'home')));
        $routes->add(new Route('test', 'testovich/{action=index}/{id?}', array('controller' => 'abc')));

        $routes->add(
            new Route(
                'news', 
                '{yyyy}-{mm}-{dd}/{id}', 
                array(
                    'controller' => 'news',
                    'action' => 'show'
                )
            ),
            array(
                'yyyy' => '\d{4}',
                'mm' => '([0]{1}[1-9]{1})|([1]{1}[0-2]{1})',
                'dd' => '[0-3]{1}[0-9]{1}',
                'id' => '\d+'
            )
        );

        $routes->add(new Route('default', '{controller=Test}/{action=index}/{id?}'));

        $httpContext = new HttpContext(
            $routes,
            array(
                'REQUEST_URI' => 'test/abc/aaa/aaaaa/ffff',
                'REQUEST_METHOD' => 'GET'
            )
        );

        $actionContext = new ActionContext($httpContext);

        $this->assertEquals(
            'test/abc/aaa/bbbbb/ffff',
            $result = UrlHelper::action($actionContext, 'bbbbb')
        );

        echo $result . ' - OK' . chr(10);

        $httpContext = new HttpContext(
            $routes,
            array(
                'REQUEST_URI' => '/abc/test',
                'REQUEST_METHOD' => 'GET'
            )
        );

        $actionContext = new ActionContext($httpContext);

        $this->assertEquals(
            'Test/index', // TODO: remove index
            $result = UrlHelper::action($actionContext, 'index')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'https://example.org/Test/index#test', // TODO: remove index
            $result = UrlHelper::action($actionContext, 'index', null, null, 'test', 'https', 'example.org')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'http://example.org/Test/index', // TODO: remove index
            $result = UrlHelper::action($actionContext, 'index', null, null, null, null, 'example.org')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'Test/page/123?text=hello+world%21&n=555', // TODO: remove index
            $result = UrlHelper::action(
                $actionContext, 
                'page', 
                null, 
                array(
                    'id' => 123, 
                    'text' => 'hello world!', 
                    'n' => 555
                )
            )
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'testovich/index', // TODO: remove index
            $result = UrlHelper::action($actionContext, 'index', 'abc')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'testovich/examplovich', 
            $result = UrlHelper::action($actionContext, 'examplovich', 'abc')
        );

        echo $result . ' - OK' . chr(10);
    }

}