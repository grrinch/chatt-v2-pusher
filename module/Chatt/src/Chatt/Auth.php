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

}
