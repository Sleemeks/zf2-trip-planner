<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Beeewithus;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;

use Zend\Db\Sql\Sql;

use Beeewithus\Model\PlanTable;
use Beeewithus\Model\DayTable;
use Beeewithus\Model\SpotTable;
use Beeewithus\Model\UserTable;

use Beeewithus\Controller\PlanController;
use Beeewithus\Controller\ProfileController;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $config = $e->getApplication()->getServiceManager()->get('Configuration');
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {
            $routeMatch = $e->getRouteMatch();
            $controller = $e->getTarget();
            $actionName = $routeMatch->getParam('action');
            
            if ($actionName == 'login' && $controller instanceof Controller\AdminController) {
                $controller->layout('layout/layout_admin_login.phtml');
            } elseif ($controller instanceof Controller\AdminController) {
                $controller->layout('layout/layout_admin.phtml');
            }
        });
        
		$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
		$userSession = new Container('user');
		$viewModel->session = $userSession;
		
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config['session']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
        
        date_default_timezone_set('Europe/Berlin');
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Beeewithus\Auth\AdminAuthStorage' => function($sm){
                    return new \Beeewithus\Auth\AdminAuthStorage('admin_login');  
                },
                'AuthService' => function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbAuthAdapter  = new DbAuthAdapter($dbAdapter, 'admin_login','login','password', 'MD5(?)');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbAuthAdapter);
                    $authService->setStorage($sm->get('Beeewithus\Auth\AdminAuthStorage'));              
                    return $authService;
                },
                'SqlAdapter' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new Sql($dbAdapter);
                },
                'Beeewithus\Model\DayTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new DayTable($dbAdapter);
                    return $table;
                },
                'Beeewithus\Model\PlanTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new PlanTable($dbAdapter);
                    return $table;
                },
                'Beeewithus\Model\SpotTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpotTable($dbAdapter);
                    return $table;
                },
                'Beeewithus\Model\UserTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                }
            )
        );
    }
	function getControllerConfig()
	{
	  return array(
	    'factories' => array(
	      'Beeewithus\Controller\Plan' => function ($sm) {
	          $locator = $sm->getServiceLocator();
	          $userTable = $locator->get('Beeewithus\Model\UserTable');
	          
	          $controller = new PlanController($userTable);
	          return $controller;
	        },
	        'Beeewithus\Controller\Profile' => function ($sm) {
	          $locator = $sm->getServiceLocator();
	          $userTable = $locator->get('Beeewithus\Model\UserTable');
	          
	          $controller = new ProfileController($userTable);
	          return $controller;
	        },
	      ),
	      
	    );
	}
}