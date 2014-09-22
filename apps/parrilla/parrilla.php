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

    public function json()
    {
        $date = date("Y-m-d", strtotime($_REQUEST["date"]));
        $action = $_REQUEST["action"];
        if ($_REQUEST["toPosition"] && $_REQUEST["fromPosition"]) {
            $action = "order";
        }
        switch ($action) {
            //Order
            case "order":
                $evento = new Evento($_REQUEST["id"]);
                $evento->order($date, $_REQUEST["toPosition"]);
            break;
            //New
            case "new":
                $evento = new Evento();
                $evento->entradaId = $_REQUEST["entradaId"];
                $evento->insert(array("fecha" => $date));
            break;
            //Delete
            case "delete":
                $evento = new Evento($_REQUEST["id"]);
                $evento->delete();
            break;
        }

        //Select
        $eventos = Evento::select(array("fecha" => $date));

        //Data
        $data = array();
        if (count($eventos)) {
            foreach ($eventos as $evento) {
                $data[] = $evento->getDataTablesJson();
            }
        }

        echo json_encode(array(
            "aaData" => $data
        ));
    }

    public function entradasJs()
    {
        $entradas = Entrada::select(array("search" => $_REQUEST["q"]), 10);
        $this->ajax(array("entradas" => $entradas));
    }
}
