<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\Route;
use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;
use PhpMvc\AppBuilder;

final class FilterTest extends TestCase
{

    protected $preserveGlobalState = false;

    protected $runTestInSeparateProcess = true;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $basePath = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mvc';

        if (!defined('PHPMVC_DS')) {
            define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('PHPMVC_ROOT_PATH')) {
            define('PHPMVC_ROOT_PATH', $basePath . PHPMVC_DS);
        }

        AppBuilder::useNamespace('PhpMvcTest');
        AppBuilder::useBasePath($basePath);
    }

    public function testEmpty(): void
    {
        $httpContext = HttpContext::get('/filter')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('index', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecuting(): void
    {
        $httpContext = HttpContext::get('/filter/executingWorld')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('executing the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecuted(): void
    {
        $httpContext = HttpContext::get('/filter/executedWorld')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('executed the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecute(): void
    {
        $httpContext = HttpContext::get('/filter/executeWorld')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('executing the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testExecute2(): void
    {
        $httpContext = HttpContext::get('/filter/executeWorld2')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('executing the world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testError(): void
    {
        $httpContext = HttpContext::get('/filter/error')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        // $this->assertEquals('hello exception!', $result);
        $this->assertContains('An error occurred while processing the request.', $result);

        echo ' - OK' . chr(10);
    }

    public function testErrorNoResult(): void
    {
        $httpContext = HttpContext::get('/filter/errorNoResult')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('', $result);

        echo ' - OK' . chr(10);
    }

    public function testErrorRewrited(): void
    {
        $httpContext = HttpContext::get('/filter/errorRewrited')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('just make an exception in this world!', $result);

        echo ' - OK' . chr(10);
    }

}