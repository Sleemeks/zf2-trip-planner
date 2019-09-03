<?php
namespace Beeewithus\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Beeewithus\Helper\Data as DataHelper;
use Zend\Session\Container;

use Beeewithus\Model\PlanTable;


class UserTable extends AbstractTableGateway
{
	
	protected $table = 'users';
	private $_planTable;
	
	public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
	}
	
	/**
	 * Create New User based just on Session key (not registered)
	 * 
	 * @param - session key
	 * 
	 * @return int - created User ID
	 */
	public function createSessionUser($sessionKey){
		
		$this->insert(array("session_id"=>$sessionKey));
		return $this->getLastInsertValue();
		
	}
	/**
	 * Get User Information from Database by User ID
	 * 
	 * @param int - UserId
	 * 
	 * @return array - User information as array
	 */
	public function getUserInfo($userId){
	
		$result = $this->select(array("id"=>$userId))->current();
		return $result;
		
	}
	/**
	 * Update Email for particular User
	 * 
	 * @TODO - check if this ID belongs to entered Email
	 * 
	 * @param int - User ID
	 * @param string - Email address
	 * 
	 * @return true
	 */
	public function updateEmail($userId, $email){
		
		$this->update(array("email" => $email), "id = ".$userId);
		return true;
		
	}
	public function getUserBySessionKey($key){

		$result = $this->select(array("session_id"=>$key))->current();
		if (isset($result['id'])){
			return $result['id'];
		}
		return false;
		
	}
	public function isLoggedIn(){
		
		$userSession = new Container('user');
		if ($userSession->session_key){
			return $this->getUserBySessionKey($userSession->session_key);	
		}
		return false;
	}
	
	public function createUser($data){
		
		if (isset($data['email']) && isset($data['password'])){

			$user = $this->select(array("email" => $data['email']));
			if ($user->count() > 0){
				return false;
			}

			$password = $data['password'];
			
			$data['password'] = md5($data['password']);
			$this->insert($data);
			
			$data['password'] = $password;
			$this->loginUser($data);
			return true;
		}
		return false;	
	}
	public function logoutUser(){
		
		$userSession = new Container('user');
		$userSession->session_key = false;
		$userSession->user_id = false;
		
		return true;
		
	}
	
	/**
	 * Check if login and password are correct
	 */
	public function checkLoginPassword($login, $password){
		
		$user = $this->select(array("email" => $login, "password" => md5($password)));
		
		if ($user->count() == 1){
			return $user;
		}
		return false;
	}
	
	public function updateUserPassword($login, $password){
		
		$this->update(array("password" => md5($password)), "email = '".$login."'");
		return true;
				
	}
	
	/**
	 * Login User by email and password
	 */
	public function loginUser($data){
		
		if (isset($data['email']) && isset($data['password'])){
		
			$user = $this->checkLoginPassword($data['email'], $data['password']);
			
			$data['password'] = md5($data['password']);
		
			if ($user && $user->count() == 1){
				
				$helper = new DataHelper();
				$userSession = new Container('user');
		
				$sessionId = $helper->generateRandomString();
				$currentUser = $user->current();
		
				if ($userSession->session_key){
					if ($cuserId = $this->getUserBySessionKey($userSession->session_key)){
						$this->_planTable = new PlanTable($this->adapter);
						$this->_planTable->update(array("user_id" => $currentUser->id), "user_id = ".$cuserId);
					}
					
				}
		
				$userSession->session_key = $sessionId;
				$userSession->user_id = $currentUser->id;

				$this->update(array("session_id" => $sessionId), "id = ".$currentUser->id);
			
				return true;
			}
			return false;
		}
		return false;	
		
	}
	public function getUserByPasswordKey($key){

        $user = $this->select(array("password_recovery" => $key));
        if ($user->count() == 0){
            return false;
        }
        return $user;

    }
	public function createForgotPwdHash($email){

        $user = $this->select(array("email" => $email));
        if ($user->count() == 0){
            return false;
        }

        $helper = new DataHelper();
        $hash = $helper->generateRandomString();

        $data['password_recovery'] = $hash;

        $this->update($data, "email = '".$email."'");

        $user = $user->current();
        return $user->id;

    }
}

