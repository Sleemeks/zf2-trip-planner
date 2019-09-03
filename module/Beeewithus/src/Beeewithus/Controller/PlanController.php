<?php

namespace Beeewithus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container;
use Beeewithus\Helper\Data as DataHelper;

use Beeewithus\Model\Plan;

class PlanController extends AbstractActionController
{
	
	protected $_planTable;
	protected $_dayTable;
	 
	private $userId = 1;
	
	public function __construct($userTable)
    {
		
		$helper = new DataHelper();
			
		$userSession = new Container('user');
		if (!$userSession->session_key){
			$sessionKey = $helper->generateRandomString();
            $userId = $userTable->createSessionUser($sessionKey);
			$userSession->session_key = $sessionKey;
		}else{
			// var_dump($userSession->session_key);
			$userId = $userTable->getUserBySessionKey($userSession->session_key);
			if (!$userId){
				$sessionKey = $helper->generateRandomString();
	            $userId = $userTable->createSessionUser($sessionKey);
				$userSession->session_key = $sessionKey;
			}
		}
		$this->userId = $userId;
		// var_dump($this->userId);
		
	}
	
	public function printAction(){
		
		$id = $this->params()->fromRoute('id');

		$plan = $this->getPlanTable()->getSavedInfo($this->userId, $id);

		$view = new ViewModel(array(
           'id' => $id,
           'plan' => $plan
         ));

        $view->setTemplate('beeewithus/plan/print.phtml'); 

		$viewRender = $this->getServiceLocator()->get('ViewRenderer');
    	$html = $viewRender->render($view);
	
		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', '');
		$dompdf->render();
		$dompdf->stream();
		
		return $this->response;		
		
	}
	
	public function printViewAction(){
		
		$id = $this->params()->fromRoute('id');

		$plan = $this->getPlanTable()->getSavedInfo($this->userId, $id);
		
		$view = new ViewModel(array(
           'id' => $id,
           'plan' => $plan
         ));

        $view->setTemplate('beeewithus/plan/print.phtml'); 
        return $view;
		
	}
	
	public function indexAction()
    {
    	
		$view = new ViewModel();
        $view->setTemplate('beeewithus/index/plan.phtml'); 
        return $view;
		
    }
	
	public function listAction(){
		
		$plansList = $this->getPlanTable()->getList($this->userId);
		
		$view = new ViewModel(array(
           'list' => $plansList,
         ));
		 
        $view->setTemplate('beeewithus/plan/list.phtml'); 
        return $view;
		
	}
	public function saveDayAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['planId'])){
			$result = $this->getPlanTable()->savePlanTitle($this->userId, $data['planId'], $data['title']);
		}
		return $this->response;
		
	}
	
    
	public function viewAction(){
		
		$id = $this->params()->fromRoute('id');
		$view = new ViewModel(array(
           'id' => $id,
         ));

        $view->setTemplate('beeewithus/index/plan.phtml'); 
        return $view;
		
	}
	
	public function getPlanTable() {
		
        if (!$this->_planTable) {
            $sm = $this->getServiceLocator();
            $this->_planTable = $sm->get('Beeewithus\Model\PlanTable');
        }
        return $this->_planTable;
    }
	
	
	public function getDayTable() {
		
        if (!$this->_dayTable) {
            $sm = $this->getServiceLocator();
            $this->_dayTable = $sm->get('Beeewithus\Model\DayTable');
        }
        return $this->_dayTable;
    }
	
	
	public function initPlanAction(){
		
		$id = 0;
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && $data['plan_id'] > 0){
			$id = $data['plan_id'];
		}
		if (!$this->getPlanTable()->checkTemporaryPlan($this->userId) && !$id){
			$result = $this->getPlanTable()->initNew($this->userId);
		}else{
			$result = $this->getPlanTable()->getSavedInfo($this->userId, $id);
		}
		
		$result = new JsonModel($result);
		echo $result->serialize();
        return $this->response;
				
	}
	public function updateDayInfoAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['dayId']) && isset($data['value']) && isset($data['plan_id']) && isset($data['type'])){
			
			$edited = 0;
			if (isset($data['edited'])){
				if ($data['edited'] == 1){
					$edited = 1;
				}
			}
			
			$lat = 0;
			if (isset($data['lat'])){
				$lat = $data['lat'];
			}

			$lng = 0;
			if (isset($data['lng'])){
				$lng = $data['lng'];
			}
			
			if ($this->getPlanTable()->updateDayInfo($data['plan_id'], $data['dayId'], $this->userId, $data['value'], $data['type'], $lat, $lng, $edited)){
				echo 1;
				return $this->response;
			}
		}
		echo 0;
		return $this->response;
		
	}
	public function changeDateAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['date'])){
			$this->getPlanTable()->updateStartDate($data, $this->userId);
		}
		return $this->response;
	}
	public function addDayAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id'])){
			$result = $this->getPlanTable()->initNew($this->userId, $data['plan_id'] );
			echo json_encode($result);
		}
		return $this->response;
		
	}
	public function addSpotAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['day_id'])){
			$result = $this->getPlanTable()->initNew($this->userId, $data['plan_id'], $data['day_id'] );
			echo json_encode($result);
		}
		return $this->response;
		
	}
	public function deletePlanAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['planId'])){
			$result = $this->getPlanTable()->deletePlanById($this->userId, $data['planId']);
		}
		return $this->response;
		
	}
	public function deleteDayAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['id'])){
			$result = $this->getPlanTable()->deleteDay($this->userId, $data['plan_id'], $data['id']);
			if ($result){
				echo 1;
				return $this->response;
			}
			echo 0;
		}
		return $this->response;
	}
	public function deleteSpotAction(){
			
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['id'])){
			$result = $this->getPlanTable()->deleteSpot($this->userId, $data['plan_id'], $data['id']);
			if ($result){
				echo 1;
				return $this->response;
			}
			echo 0;
		}
		return $this->response;
	}
	public function editSpotAction(){
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['spot_id']) && isset($data['type'])){
			
			$value = "";
			if (isset($data['value'])){
				$value = $data['value'];
			}
			
			$lat = 0;
			$lng = 0;
			if (isset($data['lat'])){
				$lat = $data['lat'];
			}
			if (isset($data['lng'])){
				$lng = $data['lng'];
			}
			
			$result = $this->getPlanTable()->editSpot($this->userId, $data['plan_id'], $data['spot_id'], $data['type'], $value, $lat, $lng);
		}
		echo 1;
		return $this->response;
	}
	public function recalculatePositionsAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['ids'])){
			$result = $this->getPlanTable()->recalculateDaysPositions($this->userId, $data['plan_id'], $data['ids']);
		}
		return $this->response;
		
	}
	public function recalculateSpotsPositionsAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['day_id']) && isset($data['ids'])){
			$result = $this->getPlanTable()->recalculateSpotsPositions($this->userId, $data['plan_id'], $data['day_id'], $data['ids']);
		}
		return $this->response;
		
	} 
	public function addNewBudgetAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id'])){
			$result = $this->getPlanTable()->addBudget($this->userId, $data['plan_id']);
			echo $result;
		}
		return $this->response;
		
	}
	public function editBudgetAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['id'])){
			$result = $this->getPlanTable()->editBudget($this->userId, $data['plan_id'], $data['id'], $data['value']);
		}
		return $this->response;
		
	}
	public function deleteBudgetAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['budget_id'])){
			$result = $this->getPlanTable()->deleteBudget($this->userId, $data['plan_id'], $data['budget_id']);
		}
		return $this->response;
		
	}
	public function saveMoneyAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['day_id']) && isset($data['budget_id'])){
			$result = $this->getPlanTable()->saveMoneyBudget($this->userId, $data['plan_id'], $data['day_id'], $data['budget_id'], $data['value']);
		}
		return $this->response;
		
	}
	public function saveRoadSettingsAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['day_id']) && isset($data['value']) && isset($data['type'])){
			$result = $this->getPlanTable()->saveRoadSettings($this->userId, $data['plan_id'], $data['day_id'], $data['value'], $data['type']);
		}
		return $this->response;
		
	}
	public function saveSpotStateAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id']) && isset($data['day_id']) && isset($data['state'])){
			$result = $this->getPlanTable()->saveSpotState($this->userId, $data['plan_id'], $data['day_id'], $data['state']);
		}
		return $this->response;
		
	}
	public function savePlanAction(){
		
		$data = $this->getRequest()->getPost();
		if (isset($data['plan_id'])){
			if ($this->getPlanTable()->isSaveAllowed($this->userId, $data['plan_id'])){
				$result = $this->getPlanTable()->savePlan($this->userId, $data['plan_id']);
				echo 1;
			}else{
				echo 0;
			}
		}
		return $this->response;
		
	}
	
}
