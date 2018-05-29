<?php
namespace PhpMvcTest\Controllers;

use \PhpMvc\Model;
use \PhpMvc\Controller;
use \PhpMvc\OutputCache;

use \PhpMvcTest\Models\Login;

class AccountController extends Controller {

    public function __construct() {
        Model::set('login', 'login');

        Model::required('login', 'username');
        Model::required('login', 'password', 'Password is required.');
        
        Model::display('login', 'username', 'Username', 'Username that you used when registering.'); 
        Model::display('login', 'password', 'Password', 'Your password.');

        Model::validation('login', 'username', function($value, &$errorMessage) {
            if ($value != 'root') {
                $errorMessage = 'Only "root" can login.';
                return false;
            }
            
            return true;
        });
    }

    public function login($model) {
        if ($this->isPost()) {
            $modelState = $this->getModelState();

            if ($modelState->isValid()) {
                $this->setData('success', true);
                return $this->view();
            }
        }

        if (!isset($model)) {
            $model = new \PhpMvcTest\Models\Login();
        }

        return $this->view($model);
    }

}