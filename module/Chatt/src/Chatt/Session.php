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
        if (isset($_SESSION['beats']) && $_SESSION['beats'] > 0)
            $beats = $_SESSION['beats'];
        elseif (isset($this->_dane['beats']) && $this->_dane['beats'] > 0)
            $beats = $this->_dane['beats'];
        else
            $beats = 0;
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
        $_SESSION['beats']++;
        return $this->getBeats();
    }

    public function resetBeats() {
        $_SESSION['beats'] = 0;
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
        $ss = self::getInstance();
        session_regenerate_id();
    }

}
