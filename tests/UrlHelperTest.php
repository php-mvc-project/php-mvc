<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\DefaultRouteProvider;
use PhpMvc\RouteCollection;
use PhpMvc\ActionContext;
use PhpMvc\UrlParameter;
use PhpMvc\ViewContext;
use PhpMvc\UrlHelper;
use PhpMvc\Route;

final class UrlHelperTest extends TestCase
{

    public function testAction(): void
    {
        echo chr(10);

        $routes = new DefaultRouteProvider();

        $routes->add('strange', 'test/abc/aaa/{action=1123}/ffff', array('controller' => 'home'));
        $routes->add('anyname', 'alibaba/the', array('controller' => 'abc', 'action' => 'kaadabrov'));
        $routes->add('test', 'testovich/{action=index}/{id?}', array('controller' => 'abc'));

        $routes->add(
            'news', 
            '{yyyy}-{mm}-{dd}/{id}', 
            array(
                'controller' => 'news',
                'action' => 'show'
            ),
            array(
                'yyyy' => '\d{4}',
                'mm' => '([0]{1}[1-9]{1})|([1]{1}[0-2]{1})',
                'dd' => '[0-3]{1}[0-9]{1}',
                'id' => '\d+'
            )
        );

        $routes->add('default', '{controller=Test}/{action=index}/{id?}');

        $httpContext = HttpContext::get('test/abc/aaa/aaaaa/ffff')->setRoutes($routes);

        $actionContext = new ActionContext($httpContext);

        $this->assertEquals(
            '/test/abc/aaa/bbbbb/ffff',
            $result = UrlHelper::action($actionContext, 'bbbbb')
        );

        echo $result . ' - OK' . chr(10);

        $httpContext = HttpContext::get('https://example.org/abcdef/test')->setRoutes($routes);

        $actionContext = new ActionContext($httpContext);

        $this->assertEquals(
            '/abcdef',
            $result = UrlHelper::action($actionContext, 'index')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'https://example.org/abcdef#test',
            $result = UrlHelper::action($actionContext, 'index', null, null, 'test', 'https', 'example.org')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'https://example.org/abcdef',
            $result = UrlHelper::action($actionContext, 'index', null, null, null, null, 'example.org')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            'git://example.org/abcdef',
            $result = UrlHelper::action($actionContext, 'index', null, null, null, 'git')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            '/abcdef/page/123?text=hello+world%21&n=555',
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
            '/testovich',
            $result = UrlHelper::action($actionContext, 'index', 'abc')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            '/testovich/examplovich', 
            $result = UrlHelper::action($actionContext, 'examplovich', 'abc')
        );

        echo $result . ' - OK' . chr(10);

        $this->assertEquals(
            '/alibaba/the', 
            $result = UrlHelper::action($actionContext, 'kaadabrov', 'abc')
        );

        echo $result . ' - OK' . chr(10);
    }

}