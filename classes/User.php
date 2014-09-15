<?php

/**
 * User Class
 *
 * @package LightFramework\Core
 */
class User extends Model
{
    /**
     * Id
     * @var int
     */
    public $id;

    /**
     * Status Id
     * @var int
     */
    public $statusId;

    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Username
     * @var string
     */
    public $username;

    /**
     * Password
     * @var string
     */
    public $password;

    /**
     * Cookie Token
     * @var string
     */
    public $token;

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
     * Last visit date
     * @var string
     */
    public $lastvisitDate;

    /**
     * Status CSS classes
     * @var array
     */
    public $statusesCss = array(
        0 => "danger",
        1 => "success",
    );

    /**
     * Status types
     * @var array
     */
    public $statuses = array(
        0 => "Deshabilitado",
        1 => "Habilitado",
    );

    /**
     * Reserved vars (not at database table)
     *
     * @var array
     */
    public static $reservedVarsChild = array("statuses", "statusesCss");

    /**
     * Class initialization
     *
     * @return void
     */
    public function init()
    {
        parent::$dbTable = "users";
        parent::$reservedVarsChild = self::$reservedVarsChild;
    }

    /**
     * Get the user status
     *
     * @return string User status
     */
    public function getStatusString()
    {
        return $this->statuses[$this->statusId];
    }

    /**
     * Get the CSS class for user status
     *
     * @return string CSS Class
     */
    public function getStatusCssString()
    {
        return $this->statusesCss[$this->statusId];
    }

    /**
     * Insert and Update validation
     * In this case, its the same for both methods
     *
     * @return bool
     */
    private function validate()
    {
        //Check username
        if (!$this->username) {
            Registry::addMessage("Debes introducir tu nombre de usuario", "error", "username");
        } elseif (User::getBy("username", $this->username, $this->id)) {
            Registry::addMessage("Este nombre de usuario ya esta registrado", "error", "username");
        }
        //Check email
        if (!$this->email) {
            Registry::addMessage("Debes introducir tu email", "error", "email");
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
             Registry::addMessage("Email incorrecto", "error", "email");
        } elseif (User::getBy("email", $this->email, $this->id)) {
            Registry::addMessage("Este email ya esta registrado", "error", "email");
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
     * Default language if none was introduced
     * Password encryption
     * Creation date
     * Force role to non-admin
     *
     * @return void
     */
    public function preInsert()
    {
        //Password encryption
        $this->password = User::encrypt($this->password);
        //Creation Date
        $this->dateInsert = date("Y-m-d H:i:s");
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
     * Password encryption
     * Update date
     *
     * @return void
     */
    public function preUpdate($data=array())
    {
        //Prevent blank password override
        if ($data['password']) {
            //Password encryption
            $this->password = User::encrypt($data['password']);
        } else {
            //Empty password to keep the current one
            $this->password = null;
        }
        //Update Date
        $this->dateUpdate = date("Y-m-d H:i:s");
    }

    /**
     * Login
     *
     * @param string $login      Username or email
     * @param string $password   Plain password
     * @param int    $expiration Expiration in Seconds
     *
     * @return bool
     */
    public static function login($login, $password, $expiration=7200)
    {
        $db = Registry::getDb();
        $rows = $db->query("SELECT * FROM `users` WHERE (`username` = :username OR `email` = :email) AND `statusId` = 1 AND `password` = :password",
            array(
                ":email" => $login,
                ":username" => $login,
                ":password" => User::encrypt($password)
            )
        );
        if ($rows) {
            $user = new User($rows[0]);
            //Set Cookie
            $user->auth($expiration);
            //Update lastVisitDate
            $user->lastvisitDate = date("Y-m-d H:i:s");
            $user->update();

            return $user;
        }
    }

    public function auth($expiration = 7200)
    {
        $this->setToken();
        $config = Registry::getConfig();
        setcookie($config->get("cookie"), $this->token, time() + $expiration, "/");
    }

    /**
     * Set user token
     */
    private function setToken()
    {
        if (!$this->token) {
            if (is_callable('openssl_random_pseudo_bytes')) {
                $this->token = bin2hex(openssl_random_pseudo_bytes(16));
            } else {
                $this->token = md5(uniqid('', true));
            }
        }

        return $this->token;
    }

    /**
     * Logout
     *
     * @return bool
     */
    public static function logout()
    {
        $config = Registry::getConfig();
        //Destroy Cookies
        unset($_COOKIE[$config->get("cookie")]);
        setcookie($config->get("cookie"), null, -1, "/");

        return true;
    }

    /**
     * Password encryption
     *
     * @param string $password Plain password
     *
     * @return string Encrypted password
     */
    public static function encrypt($password="")
    {
        return md5(sha1(trim($password)));
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
        $query = "SELECT * FROM `users` ";
        $params = array();
        //Where
        $where = " WHERE 1=1 ";
        //Search
        if ($data["search"]) {
            $where .= "AND (`username` LIKE :username OR `email` LIKE :email )";
            $params[":username"] = "%".$data["search"]."%";
            $params[":email"] = "%".$data["search"]."%";
        }
        //Status
        if (isset($data["statusId"]) && $data["statusId"]!="-1") {
            $where .= " AND `statusId` = :statusId ";
            $params[":statusId"] = $data["statusId"];
        }
        $query .= $where;
        //Total
        $totalQuery = "SELECT * FROM `users` ".$where;
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
                    $results[] = new User($row);
                }

                return $results;
            }
        }
    }
}
