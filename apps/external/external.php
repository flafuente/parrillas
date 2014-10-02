<?php
//No direct access
defined('_EXE') or die('Restricted access');

class externalController extends Controller
{
    public function init() {}

    public function index() {}

    public function entradas()
    {
        $entradas = Entrada::select(
            array(
                "search" => $_REQUEST["q"],
                "tipo" => "P"
            ), 30
        );
        $this->ajax(array("entradas" => $entradas));
    }

    public function entrada()
    {
        $entrada = new Entrada($_REQUEST["id"]);
        $this->ajax(array("entrada" => $entrada));
    }

    public function parrilla()
    {
        //Select
        $eventos = Evento::select(array("fecha" => $_REQUEST["fecha"]));
        $this->ajax(array("eventos" => $eventos));
    }
}
