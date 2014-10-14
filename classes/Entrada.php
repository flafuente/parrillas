<?php

/**
 * Modelo Entrada
 *
 * @package Tribo\Modelos
 */
class Entrada extends Model
{
    /**
     * Id
     * @var int
     */
    public $id;

    /**
     * Nombre
     * @var string
     */
    public $nombre;

    /**
     * TipoId
     * @var int
     */
    public $tipoId;

    /**
     * Programa Id
     * @var integer
     */
    public $programaId;

    /**
     * Programa
     * @var string
     */
    public $programa;

    /**
     * Capitulo
     * @var string
     */
    public $capitulo;

    /**
     * Titulo del capitulo
     * @var string
     */
    public $titulo;

    /**
     * House number
     * @example TB-PG-XXXXXXXX
     * @var string
     */
    public $houseNumber;

    /**
     * Segmento
     * @var bool
     */
    public $segmento;

    /**
     * Id de la mosca
     * @var int
     */
    public $moscaId;

    /**
     * Id de la mosca 2
     * @var int
     */
    public $moscaId2;

    /**
     * TC IN
     * @example HH:MM:SS:FR
     * @var string
     */
    public $tcIn;

    /**
     * TC OUT
     * @example HH:MM:SS:FR
     * @var string
     */
    public $tcOut;

    /**
     * Duración
     * @example HH:MM:SS:FR
     * @var string
     */
    public $duracion;

    /**
     * Entrada ED
     * @var int
     */
    public $entradaIdEd;

    /**
     * Entrada FIN
     * @var int
     */
    public $entradaIdFin;

    /**
     * Insert date
     * @var string
     */
    public $dateInsert;

    /**
     * Update date
     * @var string
     */
    public $dateUpdate;

    /**
     * Class initialization
     *
     * @return void
     */
    public function init()
    {
        parent::$dbTable = "entradas";
        parent::$reservedVarsChild = self::$reservedVarsChild;
    }

    /**
     * Insert and Update validation
     * In this case, its the same for both methods
     *
     * @return bool
     */
    private function validate()
    {
        //Check nombre
        if (!$this->nombre) {
            Registry::addMessage("Debes introducir un nombre", "error", "nombre");
        } elseif (Self::getBy("nombre", $this->nombre, $this->id)) {
            Registry::addMessage("Este nombre ya está siendo utilizado", "error", "nombre");
        }
        //Check houseNumber
        Self::validateHouseNumber($this->houseNumber, $this->tipoId, $this->id);

        //TC IN/OUT
        Self::validateTc($this->tcIn, "tcIn");
        Self::validateTc($this->tcOut, "tcOut");

        //Return messages avoiding deletion
        return Registry::getMessages(true);
    }

    private static function validateTc($tc, $field)
    {
        $parts = explode(":", $tc);
        if (count($parts) != 4) {
            Registry::addMessage("El formato de TC es XX:XX:XX:XX", "error", $field);
        }

        //Return messages avoiding deletion
        return Registry::getMessages(true);
    }

    /**
     * Insert validation
     *
     * @return array Object Messages
     */
    public function validateInsert()
    {
        //Validation
        return $this->validate();
    }

    /**
     * Pre-Insert actions
     *
     * @return void
     */
    public function preInsert()
    {
        //Creation Date
        $this->dateInsert = date("Y-m-d H:i:s");
        //Clear ED & FIN
        $this->clearEdFin();
        //Calc duración
        $this->calcDuracion();
    }

    public function postInsert()
    {
        //Tribo Sync
        $this->syncTribo();

        //Log
        Log::add(LOG_ADD_ENTRADA, $this, true);
    }

    /**
     * Update validation
     *
     * @return array Object Messages
     */
    public function validateUpdate()
    {
        //Validation
        return $this->validate();
    }

    /**
     * Pre-Update actions
     *
     * @return void
     */
    public function preUpdate()
    {
        //Update Date
        $this->dateUpdate = date("Y-m-d H:i:s");
        //Clear ED & FIN
        $this->clearEdFin();
        //Calc duración
        $this->calcDuracion();
    }

    public function postUpdate()
    {
        //Update Eventos
        $eventos = Evento::getBy("entradaId", $this->id);
        if (count($eventos)) {
            foreach ($eventos as $evento) {
                $evento->updateEntrada($this);
            }

            //Actualizamos las fechas
            Evento::actualizarFechas($evento->getFecha());
        }

        //Tribo Sync
        $this->syncTribo();

        //Log
        Log::add(LOG_UPDATE_ENTRADA, $this, true);
    }

    private function clearEdFin()
    {
        $tipo = new Tipo($this->tipoId);
        if (strtoupper($tipo->codigo) != "P") {
            $this->entradaIdEd = 0;
            $this->entradaIdFin = 0;
        }
    }

    private function calcDuracion()
    {
        $this->duracion = timeDiff($this->tcIn, timeDiff($this->tcOut, "00:00:00:01", "+"));

        return $this->duracion;
    }

    public static function validateHouseNumber($houseNumber, $tipoId, $ignoreId = null)
    {
        $tipo = new Tipo($tipoId);
        if (!$tipo->id) {
            Registry::addMessage("Debes seleccionar un tipo primero", "warning", "houseNumber");
        } else {
            // Siempre 14 Caracteres
            if (strlen($houseNumber) != 14) {
                Registry::addMessage("La numeración debe tener 14 caracteres", "error", "houseNumber");
            } else {
                /// No puede haber 2 iguales
                if (Self::getBy("houseNumber", $houseNumber, $ignoreId)) {
                    Registry::addMessage("Ya existe otra entrada con esta numeración", "error", "houseNumber");
                } else {
                    // Máscara
                    if (!$tipo->checkMascara($houseNumber)) {
                        Registry::addMessage("La numeración no coincide con su tipo: ".$tipo->mascara, "error", "houseNumber");
                    }
                }
            }
        }

        //Return messages avoiding deletion
        return !Registry::getMessages(true);
    }

    private function syncTribo()
    {
        $data = array(
            "programaId" => $this->programaId,
            "capitulo" => $this->capitulo,
            "titulo" => $this->titulo,
            "id" => $this->id,
        );
        Api::request("programas/syncParrillas", $data);
    }

    /**
     * Object selection
     *
     * @param array   $data       Conditionals and Order values
     * @param integer $limit      Limit
     * @param integer $limitStart Limit start
     * @param int     $total      Total rows found
     *
     * @return array Objects found
     */
    public static function select($data = array(), $limit = 0, $limitStart = 0, &$total = null)
    {
        $db = Registry::getDb();
        //Query
        $query = "SELECT * FROM `entradas` ";
        $params = array();
        //Where
        $where = " WHERE 1=1 ";
        //Search
        if ($data["search"]) {
            $where .= "AND (`nombre` LIKE :nombre OR `houseNumber` LIKE :houseNumber) ";
            $params[":houseNumber"] = "%".$data["search"]."%";
            $params[":nombre"] = "%".$data["search"]."%";
        }
        //Tipo
        if ($data["tipo"]) {
            $where .= "AND `tipoId` IN (SELECT `id` FROM `tipos` WHERE `codigo` = :tipo) ";
            $params[":tipo"] = $data["tipo"];
        }
        $query .= $where;
        //Total
        $totalQuery = "SELECT * FROM `entradas` ".$where;
        $total = count($db->Query($totalQuery, $params));
        if ($total) {
            //Order
            if ($data['order'] && $data['orderDir']) {
                //Secure Field
                $orders = array("ASC", "DESC");
                if (@in_array($data['order'], array_keys(get_class_vars(__CLASS__))) && in_array($data['orderDir'], $orders)) {
                    $query .= " ORDER BY `".$data['order']."` ".$data['orderDir'];
                }
            }
            //Limit
            if ($limit) {
                $query .= " LIMIT ".(int) $limitStart.", ".(int) $limit;
            }
            $rows = $db->Query($query, $params);
            if (count($rows)) {
                $results = array();
                foreach ($rows as $row) {
                    $results[] = new Entrada($row);
                }

                return $results;
            }
        }
    }

    public function postDelete()
    {
        //Log
        Log::add(LOG_DELETE_ENTRADA, $this, true);
    }

    public function getJSon()
    {
        $tipo = new Tipo($this->tipoId);
        //$mosca = new Mosca($this->moscaId);

        $object = new stdClass();
        $object->id = $this->id;
        $object->nombre = $this->nombre;
        $object->houseNumber = $this->houseNumber;
        $object->tcIn = $this->tcIn;
        $object->tipo = $tipo->nombre;
        $object->duracion = $this->duracion;
        $object->segmento = $this->segmento;

        return $object;
    }
}
