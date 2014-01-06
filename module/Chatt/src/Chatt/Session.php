<?php

namespace Chatt;

class Session {

    protected static $instance = null;
    
    protected $_dane = array(
        'login' => null,
        'hash' => null,
        'room' => null,
        'beats' => 0);
    
    protected $final = false;
    
    private $_admin = array(
        'login' => null,
        'auth' => false
    );

    public static function getInstance() {
        if (isset(self::$instance))
            return self::$instance;
        else {
            self::$instance = new Session();
            return self::$instance;
        }
    }

    private function __construct() {
        session_start();
    }

    public function create(Array $data) {
        foreach ($data as $var => $value) {
            switch ($var) {
                case 'login': $this->_dane['login'] = $value;
                    break;
                case 'hash': $this->_dane['hash'] = $value;
                    break;
                case 'room': $this->_dane['room'] = (int) $value;
                    break;
            }
        }

        if ($this->isAllData())
            $this->final = true;
        return $this->save();
    }
    
    /**
     * Tworzy sesję admina
     * @param type $login
     * @return type
     */
    public function createAdmin($login = null) {
        $this->_admin['login'] = $login;
        $this->_admin['auth'] = $this->_admin['login'] == null ? false : true;
        $this->adminMake();
        return $this->_admin['auth'];
    }
    
    /**
     * Sprawdza czy admin jest już zalogowany
     * @return boolean
     */
    public function adminLogged() {
        $this->adminMake(true);
        return $this->_admin['auth'];
    }
    
    /**
     * zwraca login zalogowanego admina lub false
     * @return string
     */
    public function adminGetLogin() {
        if($this->adminLogged()) 
            return $this->_admin['login'];
        return null;
    }
    
    /**
     * Zrzuca dane z sesji do właściwości obiektu lub odwrotnie
     * @param boolean $dir
     */
    protected function adminMake($from_session = false) {
        if($from_session) {
            $this->_admin['login'] = $_SESSION['admin_login'];
            $this->_admin['auth'] = $_SESSION['admin_auth'];
        }
        else {
            $_SESSION['admin_login'] = $this->_admin['login'];
            $_SESSION['admin_auth'] = $this->_admin['auth'];
        }
    }
    
    /**
     * Usuwa dane sesji admina
     * @return true
     */
    public function adminLogout() {
        $this->_admin['auth'] = false;
        $this->_admin['login'] = null;
        $this->adminMake();
        self::refresh();
        return true;
    }

    protected function isAllData() {
        return (isset($this->_dane['login']) && isset($this->_dane['hash']) && isset($this->_dane['room']));
    }

    public function checkAllData() {
        return $this->isAllData();
    }

    private function loadData() {
        $dane = array(
            'login' => $_SESSION['login'],
            'hash' => $_SESSION['hash'],
            'room' => $_SESSION['room'],
            'beats' => $_SESSION['beats']);
        $this->_dane = $dane;
    }

    public function hasSession() {
        return (isset($_SESSION['login']) && isset($_SESSION['hash']) && isset($_SESSION['room']));
    }

    public function getLogin() {
        return $this->_dane['login'] ? $this->_dane['login'] : $_SESSION['login'];
    }

    public function getRoom() {
        return $this->_dane['room'] ? $this->_dane['room'] : $_SESSION['room'];
    }

    public function getHash() {
        return $this->_dane['hash'] ? $this->_dane['hash'] : $_SESSION['hash'];
    }

    public function getBeats() {
        if(isset($_SESSION['beats'])) $beats = $_SESSION['beats'];
        else {
            $beats = time();
            $_SESSION['beats'] = $beats;
        }
        return $beats;
    }

    public function getLastMsgId() {
        return isset($_SESSION['last_msg']) ? $_SESSION['last_msg'] : 0;
    }

    protected function save() {
        if ($this->isAllData()) {
            $_SESSION['login'] = $this->getLogin();
            $_SESSION['room'] = $this->getRoom();
            $_SESSION['hash'] = $this->getHash();
            $_SESSION['beats'] = $this->getBeats();
            return true;
        }
        else
            return false;
    }

    public function beat() {
        $_SESSION['beats'] = time();
    }

    public function resetBeats() {
        $_SESSION['beats'] = time();
    }

    public function setLastTalkId($id) {
        $_SESSION['last_msg'] = $id;
    }

    public function getSession() {
        if ($this->hasSession()) {
            $this->loadData();
            return $this->_dane;
        }
        else
            return false;
    }

    protected function deleteData() {
        $dane = array(
            'login' => null,
            'hash' => null,
            'room' => null,
            'beats' => 0);
        $this->_dane = $dane;
    }

    public static function delete() {
        $ss = self::getInstance();
        session_destroy();
        $ss->deleteData();
    }

    public static function refresh() {
        self::getInstance();
        session_regenerate_id();
    }

}
