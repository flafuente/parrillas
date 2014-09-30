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

    public function export()
    {
        $date = date("Y-m-d", strtotime($_REQUEST["fecha"]));
        //Force order
        Evento::actualizarFechas($date);
        $eventos = Evento::select(array("fecha" => $date));
        $output = "";
        if (count($eventos)) {
            foreach ($eventos as $i => $evento) {
                $output .= $evento->telson(($i == 0))."\n";
            }
            $filename = date("Ymd", strtotime($evento->getFecha()))."Tribro.txt";
            header('Content-Type: text/plain');
            header("Content-Disposition: attachment; filename=".$filename);
            echo utf8_encode(clearDiacritics(utf8_decode($output)));
            exit;
        } else {
            Registry::addMessage("Esta parrilla no tiene eventos", "error");
            Url::redirect(Url::site("parrilla"));
        }
    }

    public function json()
    {
        $date = date("Y-m-d", strtotime($_REQUEST["date"]));
        $hour = $_REQUEST["hour"];
        $action = $_REQUEST["action"];

        if (is_numeric($_REQUEST["order"]) && (int) $_REQUEST["order"] > 0) {
            $orden = (int) $_REQUEST["order"];
        }
        if ($_REQUEST["toPosition"] && $_REQUEST["fromPosition"]) {
            $action = "order";
        }

        switch ($action) {
            //Order
            case "order":
                $evento = new Evento($_REQUEST["id"]);
                $evento->order($date, $_REQUEST["toPosition"], $hour);
            break;
            //Hour update
            case "updateHour":
                Evento::actualizarFechas($date, $hour);
            break;
            //New
            case "new":
                $evento = new Evento();
                $evento->entradaId = $_REQUEST["entradaId"];
                $evento->insert(array("fecha" => $date, "hora" => $hour, "order" => $orden));
            break;
            //Delete
            case "delete":
                $evento = new Evento($_REQUEST["id"]);
                $evento->delete();
                //Actualizamos el orden
                Evento::actualizarOrden($date);
                //Actualizamos las fechas
                Evento::actualizarFechas($date, $hour);
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
        $entradas = Entrada::select(array("search" => $_REQUEST["q"]), 30);
        $this->ajax(array("entradas" => $entradas));
    }
}
