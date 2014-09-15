<?php
//No direct access
defined('_EXE') or die('Restricted access');

class tiposController extends Controller
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
        $config = Registry::getConfig();
        $pag['total'] = 0;
        $pag['limit'] = $_REQUEST['limit'] ? $_REQUEST['limit'] : $config->get("defaultLimit");
        $pag['limitStart'] = $_REQUEST['limitStart'];
        $this->setData("results", Tipo::select($_REQUEST, $pag['limit'], $pag['limitStart'], $pag['total']));
        $this->setData("pag", $pag);
        $html = $this->view("views.list");
        $this->render($html);
    }

    public function edit()
    {
        $url = Registry::getUrl();
        $this->setData("tipo", new Tipo($url->vars[0]));
        $html = $this->view("views.edit");
        $this->render($html);
    }

    public function save()
    {
        $tipo = new Tipo($_REQUEST['id']);
        if ($tipo->id) {
            if ($tipo->update($_REQUEST)) {
                Registry::addMessage("Tipo de entrada actualizado satisfactoriamente", "success", "", Url::site("tipos"));
            }
        } else {
            if ($tipo->insert($_REQUEST)) {
                Registry::addMessage("Tipo de entrada creado satisfactoriamente", "success", "", Url::site("tipos"));
            }
        }
        $this->ajax();
    }

    public function delete()
    {
        $url = Registry::getUrl();
        $id = $_REQUEST["id"] ? $_REQUEST["id"] : $url->vars[0];
        $tipo = new Tipo($id);
        if ($tipo->id) {
            if ($tipo->delete()) {
                Registry::addMessage("Tipo de entrada eliminado satisfactoriamente", "success");
            }
        }
        Url::redirect(Url::site("tipos"));
    }
}
