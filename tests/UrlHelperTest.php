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

        // TODO: fix route parse for:
        // $routes->add(new Route('123', 'test/abc/aaa/{action=1123}/ffff'));

        $routes->add(new Route('test', 'testovich/{action=index}/{id?}', array('controller' => 'abc')));
        $routes->add(new Route('default', '{controller=Test}/{action=index}/{id?}'));

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