<?php
declare(strict_types=1);

require_once 'HttpContext.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\Route;
use PhpMvc\AppBuilder;
use PhpMvc\UrlParameter;
use PhpMvc\RouteCollection;
use PhpMvc\DefaultRouteProvider;

final class AppBuilderTest extends TestCase
{

    protected $preserveGlobalState = false;

    protected $runTestInSeparateProcess = true;

    private $basePath;

    private $defaultRoutes;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->basePath = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mvc';

        if (!defined('PHPMVC_DS')) {
            define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('PHPMVC_ROOT_PATH')) {
            define('PHPMVC_ROOT_PATH', $this->basePath . PHPMVC_DS);
        }

        AppBuilder::useNamespace('PhpMvcTest');
        AppBuilder::useBasePath($this->basePath);
    }

    public function testBuild(): void
    {
        $httpContext = HttpContext::get('/')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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

        echo ' - OK' . chr(10);
    }

    public function testSpecifiedView(): void
    {
        $httpContext = HttpContext::get('/home/specifiedView')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertContains(
            'Test message passed to the view file "hello.php" from the action method "specifiedView".',
            $result
        );

        echo ' - OK' . chr(10);
    }

    public function testMakeView() {
        $httpContext = HttpContext::get('/home/makeview')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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

        echo ' - OK' . chr(10);
    }

    public function testSetGetData() {
        $httpContext = HttpContext::get('/home/setGetData')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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

        echo ' - OK' . chr(10);
    }

    public function testDifferentWaysViewData() {
        $httpContext = HttpContext::get('/home/differentWaysViewData')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertContains(
            '<header>',
            $result
        );

        $this->assertContains(
            'Hello setData!',
            $result
        );

        $this->assertContains(
            'Replaced setData2!',
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

        echo ' - OK' . chr(10);
    }

    public function testNoViewFile() {
        $httpContext = HttpContext::get('/home/noViewFile')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        $this->expectExceptionMessageRegExp('/The view file could not be found/');

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        echo ' - OK' . chr(10);
    }

    public function testJsonData() {
        $httpContext = HttpContext::get('/home/jsonData')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('[1,2,3,4,5]', $result);

        echo ' - OK' . chr(10);
    }

    public function testGetContent() {
        $httpContext = HttpContext::get('/home/getContent')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('This is plain text', $result);

        echo ' - OK' . chr(10);
    }

    public function testString() {
        $httpContext = HttpContext::get('/home/string')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('hello world!', $result);

        echo ' - OK' . chr(10);
    }

    public function testArr() {
        $httpContext = HttpContext::get('/home/arr')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('[1,2,3,4,5]', $result);

        echo ' - OK' . chr(10);
    }

    public function testObj() {
        $httpContext = HttpContext::get('/home/obj')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('{"title":"Object result","text":"This object will be returned in JSON format!","number":555}', $result);

        echo ' - OK' . chr(10);
    }

    public function testGetImage() {
        $httpContext = HttpContext::get('/home/getImage')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('89504e470d0a1a0a0000', $result);

        echo ' - OK' . chr(10);
    }

    public function testLoginGet() {
        $httpContext = HttpContext::get('/account/login')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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
            '<form id="loginForm" action="/account/login" method="post" enctype="application/x-www-form-urlencoded">',
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
            '<a href="/" class="btn btn-default">Cancel</a>',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        echo ' - OK' . chr(10);
    }

    public function testLoginPostOnlyRootCanLogin() {
        $httpContext = HttpContext::post('/account/login', array(
            'username' => 'pupkin',
            'password' => '123123'
        ))->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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

        echo ' - OK' . chr(10);
    }

    public function testLoginPostOnlyFieldRequired() {
        $httpContext = HttpContext::post('/account/login', array(
            'username' => null,
            'password' => null
        ))->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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
            '<div class="validation-summary-errors"><ul><li>Password is required.</li><li>Username is required. Value must not be empty.</li><li>Only &quot;root&quot; can login.</li></ul></div>',
            $result
        );

        $this->assertContains(
            '<span class="field-validation-error">Username is required. Value must not be empty.<br />Only &quot;root&quot; can login.</span>',
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

        echo ' - OK' . chr(10);
    }

    public function testLoginPostSuccess() {
        $httpContext = HttpContext::post('/account/login', array(
            'username' => 'root',
            'password' => '123123'
        ))->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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

        echo ' - OK' . chr(10);
    }

    public function testActionNameValidation() {
        $this->expectException(\PhpMvc\ActionNameValidationException::class);

        $httpContext = HttpContext::get('/home/__construct')->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        echo ' - OK' . chr(10);
    }

    public function testDisabledActionNameValidation() {
        $httpContext = HttpContext::get('/home/__construct')->useDefaultRoute()->noOutputHeaders();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useValidation(array('actionName' => false));
        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('It\'s not safe!', $result);

        echo ' - OK' . chr(10);
    }

    public function testCrossSiteScriptingValidation() {
        $this->expectException(\PhpMvc\HttpRequestValidationException::class);

        $httpContext = HttpContext::post('/account/login', array(
            'username' => '<root>',
            'password' => '&#!'
        ))->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        echo ' - OK' . chr(10);
    }

    public function testDisabledCrossSiteScriptingValidation() {
        $httpContext = HttpContext::post('/account/login', array(
            'username' => '<root>',
            'password' => '&#!'
        ))->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useValidation(array('crossSiteScripting' => false));
        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertContains(
            '&lt;root>',
            $result
        );

        echo ' - OK' . chr(10);
    }

    public function testStaticFile() {
        $routes = new DefaultRouteProvider();
        $routes->ignore('content/{files}', array('files' => '.+'));
        $routes->add('default', '{controller=Home}/{action=index}/{id?}');

        $httpContext = HttpContext::get('/content/images/php.png')->noOutputHeaders()->setRoutes($routes);

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

        $result = ob_get_clean();

        $this->assertEquals('89504e470d0a1a0a0000', bin2hex(substr($result, 0, 10)));

        echo ' - OK' . chr(10);
    }

    public function testViewContext(): void
    {
        $httpContext = HttpContext::get('/home/layoutWithParent')->noOutputHeaders()->useDefaultRoute();

        echo chr(10);
        echo 'Request: ' . $httpContext->getRequest()->rawUrl();

        ob_start();

        AppBuilder::useHttpContext($httpContext);
        AppBuilder::build();

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
            'Default layout with empty parent',
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
            'Hello work!',
            $result
        );

        $this->assertContains(
            '<footer>',
            $result
        );

        $this->assertContains(
            'View file:',
            $result
        );

        $this->assertContains(
            '_empty.php',
            $result
        );

        $this->assertContains(
            '_parent.php',
            $result
        );

        $this->assertContains(
            'prog.php',
            $result
        );

        $this->assertContains(
            'header.php',
            $result
        );

        $this->assertContains(
            'footer.php',
            $result
        );

        echo ' - OK' . chr(10);
    }

}