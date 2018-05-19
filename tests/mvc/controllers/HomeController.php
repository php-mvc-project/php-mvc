<?php
namespace PhpMvcTest\Controllers;

use \PhpMvc\View;
use \PhpMvc\Controller;
use \PhpMvc\ViewResult;

use \PhpMvcTest\Models\HomePage;

class HomeController extends Controller {

    public function __construct() {
        return $this->content('It\'s not safe!');
    }

    public function index($id) {
        $model = new HomePage();
        
        $model->title = 'Hello world!';
        $model->text = 'This data is transferred to the view through the model!';
        $model->number = 123;

        return $this->view($model);
    }

    public function makeView() {
        $view = new ViewResult();

        $view->viewFile = 'prog';
        $view->title = 'The view is created programmatically';
        $view->layout = '_default';

        $model = new HomePage();

        $model->title = 'World hello!';
        $model->text = 'The model contains text.';
        $model->number = 321;

        $view->model = $model;

        $view->viewData['key1'] = 'value1';
        $view->viewData['key2'] = 'value2';
        $view->viewData['key3'] = 'value3';

        return $view;
    }

    public function specifiedView() {
        return $this->view('hello', 'Test message passed to the view file "hello.php" from the action method "specifiedView".', '_lite');
    }

    public function setGetData() {
        $this->setData('key', 'value');
        $this->setData('message', 'Hello world!');
        $this->setData('option', true);
        $this->setData('int', 123);

        return $this->view('index');
    }

    public function differentWaysViewData() {
        $this->setData('setData', 'Hello setData!');
        $this->setData('setData2', 'Hello setData2!');

        $view = new ViewResult();

        $view->viewFile = '~/views/home/index.php';
        $view->layout = '_default';

        $view->viewData['viewData'] = 'Hello viewData!';
        $view->viewData['viewData2'] = 'Hello viewData2!';
        $view->viewData['setData2'] = 'Replaced setData2!';

        // TODO: one common collection of data is needed
        $this->setData('viewData2', 'This change will be ignored');

        return $view;
    }

    public function noViewFile() {
        return $this->view();
    }

    public function jsonData() {
        $data = array(1, 2, 3, 4, 5);

        return $this->json($data);
    }

    public function getContent() {
        return $this->content('This is plain text', 'text/plain');
    }

    public function string() {
        return 'hello world!';
    }

    public function arr() {
        return array(1, 2, 3, 4, 5);
    }

    public function obj() {
        $model = new HomePage();

        $model->title = 'Object result';
        $model->text = 'This object will be returned in JSON format!';
        $model->number = 555;

        return $model;
    }

    public function getImage($download = false) {
        if ($download) {
            return $this->file('~/content/images/php.png', 'image/png', true);
        }
        else {
            return $this->file('~/content/images/php.png', 'image/png');
        }
    }

    public function layoutWithParent() {
        $view = new ViewResult();

        $view->viewFile = 'prog';
        $view->title = 'The view is created programmatically';
        $view->layout = '_parent';

        $model = new HomePage();

        $model->title = 'Hello work!';
        $model->text = 'The model contains text.';
        $model->number = 42;

        $view->model = $model;

        return $view;
    }

    public function justModel($m) {
        return $this->view('index');
    }

}