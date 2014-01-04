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
// Doctrine
use Doctrine\ORM\EntityManager;

class IndexController extends AbstractActionController {

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }

    public function indexAction() {
        /*$objectManager = $this->getEntityManager();
        $user = new \Application\Entity\MedRooms();
        $user->setActive(true);
        $user->setHash('asdasdasd');
        $user->setName('test');
        $user->setLastAct(time());
        $user->setPass(sha1('asdasdasd'.'123'));

        $objectManager->persist($user); // $user is now "managed"
        $objectManager->flush();        // commit changes to db*/

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('r')
                ->from('Application\Entity\MedRooms', 'r')
        /* ->where('r.name = :roomname')
          ->andWhere('active = 1')
          ->andWhere(
          $qb->expr()->literal(sha1(
          $qb->expr()->concat('hash', $_POST['pass'])
          ))
          )
          ->setParameters(array(':roomname' => $_POST['room'])) */;
        $room = $qb->getQuery()->getResult();
        return new ViewModel(array('room' => $room));
    }

}
