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
// Chatt
use Chatt;
// Doctrine
use Doctrine\ORM\EntityManager;
// Moje modele
use Application\Entity;

class AdminController extends AbstractActionController {

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Zend\View\Model\ViewModel
     */
    protected $viewModel;

    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    public function indexAction() {
        $this->viewModel = new ViewModel();
        $auth = Chatt\Auth::getInstance();

        $request = $this->getRequest();

        /*
         * Namiastka autoryzacji ;)
         */
        if ($request->isPost() && isset($_POST['login'])) {
            $auth->createAdmin(htmlspecialchars($_POST['login']));
            $this->viewModel->setVariables(array('logged' => true));
        }

        if ($auth->adminLogged()) {
            $this->viewModel->setVariables(array('logged' => true, 'adminlogin' => $auth->adminGetLogin()));
        } else {
            $this->viewModel->setVariables(array('logged' => false));
        }
        return $this->viewModel;
    }

    public function logoutAction() {
        $auth = Chatt\Auth::getInstance();
        $auth->adminLogout();
        $this->redirect()->toUrl('/admin');
    }

    public function roomsAction() {
        $this->viewModel = new ViewModel();
        $auth = Chatt\Auth::getInstance();

        if ($auth->adminLogged()) {
            $rooms = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedRooms')
                    ->findAll();
            $this->viewModel->setVariables(array('rooms' => $rooms));
            return $this->viewModel;
        } else {
            $this->redirect()->toUrl('/admin');
        }
    }

    public function usersAction() {
        $this->viewModel = new ViewModel();
        $auth = Chatt\Auth::getInstance();

        if ($auth->adminLogged()) {
            $users = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedUsers')
                    ->findAll();
            $this->viewModel->setVariables(array('users' => $users));
            return $this->viewModel;
        } else {
            $this->redirect()->toUrl('/admin');
        }
    }

    public function addRoomAction() {
        $this->viewModel = new ViewModel();
        $auth = Chatt\Auth::getInstance();

        if ($auth->adminLogged()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                try {
                    $objectManager = $this->getEntityManager();
                    $room = new \Application\Entity\MedRooms();
                    $room->setActive(true);
                    $hash = Chatt\Safe::khash(uniqid(microtime(true), true));
                    $room->setHash($hash);
                    $room->setName($_POST['nazwa']);
                    $room->setLastAct(time());
                    $pass = $_POST['password1'] === $_POST['password2'] ? $_POST['password1'] : null;
                    $room->setPass(sha1($hash . $pass));

                    $objectManager->persist($room);
                    $objectManager->flush();
                    $this->redirect()->toUrl('/admin/rooms');
                    //$this->viewModel->setVariables(array('error' => '$e->getMessage()'));
                } catch (\Exception $e) {
                    $this->viewModel->setVariables(array('error' => $e->getMessage()));
                }
            }
            return $this->viewModel;
        } else {
            $this->redirect()->toUrl('/admin');
        }
    }
    public function addUserAction() {
        $this->viewModel = new ViewModel();
        $auth = Chatt\Auth::getInstance();

        if ($auth->adminLogged()) {
            $request = $this->getRequest();
            
            $rooms = $this->getEntityManager()
                    ->getRepository('Application\Entity\MedRooms')
                    ->findAll();
            $this->viewModel->setVariables(array('rooms' => $rooms));
            
            if ($request->isPost()) {
                try {       
                    
                    // mikro-validacja
                    $ok = true;
                    $reason = array();
                    if(!isset($_POST['imieNazwisko']) || mb_strlen($_POST['imieNazwisko']) < 5 || !stristr($_POST['imieNazwisko'], ' ')) {
                        $ok = false;
                        $reason['imieNazwisko'] = 'Nieprawidłowe pole imię i nazwisko!';
                    }
                    if(!isset($_POST['username']) || mb_strlen($_POST['username']) < 4) {
                        $ok = false;
                        $reason['username'] = 'Nieprawidłowe lub za krótkie pole username!';
                    }
                    if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                        $ok = false;
                        $reason['email'] = 'Nieprawidłowy adres e-mail!';
                    }
                    $objectManager = $this->getEntityManager();
                    $room = $objectManager->getRepository('Application\Entity\MedRooms')
                        ->findOneBy(array('id' => $_POST['pokoj']));
                    
                    if(!isset($room)) {
                        $ok = false;
                        $reason['pokoj'] = 'Niepoprawny pokój!';
                    }
                    
                    if(!$ok) {
                        throw new \Exception(implode('<br />', $reason));
                    }
                    
                    // koniec // mikro-validacja                    
                    
                    $user = new \Application\Entity\MedUsers();
                    $user->setActive(true)
                            ->setColor(Chatt\Safe::makeColor())
                            ->setEmail($_POST['email'])
                            ->setImieNazwisko($_POST['imieNazwisko'])
                            ->setRoom($room)
                            ->setUsername($_POST['username']);

                    $objectManager->persist($user);
                    $objectManager->flush();
                    $this->redirect()->toUrl('/admin/users');
                } catch (\Exception $e) {
                    $this->viewModel->setVariables(array('error' => $e->getMessage()));
                }
            }
            return $this->viewModel;
        } else {
            $this->redirect()->toUrl('/admin');
        }
    }

    public function triggerAction() {
        $this->viewModel = new ViewModel();
        $objectManager = $this->getEntityManager();
        $auth = Chatt\Auth::getInstance();
        $id = $_GET['id'];

        if ($auth->adminLogged()) {
            if (stristr($_SERVER['REQUEST_URI'], 'trigger-room')) {
                $room = $objectManager->getRepository('Application\Entity\MedRooms')
                        ->findOneBy(array('id' => $id));
                
                if($room) {
                    $active = $room->getActive();
                    $room->setActive(!$active);
                    
                    $objectManager->persist($room);
                    $objectManager->flush();
                }
                
                $this->redirect()->toUrl('/admin/rooms');
                
            } elseif (stristr($_SERVER['REQUEST_URI'], 'trigger-user')) {
                $user = $objectManager->getRepository('Application\Entity\MedUsers')
                        ->findOneBy(array('id' => $id));
                
                if($user) {
                    $active = $user->getActive();
                    $user->setActive(!$active);
                    $objectManager->persist($user);
                    $objectManager->flush();
                }
                
                $this->redirect()->toUrl('/admin/users');
            } else {
                $this->redirect()->toUrl('/admin');
            }
            return $this->viewModel;
        } else {
            $this->redirect()->toUrl('/admin');
        }
    }

}