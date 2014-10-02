<?php
//No direct access
defined('_EXE') or die('Restricted access');

class zG2sH0A7hwdnLNUUQaoU25cmController extends Controller
{
    public function init() {}

    public function index()
    {
        $html = $this->view("views.parrilla");
        $this->render($html);
    }

    public function json()
    {
        $date = date("Y-m-d", strtotime($_REQUEST["date"]));

        //Select
        $eventos = Evento::select(array("fecha" => $date));

        //Data
        $data = array();
        if (count($eventos)) {
            foreach ($eventos as $evento) {
                $row = $evento->getDataTablesJson();
                $row[10] = "";
                $data[] = $row;
            }
        }

        echo json_encode(array(
            "aaData" => $data
        ));
    }
}
