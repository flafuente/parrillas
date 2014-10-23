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
     * Logo2
     * @var string
     */
    public $logo2;
    /**
     * Delay
     * @var string
     */
    public $delay;
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

        //Order forced?
        if ($data["order"]) {
            $this->order = $data["order"];
        }

        //Default starting Hour
        $hora = $data["hora"] ? $data["hora"] : "07:00";

        //Tiene ED?
        if ($entrada->entradaIdEd && !$data["force"]) {
            $evento = new Evento();
            $evento->insert(array(
                "fecha" => $data["fecha"],
                "hora" => $data["hora"],
                "entradaId" => $entrada->entradaIdEd,
                "order" => $data["order"],
                "force" => $data["force"],
            ));
            //Avanzamos una posición
            if ($data["order"]) {
                $this->order++;
            }
        }

        //Movemos los posteriores
        if ($data["order"]) {
            $replaceEvent = self::getPreviousEvent($data["fecha"], $this->order);
            if ($replaceEvent->id) {
                self::moveElements($data["fecha"], $this->order, 1);
            }
        }

        //Datos entrada
        $this->tipo = $entrada->tipoId;
        $this->houseNumber = $entrada->houseNumber;
        $this->titulo = $entrada->nombre;
        $this->duracion = $entrada->duracion;
        $this->tcIn = $entrada->tcIn;
        $this->segmento = $entrada->segmento;
        $mosca = new Mosca($entrada->moscaId);
        $this->logo = $mosca->codigo;

        //Fechas / Orden
        //Si se ha mandado un orden, buscamos el evento anterior
        $order = $this->order ? ($this->order - 1) : null;
        $previousEvent = self::getPreviousEvent($data["fecha"], $order);
        //Existe un evento anterior?
        if ($previousEvent->id) {
            //Si no hemos mandado orden, le asignamos el siguiente al evento anterior
            if (!$this->order) {
                $this->order = $previousEvent->order + 1;
            }
            //Inicio
            $this->fechaInicio = $previousEvent->fechaFin;
            //Fin
            $this->calcFechaFin();
            //Destruct hora
            $hora = null;
        } else {
            //Orden
            $this->order = 1;
            //Inicio
            $this->fechaInicio = $data["fecha"]." ".$hora.":00:00";
            //Fin
            $this->calcFechaFin();
        }

        if ($data["order"]) {
            self::actualizarFechas($data["fecha"], $hora);
        };

    }

    public function updateEntrada($entrada)
    {
        if ($entrada->id) {
            $this->tipo = $entrada->tipoId;
            $this->houseNumber = $entrada->houseNumber;
            $this->titulo = $entrada->nombre;
            $this->duracion = $entrada->duracion;
            $this->tcIn = $entrada->tcIn;
            $this->segmento = $entrada->segmento;
            $this->update();
        }
    }

    public function updateMosca($mosca)
    {
        if ($mosca->id) {
            $this->logo = $mosca->codigo;
            $this->update();
        }
    }

    public function postInsert($data = array())
    {
        //Entrada
        $entrada = new Entrada($this->entradaId);

        //Tiene FIN?
        if ($entrada->entradaIdFin && !$data["force"]) {
            $evento = new Evento();
            $order = $this->order + 1;
            $evento->insert(array("fecha" => $data["fecha"], "entradaId" => $entrada->entradaIdFin, "order" => $order, "force" => $data["force"]));
        }

        //Log
        Log::add(LOG_ADD_EVENTO, $this, true);
    }

    private function calcFechaFin()
    {
        $this->fechaFin = dateAddTime($this->fechaInicio, $this->duracion);
    }

    private static function getPreviousEvent($fecha, $order = null)
    {
        $previousEvent = Evento::select(array("fecha" => $fecha, "orderNum" => $order, "order" => "order", "orderDir" => "DESC"), 1);

        return $previousEvent[0];
    }

    private static function moveElements($fecha, $order, $movements)
    {
        //Actualizamos el orden
        $eventos = Evento::select(array("fecha" => $fecha, "minOrderNum" => $order, "order" => "order", "orderDir" => "ASC"));
        if (count($eventos)) {
            //Recorremos los eventos
            foreach ($eventos as $evento) {
                $pos = $evento->order + $movements;
                //echo "Actualizando evento nº".$evento->id." (".$evento->order.") a la pos ".$pos."\n";
                //Movemos el evento de posición
                $evento->order = $pos;
                $evento->update();
            }
        }
    }

    public function order($fecha, $toPosition, $hora = null)
    {
        //Actualizamos la posición del evento
        $this->order = $toPosition;
        $this->update();

        //Actualizamos el orden
        self::actualizarOrden($fecha, $this->id);

        //Actualizamos las fechas
        self::actualizarFechas($fecha, $hora);
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
                    if ($evento->order != $pos) {
                        $evento->order = $pos;
                        $evento->update();
                    }
                }
            }
            //Log
            Log::add(LOG_UPDATE_ORDER_PARRILLA, null, true);
        }
    }

    public static function actualizarFechas($fecha, $hora = null)
    {
        if ($fecha) {

            //Default starting Hour
            if ($hora) {
                if (count(explode(":", $hora)) == 2) {
                    $hora .= ":00:00";
                }
            }

            //Actualizamos las fechas
            $previousEvent = null;
            $eventos = Evento::select(array("fecha" => $fecha, "order" => "order", "orderDir" => "ASC"));
            if (count($eventos)) {
                //Recorremos los eventos
                foreach ($eventos as $evento) {
                    $updateElement = false;
                    //Calculamos las fechas
                    if ($previousEvent->id) {
                        //Inicio
                        if ($evento->fechaInicio != $previousEvent->fechaFin) {
                            $evento->fechaInicio = $previousEvent->fechaFin;
                            $updateElement = true;
                        }
                        //Fin
                        $oldFechaFin = $evento->fechaFin;
                        $evento->calcFechaFin();
                        if ($oldFechaFin != $evento->fechaFin) {
                            $updateElement = true;
                        }
                    } else {
                        //Inicio
                        $fechaInicio = $hora ? $fecha." ".$hora : $evento->fechaInicio;
                        if ($evento->fechaInicio != $fechaInicio) {
                            $evento->fechaInicio = $fechaInicio;
                            $updateElement = true;
                        }
                        //Fin
                        $oldFechaFin = $evento->fechaFin;
                        $evento->calcFechaFin();
                        if ($oldFechaFin != $evento->fechaFin) {
                            $updateElement = true;
                        }
                    }
                    /*echo "Actualizando evento ".$evento->id."<br>";
                    echo " - Order: ".$evento->order."<br>";
                    echo " - Duracion: ".$evento->duracion."<br>";
                    echo " - Inicio: ".$evento->fechaInicio."<br>";
                    echo " - Fin: ".$evento->fechaFin."<br><br>";*/
                    if ($updateElement) {
                        $evento->update();
                    }
                    $previousEvent = $evento;
                }

                //Log
                Log::add(LOG_UPDATE_DATES_PARRILLA, null, true);
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

    public function postUpdate()
    {
        //Log
        Log::add(LOG_UPDATE_EVENTO, $this, true);
    }

    public static function getByMoscaId($moscaId)
    {
        if ($moscaId) {
            $db = Registry::getDb();
            //Query
            $query = "SELECT * FROM `eventos` WHERE `entradaId` IN (SELECT `id` FROM `entradas` WHERE `moscaId` IN (SELECT `id` FROM `moscas` WHERE `id` = :moscaId))";
            $params = array(":moscaId" => $moscaId);
            $rows = $db->query($query, $params);
            if (count($rows)) {
                foreach ($rows as $row) {
                    $return[] = new Evento($row);
                }

                return $return;
            }
        }
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
            $params[":fechaInicio"] = $data["fecha"]." 03:00:00:00";
            $params[":fechaFin"] = date("Y-m-d 02:59:59:24", strtotime($data["fecha"]." +1 day"));
        }
        if ($data["orderNum"]) {
            $query .= " AND `order` = :orderNum ";
            $params[":orderNum"] = $data["orderNum"];
        }
        if ($data["minOrderNum"]) {
            $query .= " AND `order` >= :minOrderNum ";
            $params[":minOrderNum"] = $data["minOrderNum"];
        }
        //Tipo
        if ($data["tipo"]) {
            $query .= "AND `tipo` IN (SELECT `id` FROM `tipos` WHERE `codigo` = :tipo) ";
            $params[":tipo"] = $data["tipo"];
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

    public function getFecha()
    {
        return current(explode(" ", $this->fechaInicio));
    }

    public function getHora()
    {
        return end(explode(" ", $this->fechaInicio));
    }

    public function postDelete()
    {
        //Log
        Log::add(LOG_DELETE_EVENTO, $this, true);
    }

    public function getDataTablesJson()
    {
        $tipo = new Tipo($this->tipo);

        return array(
            "id" => $this->id,
            $this->order,
            "<button class='btn btn-xs mosca2Modal btn-success' data-id='".$this->id."'><span class='glyphicon glyphicon-plus'></span></button>",
            $this->getFecha(),
            $this->getHora(),
            $this->duracion,
            $this->houseNumber,
            $tipo->codigo,
            $this->titulo,
            $this->tcIn,
            $this->logo,
            $this->logo2,
            $this->delay,
            ($this->segmento) ? '<span class="glyphicon glyphicon-ok"></span>' : '',
            "<button class='btn btn-xs newModal btn-success' data-order='".$this->order."'><span class='glyphicon glyphicon-plus'></span></button>".
            "<button class='btn btn-xs importModal btn-primary' data-order='".$this->order."'><span class='glyphicon glyphicon-import'></span></button>".
            "<button class='btn btn-xs delete btn-danger'><span class='glyphicon glyphicon-remove'></span></button>",
            $tipo->color
        );
    }

    public function telson($firstLine = false)
    {
        $entrada = new Entrada($this->entradaId);
        $mosca = new Mosca($entrada->moscaId);
        $mosca2 = Mosca::getBy('codigo', $entrada->logo2);
        $tipo = new Tipo($this->tipo);

        if ($firstLine) {
            $tf = "TF";
        } else {
            $tf = "  ";
        }
        $segmento=" ";
        if ($entrada->segmento>0) {
            $segmento="X";
        }
        if ($tipo->codigo == "P") {
            $type_material = "S";
        } elseif ($tipo->codigo == "C") {
            $type_material = "C";
        } else {
            $type_material = "I";
        }

        $output =
            //utc_date
            str_replace("-", "", $this->getFecha()).
            //time
            $this->getHora().
            //start_type & fixed_time
            $tf.
            //duration
            substr($this->duracion, 1, 11).
            //update_ignore
            $segmento.
            //video_src
            "CP2-10".
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
            str_pad($type_material, 3, " ", STR_PAD_RIGHT).
            //alt_src 6
            "      ".
            //video_intime 11
            str_pad($entrada->tcIn, 11, " ", STR_PAD_RIGHT).
            //video_item 16
            str_pad($this->houseNumber, 16, " ", STR_PAD_RIGHT).
            //video_dbase_title 16
            str_pad(substr(utf8_decode($this->titulo), 0 , 16), 16, " ", STR_PAD_RIGHT).
            //comment1
            str_pad(substr(utf8_decode($this->titulo), 0 , 32), 32, " ", STR_PAD_RIGHT).
            //barker 1
            " ".
            //end_type 3
            "   ".
            //spool_number 16
            "                ".
            //dsk_src 6
            str_pad($mosca2->identificador, 6, " ", STR_PAD_RIGHT).
            //dsk_num 16
            str_pad($mosca2->codigo, 16, " ", STR_PAD_RIGHT).
            //dsk_in_time 10
            str_pad(substr($this->delay, 1), 10, " ", STR_PAD_RIGHT).
            //dsk_duration 10
            str_pad(substr($mosca2->duracion, 1), 10, " ", STR_PAD_RIGHT).
            //dsk2_src 6
            "      ".
            //dsk2_item 16
            "                ".
            //dsk2_time_ofs 10
            "          ".
            //dsk2_duration 10
            "          ".
            //vid_res 1
            " ".
            //aspect_ratio 1
            " ".
            //caption_mode 1
            " ".
            //audio_mode 1
            " ".
            //OFFSET 38
            "                                      ".
            //effect_item 16
            "                ".
            //OFFSET 26
            "                          ".
            //logo_src 6
            str_pad($mosca->identificador, 6, " ", STR_PAD_RIGHT).
            //logo 16
            str_pad($mosca->codigo, 16, " ", STR_PAD_RIGHT).
            //OFFSET 10
            "           ".
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
            //OFFSET 43
            "                                           ".
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
