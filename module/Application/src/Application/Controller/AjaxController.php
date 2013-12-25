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
// Pusher
use ZfrPusher\Client\Credentials;
use ZfrPusher\Client\PusherClient;
use ZfrPusher\Service\PusherService;

class AjaxController extends AbstractActionController {
    const APP_ID = '62399';
    const KEY = 'c40d70faadb30d3c0316';
    const SECRET = '1c3d1771796b881b99a3';

    public function indexAction() {
        $credentials = new Credentials(self::APP_ID, self::KEY, self::SECRET);
        $client = new PusherClient($credentials);
        $service = new PusherService($client);

        // Single channel
        $service->trigger('my-channel-1', 'my-event', array('key' => 'value'));

        // Multiplie channels
        $service->trigger(array('my-channel-1', 'my-channel-2'), 'my-event', array('key' => 'value'));

        return new ViewModel();
    }

}
