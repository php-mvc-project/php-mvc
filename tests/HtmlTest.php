<?php
declare(strict_types=1);

require_once 'HttpContext.php';
require_once 'models/ModelA.php';
require_once 'mvc/controllers/HomeController.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\UrlHelper;
use PhpMvc\ActionContext;
use PhpMvc\ViewContext;
use PhpMvc\UrlParameter;
use PhpMvc\RouteTable;
use PhpMvc\RouteCollection;
use PhpMvc\Route;
use PhpMvc\Html;
use PhpMvc\Model;

final class HtmlTest extends TestCase
{

    public function testGetTitle(): void {
        $viewContext = $this->setContext();

        $this->assertEquals('Ehlo, world!', Html::getTitle('Ehlo, world!'));

        $viewContext->title = 'Hello, world!';

        $this->assertEquals('Hello, world!', Html::getTitle('Ehlo, world!'));
    }

    public function testDisplay(): void {
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA('Hello, world!');

        Model::display('index', 'text', 'Program', 'The program name.');
        Model::display('index', 'number', 'Lines', 'The lines of program code.');

        $this->makeActionState($viewContext);

        $this->assertEquals('Program', Html::displayName('text'));
        $this->assertEquals('Lines', Html::displayName('number'));
        $this->assertEquals('', Html::displayName('undefined'));

        $this->assertEquals('The program name.', Html::displayText('text'));
        $this->assertEquals('The lines of program code.', Html::displayText('number'));
        $this->assertEquals('', Html::displayText('undefined'));
    }

    public function testValidationSummary(): void {
        // #1
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => '',
                'number' => 123
            )
        );

        $viewContext->model = $this->getModelA();

        Model::required('index', 'text');
        Model::validation('index', 'number', function($value, &$errorMessage) {
            if ($value != 555) {
                $errorMessage = '555 is expected.';
                return false;
            }

            return true;
        });

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->modelState);

        $validationSummary = Html::validationSummary();
        $this->assertEquals('<div class="validation-summary-errors"><ul><li>555 is expected.</li><li>text is required. Value must not be empty.</li></ul></div>', $validationSummary);

        $validationSummary = Html::validationSummary(null, 'section');
        $this->assertEquals('<section class="validation-summary-errors"><ul><li>555 is expected.</li><li>text is required. Value must not be empty.</li></ul></section>', $validationSummary);

        $validationSummary = Html::validationSummary(null, 'div', array('id' => 'errors', 'class' => 'alert alert-danger'));
        $this->assertEquals('<div id="errors" class="alert alert-danger validation-summary-errors"><ul><li>555 is expected.</li><li>text is required. Value must not be empty.</li></ul></div>', $validationSummary);

        $validationSummary = Html::validationSummary('It seems we have some mistakes:', 'article', array('id' => 'errors', 'class' => 'alert alert-danger'));
        $this->assertEquals('<article id="errors" class="alert alert-danger validation-summary-errors">It seems we have some mistakes:<ul><li>555 is expected.</li><li>text is required. Value must not be empty.</li></ul></article>', $validationSummary);

        $validationSummary = Html::validationSummary('It seems we have some mistakes:');
        $this->assertEquals('<div class="validation-summary-errors">It seems we have some mistakes:<ul><li>555 is expected.</li><li>text is required. Value must not be empty.</li></ul></div>', $validationSummary);

        // #2
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => '',
                'number' => 555
            )
        );

        Model::required('index', 'text');
        Model::validation('index', 'number', function($value, &$errorMessage) {
            if ($value != 555) {
                $errorMessage = '555 is expected.';
                return false;
            }

            return true;
        });

        Model::display('index', 'text', 'Program name');

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->modelState);

        $validationSummary = Html::validationSummary();
        $this->assertEquals('<div class="validation-summary-errors"><ul><li>Program name is required. Value must not be empty.</li></ul></div>', $validationSummary);

        // #3
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!',
                'number' => 555
            )
        );

        Model::required('index', 'text');
        Model::required('index', 'number');

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->modelState);

        $validationSummary = Html::validationSummary();
        $this->assertEquals('', $validationSummary);
    }

    public function testValidationMessage(): void {
        // #1
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => '',
                'number' => 123
            )
        );

        $viewContext->model = $this->getModelA();

        Model::required('index', 'text');
        Model::validation('index', 'number', function($value, &$errorMessage) {
            if ($value != 555) {
                $errorMessage = '555 is expected.';
                return false;
            }

            return true;
        });

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->modelState);

        $validationMessage = Html::validationMessage('text');
        $this->assertEquals('<span class="field-validation-error">text is required. Value must not be empty.</span>', $validationMessage);

        $validationMessage = Html::validationMessage('text', null, 'div');
        $this->assertEquals('<div class="field-validation-error">text is required. Value must not be empty.</div>', $validationMessage);

        $validationMessage = Html::validationMessage('text', null, 'div', array('id' => 'textError', 'class' => 'alert alert-danger'));
        $this->assertEquals('<div id="textError" class="alert alert-danger field-validation-error">text is required. Value must not be empty.</div>', $validationMessage);

        $validationMessage = Html::validationMessage('text', 'Please correct the following errors:', 'h1', array('id' => 'textError', 'class' => 'alert alert-danger', 'data-type' => 'test'));
        $this->assertEquals('<h1 id="textError" class="alert alert-danger field-validation-error" data-type="test">Please correct the following errors:<br />text is required. Value must not be empty.</h1>', $validationMessage);

        $validationMessage = Html::validationMessage('number');
        $this->assertEquals('<span class="field-validation-error">555 is expected.</span>', $validationMessage);
    }

    public function testAction() {
        $viewContext = $this->setContext();

        $action = Html::action('page');
        $this->assertEquals('/home/page', $action);

        $action = Html::action('index');
        $this->assertEquals('/home', $action);

        $action = Html::action('Index', 'Forum');
        $this->assertEquals('/forum', $action);

        $action = Html::action('show', 'forum', array('id' => '123'));
        $this->assertEquals('/forum/show/123', $action);

        $action = Html::action('show', 'forum', array('id' => '123'), 'message-5');
        $this->assertEquals('/forum/show/123#message-5', $action);

        $action = Html::action('index', 'forum', null, 'last');
        $this->assertEquals('/forum#last', $action);

        $action = Html::action('show', 'forum', array('id' => '123'), 'message-5', 'https', 'www.example.org');
        $this->assertEquals('https://www.example.org/forum/show/123#message-5', $action);

        $action = Html::action('show', 'forum', array('id' => '123'), '100', 'https');
        $this->assertEquals('https://example.org/forum/show/123#100', $action);

        $action = Html::action('show', 'forum', array('id' => '123'), '321', null, 'api.example.org');
        $this->assertEquals('http://api.example.org/forum/show/123#321', $action);

        $action = Html::action('show', 'forum', array('id' => '123', 'search' => 'hello'), '321', null, 'api.example.org');
        $this->assertEquals('http://api.example.org/forum/show/123?search=hello#321', $action);
    }

    public function testActionLink() {
        $viewContext = $this->setContext();

        $action = Html::actionLink('link', 'page');
        $this->assertEquals('<a href="/home/page">link</a>', $action);

        $action = Html::actionLink('home', 'index');
        $this->assertEquals('<a href="/home">home</a>', $action);

        $action = Html::actionLink('forum', 'Index', 'Forum');
        $this->assertEquals('<a href="/forum">forum</a>', $action);

        $action = Html::actionLink('show topic', 'show', 'forum', array('id' => '123'));
        $this->assertEquals('<a href="/forum/show/123">show topic</a>', $action);

        $action = Html::actionLink('Show in new window', 'show', 'forum', array('id' => '123'), array('class' => 'btn btn-default', 'id' => 'message', 'target' => '_blank'));
        $this->assertEquals('<a href="/forum/show/123" class="btn btn-default" id="message" target="_blank">Show in new window</a>', $action);

        $action = Html::actionLink('Show in new window', 'show', 'forum', array('id' => '123', 'hightlight' => 'test'), array('class' => 'btn btn-default', 'id' => 'message', 'target' => '_blank'));
        $this->assertEquals('<a href="/forum/show/123?hightlight=test" class="btn btn-default" id="message" target="_blank">Show in new window</a>', $action);

        $action = Html::actionLink('Show in new window', 'show', 'forum', array('id' => '123', 'hightlight' => 'test'), array('class' => 'btn btn-default', 'id' => 'message', 'target' => '_blank'), '555');
        $this->assertEquals('<a href="/forum/show/123?hightlight=test#555" class="btn btn-default" id="message" target="_blank">Show in new window</a>', $action);

        $action = Html::actionLink('Show in new window', 'show', 'forum', array('id' => '123', 'hightlight' => 'test'), array('class' => 'btn btn-default', 'id' => 'message', 'target' => '_blank'), '555', 'https');
        $this->assertEquals('<a href="https://example.org/forum/show/123?hightlight=test#555" class="btn btn-default" id="message" target="_blank">Show in new window</a>', $action);

        $action = Html::actionLink('Show in new window', 'show', 'forum', array('id' => '123', 'hightlight' => 'test'), array('class' => 'btn btn-default', 'id' => 'message', 'target' => '_blank'), '555', 'https', 'www.example.org');
        $this->assertEquals('<a href="https://www.example.org/forum/show/123?hightlight=test#555" class="btn btn-default" id="message" target="_blank">Show in new window</a>', $action);
    }

    public function testAntiForgeryToken() {
        $viewContext = $this->setContext();

        $antiForgeryToken = Html::antiForgeryToken();

        $this->assertRegExp(
            '/\<input type="hidden" name="__requestVerificationToken" value="[0-9a-f]{128}" \/\>/i',
            $antiForgeryToken
        );

        $antiForgeryToken = Html::antiForgeryToken(true);

        $this->assertRegExp(
            '/\<script\>document\.write\(\'\<input type="hidden" name="__requestVerificationToken" value="[0-9a-f]{128}" \/\>\'\);\<\/script\>/i',
            $antiForgeryToken
        );
    }

    public function testForm() {
        $viewContext = $this->setContext();

        $beginForm = Html::beginForm('post');
        $this->assertEquals('<form method="post" action="/home/post">', $beginForm);

        $beginForm = Html::beginForm('index', 'feedback');
        $this->assertEquals('<form method="post" action="/feedback">', $beginForm);

        $beginForm = Html::beginForm('support', 'feedback', array('subject' => 'hello'), 'get');
        $this->assertEquals('<form method="get" action="/feedback/support?subject=hello">', $beginForm);

        $beginForm = Html::beginForm('support', 'feedback', null, null, true);
        $this->assertRegExp(
            '/\<form method="post" action="\/feedback\/support"\>\<input type="hidden" name="__requestVerificationToken" value="[0-9a-f]{128}" \/\>/i',
            $beginForm
        );

        $beginForm = Html::beginForm('support', 'feedback', null, null, array());
        $this->assertRegExp(
            '/\<form method="post" action="\/feedback\/support"\>\<script\>document\.write\(\'\<input type="hidden" name="__requestVerificationToken" value="[0-9a-f]{128}" \/\>\'\);\<\/script\>/i',
            $beginForm
        );

        $beginForm = Html::beginForm('support', 'feedback', null, null, null, array('class' => 'form-inline', 'id' => 'mainForm'));
        $this->assertEquals('<form class="form-inline" id="mainForm" method="post" action="/feedback/support">', $beginForm);

        $endForm = Html::endForm();
        $this->assertEquals('</form>', $endForm);
    }

    private function getModelA($text = '', $number = null, $boolean = null) {
        $result = new ModelA();

        $result->text = $text;
        $result->number = $number;
        $result->boolean = $boolean;

        return $result;
    }

    private function setContext($method = 'GET', $get = array(), $post = array(), $actionResult = null, $viewData = null) {
        $routes = new RouteCollection();
        $routes->add(new Route('default', '{controller=home}/{action=index}/{id?}'));

        $httpContext = new HttpContext(
            $routes,
            array(
                'REQUEST_URI' => '/',
                'REQUEST_METHOD' => $method,
                'HTTP_HOST' => 'example.org',
                'SERVER_PORT' => 80
            ),
            $get, $post
        );

        $actionContext = new ActionContext($httpContext);
        $actionContext->controller = new \PhpMvcTest\Controllers\HomeController();
        $actionContext->actionName = 'index';

        $viewContext = new ViewContext($actionContext, $actionResult, $viewData);

        $viewContextProperty = new \ReflectionProperty('\PhpMvc\Html', 'viewContext');
        $viewContextProperty->setAccessible(true);
        $viewContextProperty->setValue(null, $viewContext);

        $modelActionContextProperty = new \ReflectionProperty('\PhpMvc\Model', 'actionContext');
        $modelActionContextProperty->setAccessible(true);
        $modelActionContextProperty->setValue(null, $actionContext);

        $requestProperty = new \ReflectionProperty('\PhpMvc\Make', 'request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue(null, $httpContext->getRequest());

        $responseProperty = new \ReflectionProperty('\PhpMvc\Make', 'response');
        $responseProperty->setAccessible(true);
        $responseProperty->setValue(null, $httpContext->getResponse());

        return $viewContext;
    }

    private function makeActionState($actionContext) {
        $makeActionStateMethod = new \ReflectionMethod('\PhpMvc\Make', 'makeActionState');
        $makeActionStateMethod->setAccessible(true);
        $makeActionStateMethod->invoke(null, $actionContext);
    }

    private function annotateAndValidateModel($modelState) {
        $makeActionStateMethod = new \ReflectionMethod('\PhpMvc\Make', 'annotateAndValidateModel');
        $makeActionStateMethod->setAccessible(true);
        $makeActionStateMethod->invoke(null, $modelState);
    }

}