<?php
/**
 * Modelo Evento
 *
 * @package Tribo\Modelos
 */
class Evento extends Model
{
    /**
     * Id
     * @var int
     */
    public $id;
    /**
     * Order
     * @var int
     */
    public $order;
    /**
     * Id del usuario creador
     * @var int
     */
    public $userId;
    /**
     * Entrada Id
     * @var int
     */
    public $entradaId;
    /**
     * House Number
     * @var int
     */
    public $houseNumber;
    /**
     * Tipo
     * @var string
     */
    public $tipo;
    /**
     * Título
     * @var string
     */
    public $titulo;
    /**
     * TC IN
     * @var string
     */
    public $tcIn;
    /**
     * Logo
     * @var string
     */
    public $logo;
    /**
     * Segmento
     * @var string
     */
    public $segmento;
    /**
     * Fecha de inicio (Y-m-d H:i:s)
     * @var string
     */
    public $fechaInicio;
    /**
     * Fecha fin (Y-m-d H:i:s)
     * @var string
     */
    public $fechaFin;
    /**
     * Duración  (H:i:s:frames)
     * @var string
     */
    public $duracion;
    /**
     * Fecha de creación
     * @var string
     */
    public $dateInsert;
    /**
     * Fecha de modificación
     * @var string
     */
    public $dateUpdate;

    /**
     * Init.
     * @return void
     */
    public function init()
    {
        //Tabla usada en la DB
        parent::$dbTable = "eventos";
    }

    /**
     * Acciones previas a la creación.
     * @return void
     */
    public function preInsert($data = array())
    {
        $user = Registry::getUser();
        $this->userId = $user->id;
        $this->dateInsert = date("Y-m-d H:i:s");

        //Entrada
        $entrada = new Entrada($this->entradaId);

        //Tiene ED?
        if ($entrada->entradaIdEd) {
            $evento = new Evento();
            $evento->insert(array("fecha" => $data["fecha"], "entradaId" => $entrada->entradaIdEd));
        }

        //Datos entrada
        $this->tipo = $entrada->tipoId;
        $this->houseNumber = $entrada->houseNumber;
        $this->titulo = $entrada->nombre;
        $this->duracion = $entrada->duracion;
        $this->tcIn = $entrada->tcIn;
        $this->segmento = $entrada->segmento;

        //Fechas / Orden
        $previousEvent = self::getPreviousEvent($data["fecha"]);
        if ($previousEvent->id) {
            //Orden
            $this->order = $previousEvent->order + 1;
            //Inicio
            $this->fechaInicio = $previousEvent->fechaFin;
            //Fin
            $this->calcFechaFin();
        } else {
            //Orden
            $this->order = 1;
            //Inicio
            $this->fechaInicio = $data["fecha"]." 07:00:00:00";
            //Fin
            $this->calcFechaFin();
        }

    }

    public function postInsert($data = array())
    {
        //Entrada
        $entrada = new Entrada($this->entradaId);

        //Tiene FIN?
        if ($entrada->entradaIdFin) {
            $evento = new Evento();
            $evento->insert(array("fecha" => $data["fecha"], "entradaId" => $entrada->entradaIdFin));
        }
    }

    private function calcFechaFin()
    {
        //$seconds = strtotime("1970-01-01 ".$this->duracion." UTC");
        //$this->fechaFin = date("Y-m-d H:i:s", strtotime($this->fechaInicio) + $seconds);

        $this->fechaFin = dateAddTime($this->fechaInicio, $this->duracion);
    }

    private static function getPreviousEvent($fecha, $order = null)
    {
        $previousEvent = Evento::select(array("fecha" => $fecha, "orderNum" => $order, "order" => "order", "orderDir" => "DESC"), 1);

        return $previousEvent[0];
    }

    public function order($fecha, $toPosition)
    {
        //Actualizamos la posición del evento
        $this->order = $toPosition;
        $this->update();

        //Actualizamos el orden
        $this->actualizarOrden($fecha, $this->id);

        //Actualizamos las fechas
        self::actualizarFechas($fecha);
    }

    public static function actualizarOrden($fecha, $ignoreId = null)
    {
        if ($ignoreId) {
            $ignoredEvento = new Evento($ignoreId);
        }
        //Actualizamos el orden
        $eventos = Evento::select(array("fecha" => $fecha, "order" => "order", "orderDir" => "ASC"));
        $pos = 0;
        if (count($eventos)) {
            //Recorremos los eventos
            foreach ($eventos as $evento) {
                if ($evento->id != $ignoredEvento->id) {
                    $pos++;
                    //Si hemos indicado ir aquí...
                    if ($ignoredEvento->order == $pos) {
                        $pos++;
                    }
                    //Movemos el evento de posición
                    $evento->order = $pos;
                    $evento->update();
                }
            }
        }
    }

    public static function actualizarFechas($fecha)
    {
        if ($fecha) {
            //Actualizamos las fechas
            $previousEvent = null;
            $eventos = Evento::select(array("fecha" => $fecha, "order" => "order", "orderDir" => "ASC"));
            if (count($eventos)) {
                //Recorremos los eventos
                foreach ($eventos as $evento) {
                    //Calculamos las fechas
                    if ($previousEvent->id) {
                        //Inicio
                        $evento->fechaInicio = $previousEvent->fechaFin;
                        //Fin
                        $evento->calcFechaFin();
                    } else {
                        //Inicio
                        $evento->fechaInicio = $fecha." 07:00:00:00";
                        //Fin
                        $evento->calcFechaFin();
                    }
                    echo "Actualizando evento nº".$evento->id." (".$evento->order.") -> ".$evento->fechaInicio." | ".$evento->fechaFin."\n";
                    $evento->update();
                    $previousEvent = $evento;
                }
            }
        }
    }

    /**
     * Acciones previas a la modificación.
     * @return void
     */
    public function preUpdate()
    {
        $this->dateUpdate = date("Y-m-d H:i:s");
    }

    /**
     * Obtiene registros de la base de datos.
     * @param  array   $data       Condicionales / ordenación
     * @param  integer $limit      Límite de resultados (Paginación)
     * @param  integer $limitStart Inicio de la limitación (Paginación)
     * @param  int     $total      Total de filas encontradas (Paginación)
     * @return array   Modelos de la clase actual
     */
    public function select($data = array(), $limit = 0, $limitStart = 0, &$total = null)
    {
        $db = Registry::getDb();
        //Query
        $query = "SELECT * FROM `eventos` WHERE 1=1 ";
        $params = array();
        //Where
        if (isset($data["fecha"])) {
            $query .= " AND `fechaInicio` >= :fechaInicio AND `fechaFin` <= :fechaFin ";
            $params[":fechaInicio"] = $data["fecha"]." 00:00:00:00";
            $params[":fechaFin"] = $data["fecha"]." 23:59:59:24";
        }
        if ($data["orderNum"]) {
            $query .= " AND `order` = :orderNum ";
            $params[":orderNum"] = $data["orderNum"];
        }
        //Total
        $total = count($db->Query($query, $params));
        if ($total) {
            //Order
            if ($data['order'] && $data['orderDir']) {
                //Secure Field
                $orders = array("ASC", "DESC");
                if (@in_array($data['order'], array_keys(get_class_vars(__CLASS__))) && in_array($data['orderDir'], $orders)) {
                    $query .= " ORDER BY `".$data['order']."` ".$data['orderDir'];
                }
            } else {
                $query .= " ORDER BY `order` ASC ";
            }
            //Limit
            if ($limit) {
                $query .= " LIMIT ".(int) $limitStart.", ".(int) $limit;
            }
            $rows = $db->Query($query, $params);
            if (count($rows)) {
                foreach ($rows as $row) {
                    $results[] = new Evento($row);
                }

                return $results;
            }
        }
    }

    public function postDelete()
    {
        $fecha = date("Y-m-d", strtotime($this->fechaInicio));
        //Actualizamos el orden
        self::actualizarOrden($fecha);
        //Actualizamos las fechas
        self::actualizarFechas($fecha);
    }

    public function getFecha()
    {
        return current(explode(" ", $this->fechaInicio));
    }

    public function getHora()
    {
        return end(explode(" ", $this->fechaInicio));
    }

    public function getDataTablesJson()
    {
        $tipo = new Tipo($this->tipo);

        return array(
            "id" => $this->id,
            $this->order,
            $this->getFecha(),
            $this->getHora(),
            $this->duracion,
            $this->houseNumber,
            $tipo->codigo,
            $this->titulo,
            $this->tcIn,
            $this->logo,
            $this->segmento,
            "<button class='btn btn-xs newModal btn-success' data-order='".$this->order."'><span class='glyphicon glyphicon-plus'></span></a>".
            "<button class='btn btn-xs delete btn-danger'><span class='glyphicon glyphicon-remove'></span></a>",
            $tipo->color
        );
    }

    public function telson()
    {
        $entrada = new Entrada($this->entradaId);
        $tipo = new Tipo($this->tipo);
        $output =
            //utc_date
            str_replace("-", "", $this->getFecha()).
            //time
            $this->getHora().
            //start_type & fixed_time
            "TF".
            //duration
            substr($this->duracion, 1, 11).
            //update_ignore
            "X".
            //video_src
            "SRV3-2".
            //transition 1
            " ".
            //audio_rate 1
            " ".
            //effect_time_ofs 10
            "          ".
            //effect_duration 10
            "          ".
            //effect_transition 6
            "      ".
            //type_material 3
            str_pad($tipo->codigo, 3, " ", STR_PAD_LEFT).
            //alt_src 6
            "      ".
            //video_intime 11
            "           ".
            //video_item 16
            "                ".
            //video_dbase_title 16
            "                ".
            //comment1
            "                                ".
            //barker 1
            " ".
            //end_type 3
            "   ".
            //spool_number 16
            $this->houseNumber."  ".
            //dsk_src 6
            "      ".
            //dsk_num 16
            "                ".
            //dsk_in_time 10
            "          ".
            //dsk_duration 10
            "          ".
            //dsk2_src 6
            "      ".
            //dsk2_item 16
            "                ".
            //dsk2_time_ofs 10
            "          ".
            //dsk2_duration 10
            "          ".
            //effect_item 16
            "                ".
            //logo_src 6
            "      ".
            //logo 16
            "                ".
            //dsk3_src 6
            "      ".
            //dsk3_item 16
            "                ".
            //dsk3_time_ofs 10
            "          ".
            //dsk3_duration 10
            "          ".
            //effect_src 6
            "      ".
            //prot_src 8
            "        ".
            //prot_item 16
            "                ".
            //prot_som 11
            "           ".
            //prot_dur 10
            "          ".
            //video_src_ts 2
            "  ".
            //actual_duration 10
            "          "
        ;

        return $output;
    }
}
