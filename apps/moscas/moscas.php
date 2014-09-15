<?php
//No direct access
defined('_EXE') or die('Restricted access');

class moscasController extends Controller
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
        $this->setData("results", Mosca::select($_REQUEST, $pag['limit'], $pag['limitStart'], $pag['total']));
        $this->setData("pag", $pag);
        $html = $this->view("views.list");
        $this->render($html);
    }

    public function edit()
    {
        $url = Registry::getUrl();
        $this->setData("mosca", new Mosca($url->vars[0]));
        $html = $this->view("views.edit");
        $this->render($html);
    }

    public function save()
    {
        $mosca = new Mosca($_REQUEST['id']);
        if ($mosca->id) {
            if ($mosca->update($_REQUEST)) {
                Registry::addMessage("Mosca actualizada satisfactoriamente", "success", "", Url::site("moscas"));
            }
        } else {
            if ($mosca->insert($_REQUEST)) {
                Registry::addMessage("Mosca creada satisfactoriamente", "success", "", Url::site("moscas"));
            }
        }
        $this->ajax();
    }

    public function delete()
    {
        $url = Registry::getUrl();
        $id = $_REQUEST["id"] ? $_REQUEST["id"] : $url->vars[0];
        $mosca = new Mosca($id);
        if ($mosca->id) {
            if ($mosca->delete()) {
                Registry::addMessage("Mosca eliminada satisfactoriamente", "success");
            }
        }
        Url::redirect(Url::site("moscas"));
    }
}
