<?php
declare(strict_types=1);

require_once 'HttpContext.php';
require_once 'models/ModelA.php';
require_once 'mvc/controllers/HomeController.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\RouteCollection;
use PhpMvc\SelectListGroup;
use PhpMvc\SelectListItem;
use PhpMvc\InternalHelper;
use PhpMvc\ActionContext;
use PhpMvc\UrlParameter;
use PhpMvc\ViewContext;
use PhpMvc\RouteTable;
use PhpMvc\UrlHelper;
use PhpMvc\Route;
use PhpMvc\Model;
use PhpMvc\Html;

final class HtmlTest extends TestCase
{

    public function testGetTitle(): void {
        $viewContext = $this->setContext();

        $this->assertEquals('Ehlo, world!', Html::getTitle('Ehlo, world!'));

        $viewContext->title = 'Hello, world!';

        $this->assertEquals('Hello, world!', Html::getTitle('Ehlo, world!'));
    }

    public function testDisplay(): void {
        $this->resetModel();

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
        $this->resetModel();

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
        $this->annotateAndValidateModel($viewContext->getModelState());

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
        $this->resetModel();

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
        $this->annotateAndValidateModel($viewContext->getModelState());

        $validationSummary = Html::validationSummary();
        $this->assertEquals('<div class="validation-summary-errors"><ul><li>Program name is required. Value must not be empty.</li></ul></div>', $validationSummary);

        // #3
        $this->resetModel();

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
        $this->annotateAndValidateModel($viewContext->getModelState());

        $validationSummary = Html::validationSummary();
        $this->assertEquals('', $validationSummary);
    }

    public function testValidationMessage(): void {
        // #1
        $this->resetModel();

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
        $this->annotateAndValidateModel($viewContext->getModelState());

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
        $this->resetModel();
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
        $this->resetModel();
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
        $this->resetModel();
        $viewContext = $this->setContext();

        $antiForgeryToken = Html::antiForgeryToken();

        $this->assertRegExp(
            '/\<input type="hidden" name="__requestVerificationToken" id="__requestVerificationToken" value="[0-9a-f]{128}" \/\>/i',
            $antiForgeryToken
        );

        $antiForgeryToken = Html::antiForgeryToken(true);

        $this->assertRegExp(
            '/\<script\>document\.write\(\'\<input type="hidden" name="__requestVerificationToken" id="__requestVerificationToken" value="[0-9a-f]{128}" \/\>\'\);\<\/script\>/i',
            $antiForgeryToken
        );
    }

    public function testForm() {
        $this->resetModel();
        $viewContext = $this->setContext();

        $beginForm = Html::beginForm('post');
        $this->assertEquals('<form method="post" action="/home/post">', $beginForm);

        $beginForm = Html::beginForm('index', 'feedback');
        $this->assertEquals('<form method="post" action="/feedback">', $beginForm);

        $beginForm = Html::beginForm('support', 'feedback', array('subject' => 'hello'), 'get');
        $this->assertEquals('<form method="get" action="/feedback/support?subject=hello">', $beginForm);

        $beginForm = Html::beginForm('support', 'feedback', null, null, true);
        $this->assertRegExp(
            '/\<form method="post" action="\/feedback\/support"\>\<input type="hidden" name="__requestVerificationToken" id="__requestVerificationToken" value="[0-9a-f]{128}" \/\>/i',
            $beginForm
        );

        $beginForm = Html::beginForm('support', 'feedback', null, null, array());
        $this->assertRegExp(
            '/\<form method="post" action="\/feedback\/support"\>\<script\>document\.write\(\'\<input type="hidden" name="__requestVerificationToken" id="__requestVerificationToken" value="[0-9a-f]{128}" \/\>\'\);\<\/script\>/i',
            $beginForm
        );

        $beginForm = Html::beginForm('support', 'feedback', null, null, null, array('class' => 'form-inline', 'id' => 'mainForm'));
        $this->assertEquals('<form class="form-inline" id="mainForm" method="post" action="/feedback/support">', $beginForm);

        $endForm = Html::endForm();
        $this->assertEquals('</form>', $endForm);
    }

    public function testCheckBox(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $checkBox = Html::checkBox('remember');
        $this->assertEquals('<input type="checkbox" name="remember" id="remember" value="true" />', $checkBox);

        // #2
        $this->resetModel();
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'remember' => 'true'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $checkBox = Html::checkBox('remember');
        $this->assertEquals('<input checked="checked" type="checkbox" name="remember" id="remember" value="true" />', $checkBox);

        // #3
        $this->resetModel();
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'remember' => 'false'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $checkBox = Html::checkBox('remember');
        $this->assertEquals('<input type="checkbox" name="remember" id="remember" value="true" />', $checkBox);

        // #4
        $this->resetModel();
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'remember' => ''
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $checkBox = Html::checkBox('remember');
        $this->assertEquals('<input type="checkbox" name="remember" id="remember" value="true" />', $checkBox);

        // #5
        $this->resetModel();
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->boolean = false;

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $checkBox = Html::checkBox('boolean');
        $this->assertEquals('<input type="checkbox" name="boolean" id="boolean" value="true" />', $checkBox);

        // #6
        $this->resetModel();
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->boolean = true;

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $checkBox = Html::checkBox('boolean');
        $this->assertEquals('<input checked="checked" type="checkbox" name="boolean" id="boolean" value="true" />', $checkBox);

        // #7
        $this->resetModel();
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'boolean' => 'TRUE'
            )
        );

        $viewContext->model = $this->getModelA();

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $checkBox = Html::checkBox('boolean');
        $this->assertEquals('<input checked="checked" type="checkbox" name="boolean" id="boolean" value="true" />', $checkBox);
    }

    public function testDropDownList(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $dropDownList = Html::dropDownList('list', array());
        $this->assertEquals('<select name="list" id="list"></select>', $dropDownList);

        // #2
        $dropDownList = Html::dropDownList('list', array('one', 'two', 'three'));
        $this->assertEquals('<select name="list" id="list"><option>one</option><option>two</option><option>three</option></select>', $dropDownList);

        // #3
        $dropDownList = Html::dropDownList('list', array('one', 'two', 'three'), 'two');
        $this->assertEquals('<select name="list" id="list"><option>one</option><option selected="selected">two</option><option>three</option></select>', $dropDownList);

        // #4
        $dropDownList = Html::dropDownList('list', array('one', 'two', 'three'), null, array('class' => 'dropdown', 'name' => 'renamed'));
        $this->assertEquals('<select class="dropdown" name="renamed" id="list"><option>one</option><option>two</option><option>three</option></select>', $dropDownList);

        // #5
        $items = array();
        $items[] = new SelectListItem('one', '1');
        $items[] = new SelectListItem('two', '2');
        $items[] = new SelectListItem('three', '3');

        $dropDownList = Html::dropDownList('list', $items);
        $this->assertEquals('<select name="list" id="list"><option value="1">one</option><option value="2">two</option><option value="3">three</option></select>', $dropDownList);

        // #6
        $items = array();
        $items[] = new SelectListItem('one', '1');
        $items[] = new SelectListItem('two', '2');
        $items[] = new SelectListItem('three', '3', true);

        $dropDownList = Html::dropDownList('list', $items);
        $this->assertEquals('<select name="list" id="list"><option value="1">one</option><option value="2">two</option><option value="3" selected="selected">three</option></select>', $dropDownList);

        // #7
        $group1 = new SelectListGroup('Odd numbers');
        $group2 = new SelectListGroup('Even numbers', true);
        $group3 = new SelectListGroup('Not numbers');

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', true, null, $group1);
        $items[] = new SelectListItem('a', null, null, null, $group3);
        $items[] = new SelectListItem('b', null, null, null, $group3);
        $items[] = new SelectListItem('c', null, null, null, $group3);

        $dropDownList = Html::dropDownList('list', $items);
        $this->assertEquals('<select name="list" id="list"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup><optgroup lable="Not numbers"><option value="">a</option><option value="">b</option><option value="">c</option></optgroup></select>', $dropDownList);

        // #8
        $group1 = new SelectListGroup('Odd numbers');
        $group2 = new SelectListGroup('Even numbers', true);

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', true, null, $group1);
        $items[] = new SelectListItem('a');
        $items[] = new SelectListItem('b');
        $items[] = new SelectListItem('c', null, true);

        $dropDownList = Html::dropDownList('list', $items);
        $this->assertEquals('<select name="list" id="list"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup><option value="">a</option><option value="">b</option><option value="" selected="selected">c</option></select>', $dropDownList);

        // #9
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->number = 3;

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', null, null, $group1);

        $dropDownList = Html::dropDownList('number', $items);
        $this->assertEquals('<select name="number" id="number"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3" selected="selected">three</option><option value="5">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup></select>', $dropDownList);

        // #10
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'number' => '5'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', null, null, $group1);

        $dropDownList = Html::dropDownList('number', $items);
        $this->assertEquals('<select name="number" id="number"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup></select>', $dropDownList);

        // #11
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'list' => 'two'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $dropDownList = Html::dropDownList('list', array('one', 'two', 'three'));
        $this->assertEquals('<select name="list" id="list"><option>one</option><option selected="selected">two</option><option>three</option></select>', $dropDownList);
    }

    public function testHidden(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $hidden = Html::hidden('ghost');
        $this->assertEquals('<input type="hidden" name="ghost" id="ghost" />', $hidden);

        // #2
        $viewContext = $this->setContext();

        $hidden = Html::hidden('ghost', 'casper');
        $this->assertEquals('<input type="hidden" name="ghost" id="ghost" value="casper" />', $hidden);

        // #3
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'ghost' => 'casper'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $hidden = Html::hidden('ghost');
        $this->assertEquals('<input type="hidden" name="ghost" id="ghost" value="casper" />', $hidden);

        // #4
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'ghost' => 'casper'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $hidden = Html::hidden('ghost', 'stinky');
        $this->assertEquals('<input type="hidden" name="ghost" id="ghost" value="casper" />', $hidden);

        // #5
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->number = 42;

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $hidden = Html::hidden('number');
        $this->assertEquals('<input type="hidden" name="number" id="number" value="42" />', $hidden);

        // #6
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->number = 42;

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $hidden = Html::hidden('number', '1024');
        $this->assertEquals('<input type="hidden" name="number" id="number" value="42" />', $hidden);

        // #7
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $hidden = Html::hidden('number', '1024');
        $this->assertEquals('<input type="hidden" name="number" id="number" value="1024" />', $hidden);

        // #8
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'number' => '42'
            )
        );

        $viewContext->model = $this->getModelA();
        $viewContext->model->number = 1024;

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $hidden = Html::hidden('number', '8');
        $this->assertEquals('<input type="hidden" name="number" id="number" value="42" />', $hidden);
    }

    public function testLabel(): void {
        $this->resetModel();
        $viewContext = $this->setContext();

        $label = Html::label('username', 'Username:');
        $this->assertEquals('<label for="username">Username:</label>', $label);

        $label = Html::label('username', '<strong>Username:</strong>');
        $this->assertEquals('<label for="username">&lt;strong&gt;Username:&lt;/strong&gt;</label>', $label);

        $label = Html::label('password', 'Password:', array('style' => 'color:red', 'for' => 'rewrited'));
        $this->assertEquals('<label style="color:red" for="rewrited">Password:</label>', $label);
    }

    public function testListBox(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $listBox = Html::listBox('list', array());
        $this->assertEquals('<select name="list[]" id="list" size="1" multiple="multiple"></select>', $listBox);

        // #2
        $viewContext = $this->setContext();

        $listBox = Html::listBox('list', array(), 10);
        $this->assertEquals('<select name="list[]" id="list" size="10" multiple="multiple"></select>', $listBox);

        // #3
        $listBox = Html::listBox('list', array('one', 'two', 'three'));
        $this->assertEquals('<select name="list[]" id="list" size="1" multiple="multiple"><option>one</option><option>two</option><option>three</option></select>', $listBox);

        // #4
        $listBox = Html::listBox('list', array('one', 'two', 'three'), 5, 'two');
        $this->assertEquals('<select name="list[]" id="list" size="5" multiple="multiple"><option>one</option><option selected="selected">two</option><option>three</option></select>', $listBox);

        // #5
        $listBox = Html::listBox('list', array('one', 'two', 'three'), null, null, array('class' => 'dropdown', 'name' => 'renamed'));
        $this->assertEquals('<select class="dropdown" name="renamed[]" id="list" size="1" multiple="multiple"><option>one</option><option>two</option><option>three</option></select>', $listBox);

        // #6
        $items = array();
        $items[] = new SelectListItem('one', '1');
        $items[] = new SelectListItem('two', '2');
        $items[] = new SelectListItem('three', '3');

        $listBox = Html::listBox('list', $items);
        $this->assertEquals('<select name="list[]" id="list" size="1" multiple="multiple"><option value="1">one</option><option value="2">two</option><option value="3">three</option></select>', $listBox);

        // #7
        $items = array();
        $items[] = new SelectListItem('one', '1');
        $items[] = new SelectListItem('two', '2');
        $items[] = new SelectListItem('three', '3', true);

        $listBox = Html::listBox('list', $items);
        $this->assertEquals('<select name="list[]" id="list" size="1" multiple="multiple"><option value="1">one</option><option value="2">two</option><option value="3" selected="selected">three</option></select>', $listBox);

        // #8
        $group1 = new SelectListGroup('Odd numbers');
        $group2 = new SelectListGroup('Even numbers', true);
        $group3 = new SelectListGroup('Not numbers');

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', true, null, $group1);
        $items[] = new SelectListItem('a', null, null, null, $group3);
        $items[] = new SelectListItem('b', null, null, null, $group3);
        $items[] = new SelectListItem('c', null, null, null, $group3);

        $listBox = Html::listBox('list', $items, 10);
        $this->assertEquals('<select name="list[]" id="list" size="10" multiple="multiple"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup><optgroup lable="Not numbers"><option value="">a</option><option value="">b</option><option value="">c</option></optgroup></select>', $listBox);

        // #9
        $group1 = new SelectListGroup('Odd numbers');
        $group2 = new SelectListGroup('Even numbers', true);

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', true, null, $group1);
        $items[] = new SelectListItem('a');
        $items[] = new SelectListItem('b');
        $items[] = new SelectListItem('c', null, true);

        $listBox = Html::listBox('list', $items, 10);
        $this->assertEquals('<select name="list[]" id="list" size="10" multiple="multiple"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup><option value="">a</option><option value="">b</option><option value="" selected="selected">c</option></select>', $listBox);

        // #10
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->array = array(3);

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', null, null, $group1);

        $listBox = Html::listBox('array', $items);
        $this->assertEquals('<select name="array[]" id="array" size="1" multiple="multiple"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3" selected="selected">three</option><option value="5">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup></select>', $listBox);

        // #11
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'array[]' => array('5')
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', null, null, $group1);

        $listBox = Html::listBox('array', $items);
        $this->assertEquals('<select name="array[]" id="array" size="1" multiple="multiple"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup></select>', $listBox);

        // #12
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'number[]' => array('3', '5')
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $items = array();
        $items[] = new SelectListItem('one', '1', null, null, $group1);
        $items[] = new SelectListItem('two', '2', null, null, $group2);
        $items[] = new SelectListItem('three', '3', null, null, $group1);
        $items[] = new SelectListItem('four', '4', null, null, $group2);
        $items[] = new SelectListItem('five', '5', null, null, $group1);

        $listBox = Html::listBox('number', $items);
        $this->assertEquals('<select name="number[]" id="number" size="1" multiple="multiple"><optgroup lable="Odd numbers"><option value="1">one</option><option value="3" selected="selected">three</option><option value="5" selected="selected">five</option></optgroup><optgroup lable="Even numbers" disabled="disabled"><option value="2">two</option><option value="4">four</option></optgroup></select>', $listBox);

        // #13
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'list[]' => array('two')
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $listBox = Html::listBox('list', array('one', 'two', 'three'));
        $this->assertEquals('<select name="list[]" id="list" size="1" multiple="multiple"><option>one</option><option selected="selected">two</option><option>three</option></select>', $listBox);
    }

    public function testPassword(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $password = Html::password('password');
        $this->assertEquals('<input type="password" name="password" id="password" />', $password);

        // #2
        $viewContext = $this->setContext();

        $password = Html::password('password', '123123');
        $this->assertEquals('<input type="password" name="password" id="password" value="123123" />', $password);

        // #3
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'password' => '123123'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $password = Html::password('password');
        $this->assertEquals('<input type="password" name="password" id="password" value="123123" />', $password);

        // #4
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'password' => '123123'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $password = Html::password('password', '321321');
        $this->assertEquals('<input type="password" name="password" id="password" value="123123" />', $password);

        // #5
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = '123123';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $password = Html::password('text');
        $this->assertEquals('<input type="password" name="text" id="text" value="123123" />', $password);

        // #6
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = '123123';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $password = Html::password('text', '111111');
        $this->assertEquals('<input type="password" name="text" id="text" value="123123" />', $password);

        // #7
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $password = Html::password('text', '123123');
        $this->assertEquals('<input type="password" name="text" id="text" value="" />', $password);

        // #8
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => '123123'
            )
        );

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'hello, world!';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $password = Html::password('text', '000000');
        $this->assertEquals('<input type="password" name="text" id="text" value="123123" />', $password);
    }

    public function testEmail(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $email = Html::email('email');
        $this->assertEquals('<input type="email" name="email" id="email" />', $email);

        // #2
        $viewContext = $this->setContext();

        $email = Html::email('email', 'example@example.org');
        $this->assertEquals('<input type="email" name="email" id="email" value="example@example.org" />', $email);

        // #3
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'email' => 'example@example.org'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $email = Html::email('email');
        $this->assertEquals('<input type="email" name="email" id="email" value="example@example.org" />', $email);

        // #4
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'email' => 'example@example.org'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $email = Html::email('email', '123@example.org', array('class' => 'email-field'));
        $this->assertEquals('<input class="email-field" type="email" name="email" id="email" value="example@example.org" />', $email);

        // #5
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'example@example.org';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $email = Html::email('text');
        $this->assertEquals('<input type="email" name="text" id="text" value="example@example.org" />', $email);

        // #6
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'example@example.org';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $email = Html::email('text', '111111');
        $this->assertEquals('<input type="email" name="text" id="text" value="example@example.org" />', $email);

        // #7
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $email = Html::email('text', 'example@example.org');
        $this->assertEquals('<input type="email" name="text" id="text" value="" />', $email);

        // #8
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'example@example.org'
            )
        );

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'hello@world.org';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $email = Html::email('text', '000000');
        $this->assertEquals('<input type="email" name="text" id="text" value="example@example.org" />', $email);
    }

    public function testRadioButton(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $radio = Html::radioButton('country', 'Russia');
        $this->assertEquals('<input type="radio" name="country" id="country" value="Russia" />', $radio);

        // #2
        $viewContext = $this->setContext();

        $radio = Html::radioButton('country', 'Russia') . Html::radioButton('country', 'USA');
        $this->assertEquals('<input type="radio" name="country" id="country" value="Russia" /><input type="radio" name="country" id="country" value="USA" />', $radio);

        // #3
        $viewContext = $this->setContext();

        $radio = Html::radioButton('country', 'Russia', true) . Html::radioButton('country', 'USA');
        $this->assertEquals('<input checked="checked" type="radio" name="country" id="country" value="Russia" /><input type="radio" name="country" id="country" value="USA" />', $radio);

        // #4
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'country' => 'Russia'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $radio = Html::radioButton('country', 'Russia') . Html::radioButton('country', 'USA');
        $this->assertEquals('<input checked="checked" type="radio" name="country" id="country" value="Russia" /><input type="radio" name="country" id="country" value="USA" />', $radio);

        // #5
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'country' => 'USA'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $radio = Html::radioButton('country', 'Russia') . Html::radioButton('country', 'USA');
        $this->assertEquals('<input type="radio" name="country" id="country" value="Russia" /><input checked="checked" type="radio" name="country" id="country" value="USA" />', $radio);

        // #6
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'country' => 'USA'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $radio = Html::radioButton('country', 'Russia', true) . Html::radioButton('country', 'USA');
        $this->assertEquals('<input type="radio" name="country" id="country" value="Russia" /><input checked="checked" type="radio" name="country" id="country" value="USA" />', $radio);

        // #7
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'Russia';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $radio = Html::radioButton('text', 'Russia') . Html::radioButton('text', 'USA');
        $this->assertEquals('<input checked="checked" type="radio" name="text" id="text" value="Russia" /><input type="radio" name="text" id="text" value="USA" />', $radio);

        // #8
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'USA';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $radio = Html::radioButton('text', 'Russia', true, array('class' => 'russia', 'id' => 'russia')) . Html::radioButton('text', 'USA', null, array('id' => 'usa'));
        $this->assertEquals('<input class="russia" id="russia" type="radio" name="text" value="Russia" /><input id="usa" checked="checked" type="radio" name="text" value="USA" />', $radio);
    }

    public function testTextArea(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text');
        $this->assertEquals('<textarea name="text" id="text"></textarea>', $textarea);

        // #2
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text', 'Hello, world!');
        $this->assertEquals('<textarea name="text" id="text">Hello, world!</textarea>', $textarea);

        // #3
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text', 'Hello, world!', 10);
        $this->assertEquals('<textarea name="text" id="text" rows="10">Hello, world!</textarea>', $textarea);

        // #4
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text', 'Hello, world!', 10, 56);
        $this->assertEquals('<textarea name="text" id="text" rows="10" cols="56">Hello, world!</textarea>', $textarea);

        // #5
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text', 'Hello, world!', null, 56);
        $this->assertEquals('<textarea name="text" id="text" cols="56">Hello, world!</textarea>', $textarea);

        // #6
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text', 'Hello, world!', 10, 56, array("id" => "main-text", "class" => "editor"));
        $this->assertEquals('<textarea id="main-text" class="editor" name="text" rows="10" cols="56">Hello, world!</textarea>', $textarea);

        // #7
        $viewContext = $this->setContext();

        $textarea = Html::textArea('text', '<h1>Hello, world!</h1>');
        $this->assertEquals('<textarea name="text" id="text">&lt;h1&gt;Hello, world!&lt;/h1&gt;</textarea>', $textarea);

        // #8
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textarea = Html::textArea('text');
        $this->assertEquals('<textarea name="text" id="text">Hello, world!</textarea>', $textarea);

        // #9
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textarea = Html::textArea('text', 'Ehlo, world!');
        $this->assertEquals('<textarea name="text" id="text">Hello, world!</textarea>', $textarea);

        // #10
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'Hello, world!';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textarea = Html::textArea('text');
        $this->assertEquals('<textarea name="text" id="text">Hello, world!</textarea>', $textarea);

        // #11
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'Hello,' . chr(10) . 'world!';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textarea = Html::textArea('text', 'Ehlo, world!');
        $this->assertEquals('<textarea name="text" id="text">Hello,' . chr(10) . 'world!</textarea>', $textarea);

        // #12
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textarea = Html::textArea('text', 'Hello, world!');
        $this->assertEquals('<textarea name="text" id="text"></textarea>', $textarea);

        // #13
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!'
            )
        );

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'Ehlo, world!';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textarea = Html::textArea('text', 'The world of hello!');
        $this->assertEquals('<textarea name="text" id="text">Hello, world!</textarea>', $textarea);
    }

    public function testTextBox(): void {
        // #1
        $this->resetModel();
        $viewContext = $this->setContext();

        $textBox = Html::textBox('text');
        $this->assertEquals('<input type="text" name="text" id="text" />', $textBox);

        // #2
        $viewContext = $this->setContext();

        $textBox = Html::textBox('text', 'Hello, world!');
        $this->assertEquals('<input type="text" name="text" id="text" value="Hello, world!" />', $textBox);

        // #3
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textBox = Html::textBox('text');
        $this->assertEquals('<input type="text" name="text" id="text" value="Hello, world!" />', $textBox);

        // #4
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!'
            )
        );

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textBox = Html::textBox('text', 'Ehlo, world!', array('class' => 'text-field'));
        $this->assertEquals('<input class="text-field" type="text" name="text" id="text" value="Hello, world!" />', $textBox);

        // #5
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'Hello, world!';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textBox = Html::textBox('text');
        $this->assertEquals('<input type="text" name="text" id="text" value="Hello, world!" />', $textBox);

        // #6
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'Hello, world!';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textBox = Html::textBox('text', '123');
        $this->assertEquals('<input type="text" name="text" id="text" value="Hello, world!" />', $textBox);

        // #7
        $viewContext = $this->setContext();

        $viewContext->model = $this->getModelA();

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textBox = Html::textBox('text', 'Hello, world!');
        $this->assertEquals('<input type="text" name="text" id="text" value="" />', $textBox);

        // #8
        $viewContext = $this->setContext(
            'POST', 
            array(),
            array(
                'text' => 'Hello, world!'
            )
        );

        $viewContext->model = $this->getModelA();
        $viewContext->model->text = 'EHLO';

        $this->makeActionState($viewContext);
        $this->annotateAndValidateModel($viewContext->getModelState());

        $textBox = Html::textBox('text', 'world?');
        $this->assertEquals('<input type="text" name="text" id="text" value="Hello, world!" />', $textBox);
    }

    public function testEncode() {
        $encode = Html::encode('<h1>Hello, world!</h1><br />This is <strong>encoded</strong> "text"!');
        $this->assertEquals('&lt;h1&gt;Hello, world!&lt;/h1&gt;&lt;br /&gt;This is &lt;strong&gt;encoded&lt;/strong&gt; &quot;text&quot;!', $encode);
    }

    private function getModelA($text = '', $number = null, $boolean = null, $array = null) {
        $result = new ModelA();

        $result->text = $text;
        $result->number = $number;
        $result->boolean = $boolean;
        $result->array = $array;

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
        InternalHelper::setPropertyValue($actionContext, 'controller', new \PhpMvcTest\Controllers\HomeController());
        InternalHelper::setPropertyValue($actionContext, 'actionName', 'index');

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

    private function resetModel() {
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Model', 'annotations', array());
    }

    private function makeActionState($actionContext) {
        $annotations = InternalHelper::getStaticPropertyValue('\\PhpMvc\\Model', 'annotations');
        $modelState = $actionContext->getModelState();
        $modelState->annotations = $annotations;

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