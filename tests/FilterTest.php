<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\Route;
use PhpMvc\RouteTable;
use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;
use PhpMvc\Make;

final class FilterTest extends TestCase
{

    protected $preserveGlobalState = false;

    protected $runTestInSeparateProcess = true;

    private $basePath;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->basePath = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mvc';
    }

    public function testEmpty(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('index', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecuting(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/executingWorld',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('executing the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecuted(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/executedWorld',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('executed the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecute(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/executeWorld',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('executing the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecute2(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/executeWorld2',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('executing the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testError(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/error',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('hello exception!', $result);

        echo ' - OK' . chr(10);
    }

    public function testErrorNoResult(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/errorNoResult',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('', $result);

        echo ' - OK' . chr(10);
    }

    public function testErrorRewrited(): void
    {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = new HttpContext(
            null,
            null,
            array(
                'REQUEST_URI' => '/filter/errorRewrited',
                'REQUEST_METHOD' => 'GET'
            )
        );

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('just make an exception in this world!', $result);

        echo ' - OK' . chr(10);
    }

}