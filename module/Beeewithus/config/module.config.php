<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Beeewithus;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Beeewithus\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Beeewithus\Controller\Admin',
                        'action' => 'index',
                    ),
                ),
            ),
            'date' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/date[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Beeewithus\Controller\Date',
                        'action' => 'index',
                    ),
                ),
            ),
            'plan' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/plan[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Beeewithus\Controller\Plan',
                        'action' => 'index',
                    ),
                ),
            ),
            'profile' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/profile[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Beeewithus\Controller\Profile',
                        'action' => 'index',
                    ),
                ),
            )
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Beeewithus\Controller\Index' => Controller\IndexController::class,
            'Beeewithus\Controller\Admin' => Controller\AdminController::class,
            'Beeewithus\Controller\Date' => Controller\DateController::class,
            // 'Beeewithus\Controller\Plan' => Controller\PlanController::class,
            // 'Beeewithus\Controller\Profile' => Controller\ProfileController::class
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'beeewithusr/index/index' => __DIR__ . '/../view/beeewithus/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
        	'ViewJsonStrategy',
    	)
    ),
    'session' => array(
        'name'              => '_shop_beeewithus',
        'use_cookies'       => true,
        'cookie_httponly'   => true
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
