<?php

/**
 * Modelo Tipo de entrada
 *
 * @package Tribo\Modelos
 */
class Tipo extends Model
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
     * Código
     * @var string
     */
    public $codigo;

    /**
     * Máscara
     * @var string
     */
    public $mascara;

    /**
     * Color
     * @var string
     */
    public $color;

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
        parent::$dbTable = "tipos";
        parent::$reservedVarsChild = self::$reservedVarsChild;
    }

    public function checkMascara($houseNumber)
    {
        if ($this->mascara) {
            for ($i = 0; $i <= strlen($this->mascara); $i++) {
                if ($houseNumber[$i] != $this->mascara[$i] && $this->mascara[$i] != "X") {
                    return false;
                }
            }
        }

        return true;
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
        $this->mascara = strtoupper($this->mascara);
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
        $this->mascara = strtoupper($this->mascara);
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
        $query = "SELECT * FROM `tipos` ";
        $params = array();
        //Where
        $where = " WHERE 1=1 ";
        $query .= $where;
        //Total
        $totalQuery = "SELECT * FROM `tipos` ".$where;
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
                    $results[] = new Tipo($row);
                }

                return $results;
            }
        }
    }
}
