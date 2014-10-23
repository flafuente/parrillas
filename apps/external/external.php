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

    public function updateEntrada()
    {
        $error = null;
        $entrada = new Entrada($_REQUEST["id"]);
        if ($entrada->id) {
            $entrada->programaId = $_REQUEST['programaId'];
            $entrada->programa = $_REQUEST['programa'];
            $entrada->capitulo = $_REQUEST['capitulo'];
            $entrada->titulo = ($entrada->titulo) ? $entrada->titulo : $_REQUEST['titulo'];
            if ($entrada->update()) {
                $status = "ok";
            } else {
                $status = "error";
                $error = current(Registry::getMessages())->message;
            }
        } else {
            $status = "error";
            $error = "Entrada no encontrada";
        }
        $this->ajax(array("status" => $status, "error" => $error));
    }

    public function parrilla()
    {
        //Select
        $eventos = Evento::select(array("fecha" => $_REQUEST["fecha"], "tipo" => "P"));
        $this->ajax(array("eventos" => $eventos));
    }

    public function capitulosByHouseNumber()
    {
        $capitulo = Entrada::getCapituloByHouseNumber($_REQUEST["houseNumber"]);
        $this->ajax(array("capitulo" => $capitulo));
    }
}
