<?php
namespace PhpMvcTest\Controllers;

use \PhpMvc\Model;
use \PhpMvc\Controller;

use \PhpMvcTest\Models\Login;

class AccountController extends Controller {

    public function __construct() {
        Model::required('login', 'username', array('password' => 'Password is required.'));
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
                $this->setViewData('success', true);
                return $this->view();
            }
        }

        return $this->view($model);
    }

}