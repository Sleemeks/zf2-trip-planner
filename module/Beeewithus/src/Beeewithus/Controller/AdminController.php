<?php

namespace Beeewithus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    protected $authservice;
    protected $sessionstorage;
    
    public function indexAction()
    {
        if (!$this->getServiceLocator()->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('admin', array('action' => 'login'));
        }
        return new ViewModel();
    }
    
    public function loginAction()
    {
        /*
         * In this action render (layout_admin_login)
         */
        
        if ($this->getAuthService()->hasIdentity()){
            return $this->redirect()->toRoute('admin');
        }
        return array(
            'messages'  => $this->flashmessenger()->getMessages()
        );
    }
    
    public function logoutAction()
    {
        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
         
        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('admin', array('action' => 'login'));
    }
    
    public function authenticateAction()
    {
        $route = 'admin';
        $action = array('action' => 'login');
         
        $request = $this->getRequest();
        if ($request->isPost()){
            if(!empty($request->getPost('login')) && !empty($request->getPost('login'))) {
                $this->getAuthService()->getAdapter()
                                ->setIdentity($request->getPost('login'))
                                ->setCredential($request->getPost('password'));
                $result = $this->getAuthService()->authenticate();

                if($result->isValid()) {
                    $action = array();

                    //check if it has rememberMe :
                    if ($request->getPost('remember') == 1 ) {
                        $this->getSessionStorage()->setRememberMe(1);
                        //set storage again 
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write($request->getPost('login'));
                }
            }      
        }
        return $this->redirect()->toRoute($route, $action);
    }
        
    private function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }
    
    private function getSessionStorage()
    {
        if (!$this->sessionstorage) {
            $this->sessionstorage = $this->getServiceLocator()->get('Beeewithus\Auth\AdminAuthStorage');
        }
        return $this->sessionstorage;
    }
}