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
        $entradas = Entrada::select();
        foreach ($entradas as $entrada) {
            $entrada->update();
        }

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
            Evento::actualizarFechas($fecha);
        }
    }

    public function testTimeDiff()
    {
        echo timeDiff("00:00:00:01", "00:12:01:00");
    }
}
