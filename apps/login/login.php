<?php
//No direct access
defined('_EXE') or die('Restricted access');

/**
 * User login Controller
 */
class loginController extends Controller
{
    /**
     * Init
     */
    public function init() {}

    /**
     * Default view
     */
    public function index()
    {
        //Load Login form view
        $this->login();
    }

    /**
     * Login form view
     */
    public function login()
    {
        //Load View to Template var
        $html = $this->view("views.login");
        //Render the Template
        $this->render($html);
    }

    /**
     * Login action
     */
    public function doLogin()
    {
        $user = User::login($_REQUEST['login'], $_REQUEST['password']);
        //Try to login
        if ($user->id) {
            //Add success message
            Registry::addMessage("", "", "", Url::site());
        } else {
            //Add error message and redirect to login form view
            Registry::addMessage("Datos incorrectos", "error", "login");
        }
        //Show ajax JSON response
        $this->ajax();
    }

    /**
     * Logout action
     */
    public function doLogout()
    {
        $user = Registry::getUser();
        if ($user->id) {
            //Logout
            $user->logout();
        }
        //Redirect to index
        Url::redirect();
    }
}
