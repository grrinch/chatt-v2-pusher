<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

// Pusher
use ZfrPusher\Client\Credentials;
use ZfrPusher\Client\PusherClient;
use ZfrPusher\Service\PusherService;

// Chatt
use Chatt;

// Doctrine
use Doctrine\ORM\EntityManager;

// Moje modele
use Application\Entity;

class AjaxController extends AbstractActionController {

    const APP_ID = '62399';
    const KEY = 'c40d70faadb30d3c0316';
    const SECRET = '1c3d1771796b881b99a3';

    /**
     * @var Zend\View\Model\ViewModel lub Zend\View\Model\JsonModel
     */
    protected $viewModel;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    private function _ajaxCheck() {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->viewModel = new ViewModel();
            $response = $this->getResponse();
            $response->setStatusCode(404);
            $this->viewModel->setTerminal(true);
            return true;
        } else {
            $this->viewModel = new JsonModel;
            return false;
        }
    }

    public function indexAction() {
        $credentials = new Credentials(self::APP_ID, self::KEY, self::SECRET);
        $client = new PusherClient($credentials);
        $service = new PusherService($client);

        // Single channel
        $service->trigger('my-channel-1', 'my-event', array('key' => 'value'));

        /* $em = $this->getServiceLocator()
          ->get('doctrine.entitymanager.orm_default');
          $data = $em->getRepository('Application\Entity\MedRooms')->findAll();
          foreach ($data as $key => $row) {
          echo $row->getId();
          echo '<br />';
          } */

        return new ViewModel();
    }

    public function checkSessionAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;

        $auth = Chatt\Auth::getInstance();
        $ans = Array();

        if ($auth->hasIdentity()) {
            $ans['ans'] = true;
            $sess = Chatt\Session::getInstance();
            $ans['body'] = $sess->getSession();

            $room = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedRooms')
                    ->findOneBy(array('id' => $sess->getRoom()));
            $room->setLastAct();
            $this->getEntityManager()->flush();

            $sess->resetBeats();
            $sess->setLastTalkId(0);
        } else {
            $ans['ans'] = false;
            $ans['body'] = Chatt\Mess::ZALOGUJ;
        }

        $this->viewModel->setVariables($ans);

        return $this->viewModel;
    }

    public function heartBeatAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;
        $this->viewModel->setVariables(array('test', 'test2'));
        return $this->viewModel;
    }

    public function roomNameAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;
        $this->viewModel->setVariables(array('test', 'test2'));
        return $this->viewModel;
    }

    public function getRoomUsersAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;
        
        $auth = Chatt\Auth::getInstance();
        $ans = array();
        if ($auth->hasIdentity()) {
            $ans['ans'] = true;
            $ans['body'] = '';

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select(array('r', 'u'))
                    ->from('Application\Entity\MedRooms', 'r')
                    ->leftJoin('Application\Entity\MedUsers', 'u')
                    ->where('r.id = :roomid')
                    ->andWhere('r.hash = :roomhash')
                    ->andWhere('u.active = 1')
                    ->andWhere('r.active = 1')
                    ->setParameters(array(':roomid' => $_POST['roomid'], ':roomhash' => $_POST['roomhash']));
            $users = $qb->getQuery()->getResult();
            
            foreach ($users as $user) {
                $ans['body'] .= '<p style="color: #' . $user->getColor() . '"><b>';
                $ans['body'] .= $user->getImieNazwisko();
                $ans['body'] .= '</b></p>' . "\n";
            }
        } else {
            $ans['ans'] = false;
            $ans['body'] = 'clearAll(); errorBox("' . Chatt\Mess::I401 . '");';
        }

        $this->viewModel->setVariables($ans);
        
        return $this->viewModel;
    }

    public function logMeInAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;

        /* tutaj wszystko jest jeszcze nie przerobione */
        $sess = Chatt\Session::getInstance();
        
        // najpierw musimy sprawdzić, czy pokój istnieje, jeśli nie, to go utworzyć, jeśli tak, zalogować
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
                ->from('Application\Entity\MedRooms', 'r')
                ->where('r.name = :roomname')
                ->andWhere('active = 1')
                ->andWhere(
                        $qb->expr()->literal(sha1(
                                        $qb->expr()->concat('hash', $_POST['pass'])
                        ))
                )
                ->setParameters(array(':roomname' => $_POST['room']));
                $room = $qb->getQuery()->getResult();
        
		if(isset($room[0])) {
                    $r = $room[0];
			$this->getEntityManager()->persist($r);
                        $r->setLastAct();
                        $this->getEntityManager()->flush();
		}
		else {
			//$ret = $this->conn->query('SELECT `id` FROM `rooms` WHERE `name`="'.Safe::mys($name).'" AND `active` = 1');
                        $r = $this->getEntityManager()
                            ->getRepository('Application\Entity\MedRooms')
                            ->findOneBy(array('name' => $_POST['room']));
			if($r) {
				$r = true;
			}
		} 
                $room = $r;
        
        if ($room) { //pokój istnieje
            if ($room === true) { // podał złe hasło
                $ans = array('ans' => false,
                    'body' => Chatt\Mess::POKOJ_ZLE_HASLO);
                $this->viewModel->setVariables($ans);
                
            } else { // podał dobre hasło
                $id = $room->getId();
                $ans = array('ans' => true,
                    'body' => array('room' => $id,
                        'login' => Chatt\Safe::san($_POST['login']),
                        'hash' => $room->getHash()));
                
                //$user = $db->createUser(Safe::san($_POST['login']), $id);
                $user = $this->getEntityManager()
                            ->getRepository('Application\Entity\MedUsers')
                            ->findOneBy(array('username' => $_POST['login'], 'active' => 1, 'room_id' => $id));

		if(!$user) {
			$res = $this->conn->query('INSERT INTO `users` VALUES(NULL, "'.Safe::mys($name).'", "'.Safe::mys($room).'", NULL, NULL, "'.$this->makeColor().'", 1, "'.$_SERVER['REMOTE_ADDR'].'")');
                        $objectManager = $this->getEntityManager();
        $user = new \Application\Entity\MedUsers();
        $user->setActive(true)
                ->setColor($this->makeColor())
                ->setUsername($_POST['login'])
                ->setIp($_SERVER['REMOTE_ADDR'])
                ->setRoom($room)
                ->se
        $user->setName('test');
        $user->setLastAct(time());
        $user->setPass(sha1('asdasdasd'.'123'));

        $objectManager->persist($user); // $user is now "managed"
        $objectManager->flush();        // commit changes to db
        
			$user == true;
		}
                
                if ($user) {
                    if ($user !== true) {
                        $ans['ans'] = false;
                        $ans['body'] = Chatt\Mess::USER_ALREADY_EXISTS;
                        
                    } else {
                        $sess =  Chatt\Session::getInstance();
                        if (!$sess->create($ans['body'])){
                            $ans['ans'] = false;
                            $ans['body'] = Mess::I502;
                        }
                    }
                } else {
                    $ans['ans'] = false;
                    $ans['body'] = Chatt\Mess::I501;
                }
            }
        } // pokój nie istnieje
        else {
            $id = $db->createRoom(Safe::san($_POST['room']), Safe::san($_POST['pass']));
            $db->createUser(Safe::san($_POST['login']), $id);
            $hash = $db->getRoomHashById($id);
            $ans = array('ans' => true);
            if ($hash) {
                if ($id) {
                    $ans['body']['hash'] = $hash;
                    $ans['body']['room'] = $id;
                    $ans['body']['login'] = Safe::san($_POST['login']);
                    if (!$sess->create($ans['body'])) {
                        $ans['ans'] = false;
                        $ans['body'] = Mess::I500;
                    }
                } else {
                    $ans['ans'] = false;
                    $ans['body'] = Mess::I500;
                }
            } else {
                $ans['ans'] = false;
                $ans['body'] = Mess::I500;
            }
        }
        $this->viewModel->setVariables($ans);
        
        return $this->viewModel;
    }

    public function logoutAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;
        $sess = Chatt\Session::getInstance();

        if (isset($_SESSION['login']) && isset($_SESSION['room'])) {
            $user = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedUsers')
                    ->findOneBy(array('username' => $_SESSION['login'], 'id' => $_SESSION['room']));

            if ($user instanceof Doctrine\ORM\EntityRepository)
                $user->setActive(0);

            $this->getEntityManager()->flush();
        }

        Chatt\Session::delete();
        return $this->viewModel;
    }

    public function roomCheckAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;

        $room = $this->getEntityManager()
                ->getRepository('Application\Entity\MedRooms')
                ->findOneBy(array('name' => $_POST['room'], 'active' => 1));
        if ($room)
            $id = $room->getId();

        if (isset($id) && is_numeric($id)) {
            $reply = array('ans' => true,
                'body' => Chatt\Mess::POKOJ_JEST);
        } else {
            $ans = false;
            $reply = array('ans' => false,
                'body' => Chatt\Mess::POKOJ_NIE_ISTNIEJE);
        }
        $this->viewModel->setVariables($reply);

        return $this->viewModel;
    }

    public function postMessageAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;
        $this->viewModel->setVariables(array('test', 'test2'));
        return $this->viewModel;
    }
    
    	public static function khash($data) {
	    static $map="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $hash=crc32($data)+0x100000000;
	    $str = "";
	    do {
	        $str = $map[31+ ($hash % 31)] . $str;
	        $hash /= 31;
	    } while($hash >= 1);  
	    
	    return $str;
	}
	
	private function makeColor() {
		return dechex(mt_rand(0, 13)) .
				dechex(mt_rand(0, 13)) .
				dechex(mt_rand(0, 13)) .
				dechex(mt_rand(0, 13)) .
				dechex(mt_rand(0, 13)) .
				dechex(mt_rand(0, 13));
	}

}
