<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\Route;
use PhpMvc\RouteTable;
use PhpMvc\RouteCollection;
use PhpMvc\UrlParameter;
use PhpMvc\Make;

final class MakeTest extends TestCase
{

    protected $preserveGlobalState = false;

    protected $runTestInSeparateProcess = true;

    private $basePath;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->basePath = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mvc';
    }

    public function testMagic(): void
    {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            'Home page - PHP MVC Test project',
            $result
        );

        $this->assertContains(
            'Default layout',
            $result
        );

        $this->assertContains(
            'Hello world!',
            $result
        );

        $this->assertContains(
            'This data is transferred to the view through the model!',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testSpecifiedView(): void
    {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/specifiedView',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            'Test message passed to the view file "hello.php" from the action method "specifiedView".',
            $result
        );

        echo " - OK\n";
    }

    public function testMakeView() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/makeview',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            'The view is created programmatically - PHP MVC Test project',
            $result
        );

        $this->assertContains(
            'Default layout',
            $result
        );

        $this->assertContains(
            'This is another page of the most wonderful site in the world!',
            $result
        );

        $this->assertContains(
            'The view is created programmatically',
            $result
        );

        $this->assertContains(
            'World hello!',
            $result
        );

        $this->assertContains(
            'key1',
            $result
        );

        $this->assertContains(
            'value3',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testSetGetViewData() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/setGetViewData',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            '"Hello world!"',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testDifferentWaysViewData() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/differentWaysViewData',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            'Hello setViewData!',
            $result
        );

        $this->assertContains(
            'Replaced setViewData2!',
            $result
        );

        $this->assertContains(
            'Hello viewData!',
            $result
        );

        // TODO: one common collection of data is needed
        $this->assertContains(
            'Hello viewData2!',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testNoViewFile() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/noViewFile',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        $this->expectExceptionMessageRegExp('/The view file could not be found/');

        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        echo " - OK\n";
    }

    public function testJsonData() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/jsonData',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('[1,2,3,4,5]', $result);

        echo " - OK\n";
    }

    public function testGetContent() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/getContent',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('This is plain text', $result);

        echo " - OK\n";
    }

    public function testString() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/string',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('hello world!', $result);

        echo " - OK\n";
    }

    public function testArr() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/arr',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('[1,2,3,4,5]', $result);

        echo " - OK\n";
    }

    public function testObj() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/obj',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('{"title":"Object result","text":"This object will be returned in JSON format!","number":555}', $result);

        echo " - OK\n";
    }

    public function testGetImage() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/home/getImage',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertEquals('89504e470d0a1a0a0000', $result);

        echo " - OK\n";
    }

    public function testLoginGet() {
        $httpContext = new HttpContext(array(
            'REQUEST_URI' => '/account/login',
            'REQUEST_METHOD' => 'GET'
        ));

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            '<h4 class="modal-title">Login for all</h4>',
            $result
        );

        $this->assertContains(
            '<label for="login">Username:</label>',
            $result
        );

        $this->assertContains(
            '<label for="password">Password:</label>',
            $result
        );

        $this->assertContains(
            'Username that you used when registering.',
            $result
        );

        $this->assertContains(
            'Your password.',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testLoginPostOnlyRootCanLogin() {
        $httpContext = new HttpContext(
            array(
                'REQUEST_URI' => '/account/login',
                'REQUEST_METHOD' => 'POST'
            ), 
            array(),
            array(
                'username' => 'pupkin',
                'password' => '123123'
            )
        );

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            '<h4 class="modal-title">Login for all</h4>',
            $result
        );

        $this->assertContains(
            '<label for="login">Username:</label>',
            $result
        );

        $this->assertContains(
            '<label for="password">Password:</label>',
            $result
        );

        $this->assertContains(
            'Username that you used when registering.',
            $result
        );

        $this->assertContains(
            'Your password.',
            $result
        );

        $this->assertContains(
            '<div class="validation-summary-errors"><ul><li>Only &quot;root&quot; can login.</li></ul></div>',
            $result
        );

        $this->assertContains(
            '<span class="field-validation-error">Only &quot;root&quot; can login.</span>',
            $result
        );

        $this->assertContains(
            'value="pupkin"',
            $result
        );

        $this->assertContains(
            'value="123123"',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testLoginPostOnlyFieldRequired() {
        $httpContext = new HttpContext(
            array(
                'REQUEST_URI' => '/account/login',
                'REQUEST_METHOD' => 'POST'
            ),
            array(),
            array(
                'username' => null,
                'password' => null
            )
        );

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            '<h4 class="modal-title">Login for all</h4>',
            $result
        );

        $this->assertContains(
            '<label for="login">Username:</label>',
            $result
        );

        $this->assertContains(
            '<label for="password">Password:</label>',
            $result
        );

        $this->assertContains(
            'Username that you used when registering.',
            $result
        );

        $this->assertContains(
            'Your password.',
            $result
        );

        $this->assertContains(
            '<div class="validation-summary-errors"><ul><li>Password is required.</li><li>username is required. Value must not be empty.</li><li>Only &quot;root&quot; can login.</li></ul></div>',
            $result
        );

        $this->assertContains(
            '<span class="field-validation-error">username is required. Value must not be empty.<br />Only &quot;root&quot; can login.</span>',
            $result
        );

        $this->assertContains(
            '<span class="field-validation-error">Password is required.</span>',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

    public function testLoginPostSuccess() {
        $httpContext = new HttpContext(
            array(
                'REQUEST_URI' => '/account/login',
                'REQUEST_METHOD' => 'POST'
            ),
            array(),
            array(
                'username' => 'root',
                'password' => '123123'
            )
        );

        echo "\n";
        echo 'Request: ' . $httpContext->getRequest()->getRequestUri();

        RouteTable::clear();
        RouteTable::addRoute('default', '{controller=Home}/{action=index}/{id?}');

        ob_start();
        
        Make::magic('PhpMvcTest', $httpContext, $this->basePath);

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertNotContains(
            '<h4 class="modal-title">Login for all</h4>',
            $result
        );

        $this->assertNotContains(
            '<label for="login">Username:</label>',
            $result
        );

        $this->assertNotContains(
            '<label for="password">Password:</label>',
            $result
        );

        $this->assertContains(
            'It\'s a success! Undeniably!',
            $result
        );

        $this->assertNotContains(
            'class="validation-summary-errors"',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo " - OK\n";
    }

}