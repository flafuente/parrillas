<?php
//No direct access
defined('_EXE') or die('Restricted access');

class entradasController extends Controller
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
        $this->setData("results", Entrada::select($_REQUEST, $pag['limit'], $pag['limitStart'], $pag['total']));
        $this->setData("pag", $pag);
        $html = $this->view("views.list");
        $this->render($html);
    }

    public function edit()
    {
        $url = Registry::getUrl();
        $this->setData("entrada", new Entrada($url->vars[0]));
        $this->setData("moscas", Mosca::select());
        $this->setData("tipos", Tipo::select());
        $this->setData("entradas", Entrada::select());
        $html = $this->view("views.edit");
        $this->render($html);
    }

    public function saveNew()
    {
        $this->save(Url::site("entradas/edit"));
    }

    public function save($redirect = false)
    {
        if (!$redirect) {
            $redirect = Url::site("entradas");
        }
        $entrada = new Entrada($_REQUEST['id']);
        if ($entrada->id) {
            if ($entrada->update($_REQUEST)) {
                Registry::addMessage("Entrada actualizada satisfactoriamente", "success", "", $redirect);
            }
        } else {
            if ($entrada->insert($_REQUEST)) {
                Registry::addMessage("Entrada creada satisfactoriamente", "success", "", $redirect);
            }
        }
        $this->ajax();
    }

    public function delete()
    {
        $url = Registry::getUrl();
        $id = $_REQUEST["id"] ? $_REQUEST["id"] : $url->vars[0];
        $entrada = new Entrada($id);
        if ($entrada->id) {
            if ($entrada->delete()) {
                Registry::addMessage("Entrada eliminada satisfactoriamente", "success");
            }
        }
        Url::redirect(Url::site("entradas"));
    }

    public function ajaxCheckHouseNumber()
    {
        if (Entrada::checkHouseNumber($_REQUEST["houseNumber"], $_REQUEST["tipoId"])) {
            $data["status"] = "ok";
        } else {
            $data["status"] = "error";
        }
        $this->ajax($data);
    }
}