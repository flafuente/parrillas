<?php
//No direct access
defined('_EXE') or die('Restricted access');

class parrillaController extends Controller
{
    public function init()
    {
        $user = Registry::getUser();
        if (!$user->id) {
            Url::redirect(Url::site("login"));
        }
    }

    public function index()
    {
        $html = $this->view("views.parrilla");
        $this->render($html);
    }
}
