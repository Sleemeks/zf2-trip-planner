<?php

namespace Beeewithus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail;

use Zend\View\Model\JsonModel;

// use Beeewithus\Model\User;

class ProfileController extends AbstractActionController
{

	private $userId = null;
	
	protected $_planTable;
	protected $_userTable;
	
	public function loginAction(){
		
		$data = $this->getRequest()->getPost();
		if (!isset($data['email']) || !isset($data['password'])){
			echo 0;
			return $this->response;
		}
		
		$loginResult = $this->_userTable->loginUser($data);
		if ($loginResult){
			echo 1;
		}else{
			echo 0;
		}
		return $this->response;
		
	}
	public function logoutAction(){
		
		$this->_userTable->logoutUser();
		return $this->response;
		
	}
	public function registerAction(){
		
		$data = $this->getRequest()->getPost();
		if (!isset($data['email']) || !isset($data['pass_confirmation']) || !isset($data['pass'])){
			echo 0;
			return $this->response;
		}
		
		if ($data['pass_confirmation'] != $data['pass']){
			echo "Passwords mismatch, please check";
			return $this->response;
		}
		
		$result = $this->_userTable->createUser(array("email" => $data['email'], "password" => $data['pass']));
		if (!$result){
			echo "User with this email already exists";
		}else{
			echo 1;
		}
		
		return $this->response;
		
	}
	
	public function __construct($userTable)
    {
		
		$this->_userTable = $userTable;
		$this->userId = $this->_userTable->isLoggedIn();
		
		// $fb = new \Facebook\Facebook([
		  // 'app_id' => '1138486689496016',
		  // 'app_secret' => '4dea3bf47472cd33dc35f742d1a30f6d',
		  // 'default_graph_version' => 'v2.5',
		// ]);
		
		// $helper = $fb->getRedirectLoginHelper();
		
		// $permissions = ['email']; // Optional permissions
		
		// $loginUrl = $helper->getLoginUrl('http://public.trip_planner.loc/profile/flogin', $permissions);
		// echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
		
		// exit;	
		

		// $helper = new DataHelper();
// 			
		// $userSession = new Container('user');
		// if (!$userSession->session_key){
			// $sessionKey = $helper->generateRandomString();
            // $userId = $userTable->createSessionUser($sessionKey);
			// $userSession->session_key = $sessionKey;
		// }else{
			// $userId = $userTable->getUserBySessionKey($userSession->session_key);
			// if (!$userId){
				// $sessionKey = $helper->generateRandomString();
	            // $userId = $userTable->createSessionUser($sessionKey);
				// $userSession->session_key = $sessionKey;
			// }
		// }
		// $this->userId = $userId;
		
	}

	public function register(){
		
		$data = $this->getRequest()->getPost();
		
		
		
	}

	public function resetAction(){

	    $key = $this->params()->fromQuery('k',null);
        $user = $this->_userTable->getUserByPasswordKey($key);

        if (!$user){
            $this->flashmessenger()->addMessage("This recovery link is wrong", "error");
            return $this->redirect()->toUrl('/');
        }

        $view = new ViewModel(["key" => $key]);

        $view->setTemplate('planner/profile/password_reset.phtml');
        return $view;

    }

    public function resetPostAction(){

        $data = $this->getRequest()->getPost();
        if ($data['pass_confirmation'] != $data['pass']){
            $redirectUrl = $this->getRequest()->getHeader('HTTP_REFERER');
            $this->redirect()->toUrl($redirectUrl);
            return;
        }

        $key = $this->params()->fromQuery('k',null);
        $user = $this->_userTable->getUserByPasswordKey($key)->current();

        $this->_userTable->updateUserPassword($user->email, $data['pass']);

        $this->flashmessenger()->addMessage("Your password was updated", "success");
        return $this->redirect()->toUrl('/');

    }

	public function getPlanTable() {
		
        if (!$this->_planTable) {
            $sm = $this->getServiceLocator();
            $this->_planTable = $sm->get('Beeewithus\Model\PlanTable');
        }
        return $this->_planTable;
    }
	public function indexAction(){
		
		if (!$this->userId){
			return $this->redirect()->toUrl('/');
		}
		
		$plansList = $this->getPlanTable()->getList($this->userId);
		
		$view = new ViewModel(array(
           'list' => $plansList,
         ));
		 
        $view->setTemplate('beeewithus/plan/list.phtml'); 
        return $view;
		
	}
	
	public function editAction(){
		
		if (!$this->userId){
			return $this->redirect()->toUrl('/');
		}
		
		$view = new ViewModel(array(
			"user" => $this->_userTable->getUserInfo($this->userId)
        ));
		 
        $view->setTemplate('beeewithus/profile/edit.phtml'); 
        return $view;
		
	}
	public function forgotAction(){

        $post = $this->getRequest()->getPost();
        $error = array();
        if (isset($post['email'])){

            if (filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {

                $userId = $this->_userTable->createForgotPwdHash($post['email']);
                if ($userId === false) {
                    $result = new JsonModel(["error"=>true]);
                }

                $userData = $this->_userTable->getUserInfo($userId);

                $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
                $content = $this->renderer->render('planner/email/forgot_password.phtml', ["hash" => $userData->password_recovery]);

                $html = new MimePart($content);
                $html->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($html));

                $config = $this->getServiceLocator()->get('Config');
                $mail = new Mail\Message();
                $mail->setBody($body);
                $mail->setFrom($config['email_sender_email']['email'], $config['email_sender_email']['name']);
                $mail->addTo($post['email'], '');
                $mail->setSubject('TestSubject');

                $transport = new Mail\Transport\Sendmail();
                $transport->send($mail);

            }else{
                $error[] = "Please enter Email address";
            }
        }

        $result = new JsonModel($error);
        return $result;

    }
	public function editPostAction(){
		
		$post = $this->getRequest()->getPost();
		$error = array();
		if (isset($post['email'])){
		
			if (filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
			    
				$this->_userTable->updateEmail($this->userId, $post['email']);
				if (isset($post['newPassword']) && isset($post['newPassword2']) && isset($post['oldPassword']) && $post['newPassword'] != "" && $post['newPassword2'] != "" && $post['oldPassword']){
					
					if ($post['newPassword'] !== $post['newPassword2']){
						$error[] = "Passwords mismatch, please check";
					}else{
						
						$checkUser = $this->_userTable->checkLoginPassword($post['email'], $post['oldPassword']);
						if ($checkUser){
								
							$this->_userTable->updateUserPassword($post['email'], $post['newPassword']);							
							
//							$mail = new Mail\Message();
//							$mail->setBody('Your Password has been changed.');
//							$mail->setFrom('alex.nuzil@gmail.com', 'Sender\'s name');
//							$mail->addTo($post['email'], 'Name of recipient');
//							$mail->setSubject('TestSubject');
//
//							$transport = new Mail\Transport\Sendmail();
//							$transport->send($mail);
							
						}else{
							$error[] = "Your current password is wrong";
						}
						
					}
					
				}
		
				
			}else{
				$error[] = "Your email is wrong, please correct it";
			}
		}else{
			$error[] = "Please enter Email address";
		}
		
		$result = new JsonModel($error);
        return $result;
		
	}
	
}
