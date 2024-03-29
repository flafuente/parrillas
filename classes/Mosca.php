<?php

/**
 * Modelo Mosca
 *
 * @package Tribo\Modelos
 */
class Mosca extends Model
{
    /**
     * Id
     * @var int
     */
    public $id;

    /**
     * Tipo Id
     * @var int
     */
    public $tipoId;

    /**
     * Nombre
     * @var string
     */
    public $nombre;

    /**
     * Código
     * @var string
     */
    public $codigo;

    /**
     * Duración
     * @var string
     */
    public $duracion;

    /**
     * Identificador
     * @var string
     */
    public $identificador;

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

    public $tipos = array(
        1 => "Tipo 1",
        2 => "Tipo 2",
    );

    /**
     * Reserved vars (not at database table)
     *
     * @var array
     */
    public static $reservedVarsChild = array("tipos");

    /**
     * Class initialization
     *
     * @return void
     */
    public function init()
    {
        parent::$dbTable = "moscas";
        parent::$reservedVarsChild = self::$reservedVarsChild;
    }

    public function getTipoString()
    {
        return $this->tipos[$this->tipoId];
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
            Registry::addMessage("Ya existe otro tipo de entrada con este nombre", "error", "nombre");
        }
        //Check codigo
        if (!$this->codigo) {
            Registry::addMessage("Debes introducir un codigo", "error", "codigo");
        } elseif (Self::getBy("codigo", $this->codigo, $this->id)) {
            Registry::addMessage("Ya existe otro tipo de entrada con este codigo", "error", "codigo");
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
    }

    public function postInsert()
    {
        //Log
        Log::add(LOG_ADD_MOSCA, $this, true);
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
    }

    public function postUpdate()
    {
        //Update Eventos
        $eventos = Evento::getByMoscaId($this->id);
        if (count($eventos)) {
            foreach ($eventos as $evento) {
                $evento->updateMosca($this);
            }
        }

        //Log
        Log::add(LOG_UPDATE_MOSCA, $this, true);
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
    public static function select($data=array(), $limit=0, $limitStart=0, &$total=null)
    {
        $db = Registry::getDb();

        //Query
        $query = "SELECT * FROM `moscas` ";
        $params = array();

        //Where
        $where = " WHERE 1=1 ";

        //TipoId
        if ($data["tipoId"]) {
            $where .= " AND tipoId = :tipoId ";
            $params[":tipoId"] = $data["tipoId"];
        }

        $query .= $where;

        //Total
        $totalQuery = "SELECT * FROM `moscas` ".$where;
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

            //Query
            $rows = $db->Query($query, $params);
            if (count($rows)) {
                $results = array();
                foreach ($rows as $row) {
                    $results[] = new Mosca($row);
                }

                return $results;
            }
        }
    }

    public function postDelete()
    {
        //Log
        Log::add(LOG_DELETE_MOSCA, $this, true);
    }
}
