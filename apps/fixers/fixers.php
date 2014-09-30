<?php
//No direct access
defined('_EXE') or die('Restricted access');

class fixersController extends Controller
{
    public function init() {}

    public function index() {}

    public function duracion()
    {
        //Fix duración entradas
        /*$entradas = Entrada::select();
        foreach ($entradas as $entrada) {
            $entrada->update();
        }*/

        //Eventos
        $fechas = array();
        $eventos = Evento::select();
        foreach ($eventos as $evento) {
            if (!in_array($evento->getFecha(), $fechas)) {
                $fechas[] = $evento->getFecha();
            }
        }
        foreach ($fechas as $fecha) {
            echo "<h3>".$fecha."</h3>";
            Evento::actualizarOrden($fecha);
            Evento::actualizarFechas($fecha);
        }
    }

    public function testTimeDiff()
    {
        echo timeDiff("00:46:02:24", "00:01:14:19", "+");
    }

    public function test()
    {
        $titulo = "EL CIRCULO 1x01";
        $evento = new Evento(592);
        echo str_pad(substr(utf8_decode($evento->titulo), 0 , 16), 16, "X", STR_PAD_RIGHT);
    }
}
