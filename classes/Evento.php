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
     * Validación para creación/edición del capítulo.
     * @return array Array de errores
     */
    private function validate()
    {
        return Registry::getMessages(true);
    }

    /**
     * Validación de creación.
     * @return array Errores
     */
    public function validateInsert()
    {
        return $this->validate();
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
        $this->fillVars($data["fecha"]);

        //Entrada
        $entrada = new Entrada($this->entradaId);
        $this->duracion = $entrada->duracion;
        $this->tipo = $entrada->tipoId;
        $this->houseNumber = $entrada->houseNumber;
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
            $this->orden = 1;
            //Inicio
            $this->fechaInicio = $data["fecha"]." 00:00:00";
            //Fin
            $this->calcFechaFin();
        }

    }

    private function calcFechaFin()
    {
        $seconds = strtotime("1970-01-01 ".$this->duracion." UTC");
        $this->fechaFin = date("Y-m-d H:i:s", strtotime($this->fechaInicio) + $seconds);
    }

    private static function getPreviousEvent($fecha, $order = null)
    {
        $previousEvent = Evento::select(array("fecha" => $fecha, "orderNum" => $order, "order" => "order", "orderDir" => "DESC"), 1);

        return $previousEvent;
    }

    public function order($fecha, $toPosition)
    {
        //Actualizamos la posición del evento
        $this->order = $toPosition;
        $this->update();

        //Actualizamos el orden
        $this->actualizarOrden($fecha, $this->id);
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

        //Actualizamos las fechas
        $this->actualizarFechas($fecha);
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
                        $evento->fechaInicio = $fecha." 00:00:00";
                        //Fin
                        $evento->calcFechaFin();
                    }
                    $evento->update();
                    $previousEvent = $evento;
                }
            }
        }
    }

    /**
     * Validación de modificación.
     * @return array Errores
     */
    public function validateUpdate()
    {
        return $this->validate();
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
            $params[":fechaInicio"] = $data["fecha"]." 00:00:00";
            $params[":fechaFin"] = $data["fecha"]." 23:59:59";
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
        $this->actualizarOrden(date("Y-m-d", strtotime($this->fechaInicio)));
    }

    public function getFecha()
    {
        return date("d/m/Y", strtotime($this->fechaInicio));
    }

    public function getHora()
    {
        return date("H:i:s", strtotime($this->fechaInicio));
    }

    public function getDataTablesJson()
    {
        return array(
            "id" => $this->id,
            $this->order,
            $this->getFecha(),
            $this->getHora(),
            $this->duracion,
            $this->houseNumber,
            $this->tipo,
            $this->titulo,
            $this->tcIn,
            $this->logo,
            $this->segmento,
            "<button class='btn btn-danger delete'><span class='glyphicon glyphicon-remove'></span></a>",
        );
    }
}
