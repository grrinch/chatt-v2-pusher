<?php
namespace Chatt;

class Auth {

    static protected $instance = null;
    protected $sess = null;

    public static function getInstance() {
        if (isset(self::$instance))
            return self::$instance;
        else {
            self::$instance = new Auth();
            return self::$instance;
        }
    }

    public function __construct() {
        $this->sess = Session::getInstance();
    }

    public function hasIdentity() {
        if ($this->sess->hasSession())
            return true;
        else {
            Session::refresh();
            return false;
        }
    }
    
    /**
     * Zaślepka funkcji tworzenia sesji admina
     * @param string $login
     * @param string $pass
     * @return boolean
     */
    public function createAdmin($login = null, $pass = null) {
        return $this->sess->createAdmin($login);
    }
    
    /**
     * Zaślepka do pobierania loginu admina z sesji
     * @return string
     */
    public function adminGetLogin() {
        return $this->sess->adminGetLogin();
    }
    
    /**
     * Zaślepka funkcji sprawdzania zalogowania admina
     * @return boolean
     */
    public function adminLogged() {
        return $this->sess->adminLogged();
    }
    
    /**
     * Zaślepka funkcji wylogowania sesji admina
     * @return boolean
     */
    public function adminLogout() {
        return $this->sess->adminLogout();
    }

}
