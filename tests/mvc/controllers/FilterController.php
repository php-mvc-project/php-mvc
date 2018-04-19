<?php
namespace PhpMvcTest\Controllers;

use \PhpMvc\Filter;
use \PhpMvc\Controller;

class FilterController extends Controller {

    public function __construct() {
        Filter::add('EmptyFilter');

        Filter::add('executingWorld', 'ExecutingWorldFilter');
        Filter::add('executedWorld', 'ExecutedWorldFilter');

        Filter::add('executeWorld', 'ExecutingWorldFilter');
        Filter::add('executeWorld', 'ExecutedWorldFilter');

        Filter::add('executeWorld2', 'ExecutedWorldFilter');
        Filter::add('executeWorld2', 'ExecutingWorldFilter');
        
        Filter::add('error', 'ErrorFilter');

        Filter::add('errorNoResult', 'NoResultErrorFilter');

        Filter::add('errorRewrited', 'NoResultErrorFilter');
        Filter::add('errorRewrited', 'ExecutedWorldFilter');
    }

    public function index() {
        return $this->content('index');
    }

    public function executingWorld() {
        return $this->content('this result will be redefined!');
    }

    public function executedWorld() {
        return $this->content('this result will be redefined!');
    }

    public function executeWorld() {
        return $this->content('this result will be redefined!');
    }

    public function executeWorld2() {
        return $this->content('this result will be redefined!');
    }

    public function error() {
        throw new \Exception('ой!');
    }

    public function errorNoResult() {
        throw new \Exception('error');
    }

    public function errorRewrited() {
        throw new \Exception('just make an exception in this world!');
    }

}