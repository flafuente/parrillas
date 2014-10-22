<?php

/**
 * Log Class
 */
class Log extends Model
{
    /**
     * Id
     * @var int
     */
    public $id;

    /**
     * User Id
     * @var int
     */
    public $userId;

    /**
     * Action Id
     * @var int
     */
    public $actionId;

    /**
     * Class action
     * @var bool
     */
    public $classAction;

    /**
     * Data
     * @var string
     */
    public $data;

    /**
     * Insert date
     * @var string
     */
    public $dateInsert;

    /**
     * Actions
     * @var array
     */
    public $actions = array(
        LOG_LOGIN => "Login",
        LOG_LOGOUT => "Logout",

        LOG_LISTAR_ENTRADA => "Listar entradas",
        LOG_ADD_ENTRADA => "Añadir entrada",
        LOG_UPDATE_ENTRADA => "Guardar entrada",
        LOG_DELETE_ENTRADA => "Eliminar entrada",

        LOG_LISTAR_MOSCA => "Listar moscas",
        LOG_ADD_MOSCA => "Añadir mosca",
        LOG_UPDATE_MOSCA => "Guardar mosca",
        LOG_DELETE_MOSCA => "Eliminar mosca",

        LOG_LISTAR_PARRILLA => "Listar parrilla",
        LOG_ADD_EVENTO => "Añadir evento",
        LOG_DELETE_EVENTO => "Eliminar evento",
        LOG_MOVE_EVENTO => "Mover evento",
        LOG_EXPORT_PARRILLA => "Exportar parrilla",
        LOG_UPDATE_HOUR_PARRILLA => "Actualizar hora de la parrilla",
        LOG_UPDATE_EVENTO => "Update evento",

        LOG_UPDATE_ORDER_PARRILLA => "Mass update order parrilla",
        LOG_UPDATE_DATES_PARRILLA => "Mass update dates parrilla",

        LOG_IMPORT_EVENTOS => "Eventos importados",

        LOG_UPDATE_MOSCA2_PARRILLA => "Mosca2 añadida",
    );

    /**
     * Reserved vars (not at database table)
     * @var array
     */
    public static $reservedVarsChild = array("actions");

    /**
     * Class initialization
     *
     * @return void
     */
    public function init()
    {
        parent::$dbTable = "logs";
        parent::$reservedVarsChild = self::$reservedVarsChild;
    }

    static function add($actionId, $data = array(), $classAction = false)
    {
        $log = new Log();
        $log->actionId = $actionId;
        $log->data = (array) $data;
        $log->classAction = $classAction;

        //Clear useless data
        if (isset($log->data["reservedVarsChild"])) {
            foreach ($log->data["reservedVarsChild"] as $var) {
                unset($log->data["reservedVarsChild"][$var]);
            }
        }
        if (isset($log->data["reservedVars"])) {
            foreach ($log->data["reservedVars"] as $var) {
                unset($log->data["reservedVars"][$var]);
            }
        }

        return $log->insert();
    }

    /**
     * Pre-Insert actions
     *
     * Creation date
     *
     * @return void
     */
    public function preInsert()
    {
        $user = Registry::getUser();
        $this->userId = $user->id;

        $this->dateInsert = date("Y-m-d H:i:s");
        $this->data = json_encode($this->data);
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
        $query = "SELECT * FROM `logs` WHERE 1=1 ";
        $params = array();
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
                $query .= " ORDER BY `logs` ASC ";
            }
            //Limit
            if ($limit) {
                $query .= " LIMIT ".(int) $limitStart.", ".(int) $limit;
            }
            $rows = $db->Query($query, $params);
            if (count($rows)) {
                foreach ($rows as $row) {
                    $results[] = new Log($row);
                }

                return $results;
            }
        }
    }
}
