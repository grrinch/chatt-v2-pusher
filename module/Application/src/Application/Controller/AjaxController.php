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

    //oczekiwana wartość, jeśli jest to tylko odpowiedź jałowa
    const HBeat = 'puk';
    //max bić serca
    const MAX_HEART_BEATS = 350;
    // timeout sesji
    const MAX_TIMEOUT = 1200; //60*20 sekund

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

        $sess = Chatt\Session::getInstance();

        $sess->beat();
        $to_logout = self::MAX_TIMEOUT;
        if (time() - $sess->getBeats() < $to_logout) {
            $ans['ans'] = self::HBeat;
            $ans['body']['beats'] = time();
        } else {
            $ans['ans'] = false;
            $ans['body'] = 'clearAll(); errorBox("' . Chatt\Mess::AUTO_LOGOUT . '");';
        }

        $this->viewModel->setVariables($ans);

        return $this->viewModel;
    }

    public function roomNameAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;

        $auth = Chatt\Auth::getInstance();
        if ($auth->hasIdentity()) {
            $room = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedRooms')
                    ->findOneBy(array('id' => $_POST['room'], 'hash' => $_POST['hash'], 'active' => true));

            if (isset($room)) {
                $ans['ans'] = true;
                $ans['body'] = '<h1>Jesteś teraz w pokoju &quot;' . $room->getName() . '&quot;</h1>';
            } else {
                $ans['ans'] = false;
                $ans['body'] = 'errorBox("' . Mess::NAZWA_POKOJU . '");';
            }
        } else {
            $ans['ans'] = false;
            $ans['body'] = 'clearAll(); errorBox("' . Mess::I401 . '");';
        }

        $this->viewModel->setVariables($ans);
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

            $room = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedRooms')
                    ->findOneBy(array('id' => $_POST['roomid'], 'hash' => $_POST['roomhash']));

            $users = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedUsers')
                    ->findBy(array('room' => $room->getId(), 'active' => true));

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

        // najpierw musimy sprawdzić, czy pokój istnieje
        $room = $this->getEntityManager()
                ->getRepository('Application\Entity\MedRooms')
                ->findOneBy(array('name' => $_POST['room']));

        $user = $this->getEntityManager()
                ->getRepository('Application\Entity\MedUsers')
                ->findOneBy(array('username' => $_POST['login']));

        if (!isset($room) || $room->getPass() != sha1($room->getHash() . $_POST['pass'])) {
            // dane pokoju nie są OK
            $ans = array('ans' => false,
                'body' => Chatt\Mess::POKOJ_ZLE_HASLO);
        } elseif (!isset($user) || $user->getRoom()->getId() != $room->getId()) {
            // dane użytkownika nie są OK
            $ans = array('ans' => false,
                'body' => Chatt\Mess::I401);
        } else {
            //pokój istnieje, użytkownik i hasło jest OK
            $id = $room->getId();
            $user->setActive(true);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();

            $ans = array('ans' => true,
                'body' => array(
                    'room' => $id,
                    'login' => $user->getImieNazwisko(),
                    'hash' => $room->getHash()
                )
            );
            $sess->create($ans['body']);
        }
        $this->viewModel->setVariables($ans);

        return $this->viewModel;
    }

    public function logoutAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;
        $sess = Chatt\Session::getInstance();

        if (isset($_SESSION['login']) && isset($_SESSION['room'])) {
            $room = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedRooms')
                    ->findOneBy(array('id' => $_SESSION['room'], 'active' => 1));
            var_dump($_SESSION);
            if ($room) {
                $user = $this->getEntityManager()
                        ->getRepository('Application\Entity\MedUsers')
                        ->findOneBy(array('imieNazwisko' => $_SESSION['login'], 'room' => $room));
                if ($user) {
                    $user->setActive(false);
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                }
            }
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

    public function activateAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;

        $auth = Chatt\Auth::getInstance();
        if ($auth->hasIdentity()) {
            $sess = Chatt\Session::getInstance();
            $ans['ans'] = true;

            $em = $this->getEntityManager();

            $room = $em->getRepository('Application\Entity\MedRooms')
                    ->findOneBy(array('id' => $sess->getRoom(), 'hash' => $sess->getHash(), 'active' => 1));

            $user = $em->getRepository('Application\Entity\MedUsers')
                    ->findOneBy(array('imieNazwisko' => $sess->getLogin(), 'room' => $room));
            $ans['body'] = null;

            if (isset($room) && isset($user)) {
                $talks = $em->getRepository('Application\Entity\MedTalk')
                        ->findBy(array('room' => $room));
                if (isset($talks)) {
                    foreach ($talks as $talk)
                        $ans['body'] .= Chatt\Safe::format($talk->getUser()->getColor(), $talk->getUser()->getImieNazwisko(), $talk->getTime(), $talk->getContent()) . "\n";
                }
            }
        } else {
            $ans['ans'] = false;
            $ans['body'] = 'clearAll(); errorBox("' . Chatt\Mess::I401 . '");';
        }

        $this->viewModel->setVariables($ans);

        return $this->viewModel;
    }

    public function postMessageAction() {
        if ($this->_ajaxCheck())
            return $this->viewModel;

        $auth = Chatt\Auth::getInstance();

        if ($auth->hasIdentity()) {
            $sess = Chatt\Session::getInstance();

            $time = time();
            $em = $this->getEntityManager();

            $room = $em->getRepository('Application\Entity\MedRooms')
                    ->findOneBy(array('id' => $_POST['roomid'], 'hash' => $_POST['roomhash'], 'active' => 1));

            $user = $em->getRepository('Application\Entity\MedUsers')
                    ->findOneBy(array('imieNazwisko' => $_POST['userlogin'], 'room' => $room));

            if (isset($_POST['imageid'])) {
                $image = $em->getRepository('Application\Entity\MedUsers')
                        ->findOneBy(array('is' => $_POST['imageid']));
            }

            try {
                $mess = new \Application\Entity\MedTalk();
                $mess->setContent($_POST['post'])
                        ->setRoom($room)
                        ->setTime($time)
                        ->setUser($user);
                if (isset($image)) {
                    $mess->setImage($image);
                }
                $em->persist($mess);
                $em->flush();
                $lastid = $mess->getId();

                if ($sess->getLastMsgId() === 0)
                    $sess->setLastTalkId($lastid);

                $sess->resetBeats();
                $ans['ans'] = true;
                $ans['body'] = '';
                $body = Chatt\Safe::format($user->getColor(), $user->getImieNazwisko(), $time, Chatt\Safe::par(Chatt\Safe::san($_POST['post'])));

                // pusher
                $credentials = new Credentials(self::APP_ID, self::KEY, self::SECRET);
                $client = new PusherClient($credentials);
                $service = new PusherService($client);
                // Single channel
                if ($sess->getHash() === $room->getHash())
                    $sessionid = $sess->getHash();
                $service->trigger($sessionid, 'message-post', array('body' => $body));
            } catch (\Exception $e) {
                $ans['ans'] = false;
                $ans['body'] = 'errorBox("' . $e->getMessage() . '");';
            }
        } else {
            $ans['ans'] = false;
            $ans['body'] = 'clearAll(); errorBox("' . Chatt\Mess::I401 . '");';
        }
        $this->viewModel->setVariables($ans);

        return $this->viewModel;
    }

}

