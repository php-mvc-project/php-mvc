<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\Route;
use PhpMvc\RouteTable;
use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;
use PhpMvc\Make;

final class OutputCacheTest extends TestCase
{

    protected $preserveGlobalState = false;

    protected $runTestInSeparateProcess = true;

    private $basePath;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->basePath = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mvc';

        if (!defined('PHPMVC_DS')) {
            define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('PHPMVC_ROOT_PATH')) {
            define('PHPMVC_ROOT_PATH', $this->basePath . PHPMVC_DS);
        }
    }

    public function testNoCache(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/nocache?time=' . $time);

        echo chr(10);
        echo '# No-cache 10 sec. test';

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: no-cache',
            $result
        );

        echo chr(10);
        echo 'Sleep: 5 sec.';
        sleep(5);

        $httpContext = HttpContext::get('/OutputCache/nocache?time=' . time());

        echo chr(10);

        echo 'Repeated Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: no-cache',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testDuration10(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/duration10?time=' . $time);

        echo chr(10);
        echo '# Duration 10 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/duration10?time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=10',
            $result
        );

        $this->assertContains((string)$time, $result);
 
        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(10);

        $httpContext = HttpContext::get('/OutputCache/duration10?time=' . time());

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testLocationServer(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/locationServer?time=' . $time);

        echo chr(10);
        echo '# Location Server 10 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: no-cache',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/locationServer?time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: no-cache',
            $result
        );

        $this->assertContains((string)$time, $result);
 
        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(10);

        $httpContext = HttpContext::get('/OutputCache/locationServer?time=' . time());

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: no-cache',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testLocationClient(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/locationClient?time=' . $time);

        echo chr(10);
        echo '# Location Client 10 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: private, max-age=10',
            $result
        );
 
        echo chr(10);
        echo 'Sleep: 11 sec.';
        sleep(11);

        $httpContext = HttpContext::get('/OutputCache/locationClient?time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: private, max-age=10',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testLocationServerAndClient(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/locationServerAndClient?time=' . $time);

        echo chr(10);
        echo '# Location Server & Client 10 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: private, max-age=10',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/locationServerAndClient?time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: private, max-age=10',
            $result
        );

        $this->assertContains((string)$time, $result);
 
        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(10);

        $httpContext = HttpContext::get('/OutputCache/locationServerAndClient?time=' . time());

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: private, max-age=10',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testLocationDownstream(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/locationDownstream?time=' . $time);

        echo chr(10);
        echo '# Location Downstream 10 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=10',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/locationDownstream?time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=10',
            $result
        );

        $this->assertNotContains((string)$time, $result);
 
        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(10);

        $httpContext = HttpContext::get('/OutputCache/locationDownstream?time=' . time());

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=10',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testVaryByParam(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/varyByParam/1?time=' . $time);

        echo chr(10);
        echo '# vary-by-param 30 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam/1?time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam/2?time=' . time());

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam/1?time=' . time());

        echo chr(10);

        echo 'Request #4: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(31);

        $httpContext = HttpContext::get('/OutputCache/varyByParam/1?time=' . time());

        echo chr(10);

        echo 'Request #5: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testVaryByParam2(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/varyByParam2/1?abc=123&time=' . $time);

        echo chr(10);
        echo '# vary-by-param #2 30 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam2/1?abc=123&time=' . time());

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam2/1?abc=321&time=' . time());

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam2/2?abc=123&time=' . time());

        echo chr(10);

        echo 'Request #4: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByParam2/1?abc=123&time=' . time());

        echo chr(10);

        echo 'Request #5: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(31);

        $httpContext = HttpContext::get('/OutputCache/varyByParam2/1?abc=123&time=' . time());

        echo chr(10);

        echo 'Request #6: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testVaryByHeader(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/varyByHeader?time=' . $time, array('HTTP_USER_AGENT' => 'Chrome'));

        echo chr(10);
        echo '# vary-by-header 30 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByHeader?time=' . time(), array('HTTP_USER_AGENT' => 'Chrome'));

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByHeader?time=' . time(), array('HTTP_USER_AGENT' => 'Opera'));

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByHeader?time=' . time(), array('HTTP_USER_AGENT' => 'Chrome'));

        echo chr(10);

        echo 'Request #4: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(31);

        $httpContext = HttpContext::get('/OutputCache/varyByHeader?time=' . time(), array('HTTP_USER_AGENT' => 'Chrome'));

        echo chr(10);

        echo 'Request #5: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

    public function testVaryByCustom(): void {
        RouteTable::clear();
        RouteTable::add('default', '{controller=Home}/{action=index}/{id?}');

        $time = time();

        $httpContext = HttpContext::get('/OutputCache/varyByCustom?time=' . $time, array('HTTP_ACCEPT_LANGUAGE' => 'fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5'));

        echo chr(10);
        echo '# vary-by-custom 30 sec. test';

        echo chr(10);
        echo 'Request #1: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByCustom?time=' . time(), array('HTTP_ACCEPT_LANGUAGE' => 'fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5'));

        echo chr(10);

        echo 'Request #2: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByCustom?time=' . time(), array('HTTP_ACCEPT_LANGUAGE' => 'ru, fr'));

        echo chr(10);

        echo 'Request #3: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 1 sec.';
        sleep(1);

        $httpContext = HttpContext::get('/OutputCache/varyByCustom?time=' . time(), array('HTTP_ACCEPT_LANGUAGE' => 'en'));

        echo chr(10);

        echo 'Request #4: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertContains((string)$time, $result);

        echo chr(10);
        echo 'Sleep: 10 sec.';
        sleep(31);

        $httpContext = HttpContext::get('/OutputCache/varyByCustom?time=' . time(), array('HTTP_ACCEPT_LANGUAGE' => 'ru,en;q=0.9,en-US;q=0.8'));

        echo chr(10);

        echo 'Request #5: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Cache-Control: public, max-age=30',
            $result
        );

        $this->assertNotContains((string)$time, $result);
    }

}