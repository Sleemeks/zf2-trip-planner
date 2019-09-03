<?php
namespace Beeewithus\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

use Beeewithus\Model\DayTable;
use Beeewithus\Model\SpotTable;
use Beeewithus\Model\BudgetTable;
use Beeewithus\Model\BudgetTypeTable;
use Beeewithus\Model\UserTable;

class PlanTable extends AbstractTableGateway
{
	
	protected $table = 'trip_plan';
	
	protected $_dayTable = null;
	protected $_spotTable = null;
	protected $_budgetTable = null;
	protected $_budgetTypeTable = null;
	protected $_userTable = null;
	
	public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;

		$this->_dayTable = new DayTable($adapter);
		$this->_spotTable = new SpotTable($adapter);
		$this->_budgetTable = new BudgetTable($adapter);
		$this->_budgetTypeTable = new BudgetTypeTable($adapter);
		$this->_userTable = new UserTable($adapter);
		
    }
	
	public function getList($userId){
		
		return $this->select(array("user_id"=>$userId));
		
	}
	
	public function checkTemporaryPlan($userId){
		
		$rows = $this->select(array('user_id' => (int) $userId, "is_saved"=>0))->count();
		if (!$rows){
			return false;
		}
		return true;
		
	}

	/**
 	* Get plan information for user
 	*/
	public function getSavedInfo($userId, $planId = 0){
	
		$result = array();
		if ($planId){
			$plan = (array) $this->select(array('user_id' => (int) $userId, "id"=>$planId))->current();
		}else{
			$plan = (array) $this->select(array('user_id' => (int) $userId, "is_saved"=>0))->current();
		}
		if ($plan['start_date']){
			$plan['start_date'] = date("d/m/Y", strtotime($plan['start_date']));
		}else{
			$plan['start_date'] = "";
		}
		$result['plan'] = $plan;
		
		$select = new Select('trip_day');
		$select->where('trip_plan_id = '.$plan['id']);
     	$select->order(array('position ASC'));

		$days = $this->executeSelect($select);
		
		foreach ($days as $key=>$day){

			$select = new Select("trip_spot");
			$select->where('trip_day_id = '.$day['id']);
	     	$select->order('position ASC');
		
			$day['spots'] = (array) $this->_spotTable->selectWith($select)->toArray();

			$result['days'][(string) $day['position']] = $day;
		
			$select = new Select('trip_budget');
			$select->where('trip_day_id = '.$day['id']);

			$budgetValues = (array) $this->_budgetTable->selectWith($select)->toArray();
			
			$result['days'][(string) $day['position']]['budget'] = $budgetValues;
			
		}
		
		$select = new Select('trip_budget_type');
		$select->where('plan_id = 0 OR plan_id = '.$result['plan']['id']);
		
		$result['budget'] = (array) $this->_budgetTypeTable->selectWith($select)->toArray();
		
		
		
		return $result;
		
	}
	public function checkPlanAndUser($planId, $userId){
		
		$count = $this->select(array("id"=> $planId, "user_id" => $userId))->count();
		if ($count){
			return true;
		}
		return false;
		
	}
	public function updateStartDate($data, $userId){
		
		if (!$this->checkPlanAndUser($data['plan_id'], $userId)){
			return false;
		}
		
		$date = explode("/", $data['date']);
		if (sizeof($date) == 3){
			$this->update(array("start_date"=>date('Y-m-d', strtotime($date[2]."-".$date[1]."-".$date[0]))), "id = ".$data['plan_id']." AND user_id=".$userId);
		}
		
	}
	
	/**
	 * Init of creation new Plan
	 * 
	 * Create new Plan
	 * Create 2 new days for a plan
	 * Create 2 new spots - 1 for each day
	 * 
	 */
	public function initNew($userId, $planId = false, $day1 = false){
		
		if ($planId && !$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		
		$result = array();
		
		if (!$planId && !$day1){
			// Insert a Plan
			$id = $this->insert(array("user_id"=>$userId, "is_saved" => 0));
			$planId = $this->getLastInsertValue();
			$result['plan'] = array('id' => $planId, 'start_date' => "", "is_saved" => '0', "title" => NULL, "user_id" => $userId);
		}
		
		$position = false;
		if (!$day1){
						
			$position = $this->_dayTable->select(array("trip_plan_id" => $planId))->count() + 1;
			
			// Insert day1
			$this->_dayTable->insert(array("trip_plan_id" => $planId, "position" => $position));
			$day1 = $this->_dayTable->getLastInsertValue();
			$lastDay = $this->_dayTable->select(array("id"=>$day1))->current();
			$lastDay['budget'] = array();
			$result['days'][$position] = $lastDay;

		}
		
		if (!$position){
			$position = $this->_spotTable->select(array("trip_day_id" => $day1))->count() + 1;
		}
		
		//Add Spot for day
		$spotPosition = $this->_spotTable->select(array("trip_day_id" => $day1));
		$maxPosition = 1;
		foreach ($spotPosition as $sposition){
			if ($maxPosition < $sposition['position']){
				$maxPosition = $sposition['position'];
			}
		}
		$maxPosition++;
			
		$this->_spotTable->insert(array("trip_day_id"=>$day1, "position"=>$maxPosition));
		$spot1 = $this->_spotTable->getLastInsertValue();
		$lastSpot = $this->_spotTable->select(array("id"=>$spot1))->current();
		$result['days'][$position]['spots'][] = $lastSpot;
		
		if (!$planId && !$day1){
		
			$position = $this->_dayTable->select(array("trip_plan_id" => $planId))->count() + 1;
			
			// Insert day 2
			$this->_dayTable->insert(array("trip_plan_id" => $planId, "position" => $position));
			$day2 = $this->_dayTable->getLastInsertValue();
		
			$lastDay = $this->_dayTable->select(array("id"=>$day2))->current();
			$lastDay['budget'] = array();
			$result['days'][$position] = $lastDay;
		
			// Insert spot for a day 2
			$this->_spotTable->insert(array("trip_day_id"=>$day2, "position"=>$position));
			$spot2 = $this->_spotTable->getLastInsertValue();
			
			$lastSpot = $this->_spotTable->select(array("id"=>$spot2))->current();
			$result['days'][$position]['spots'][] = $lastSpot;
	
		}
		
		$select = new Select('trip_budget_type');
		$select->where('plan_id = 0 OR plan_id = '.$planId);
		
		$result['budget'] = (array) $this->_budgetTypeTable->selectWith($select)->toArray();
		
				
		return $result;
		
	}

	public function deletePlanById($userId, $planId){
		
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}

		$this->_budgetTypeTable->delete(array("plan_id"=>$id));

		$select = new Select('trip_day');
		$select->where('trip_plan_id = '.$planId);

		$days = $this->executeSelect($select);
		
		foreach ($days as $key=>$day){
			$this->_budgetTable->delete(array("trip_day_id" => $day['id']));
			$this->_spotTable->delete(array("trip_day_id"=>$day['id']));
			$this->_dayTable->delete(array("id"=>$day['id']));
		}

		$this->delete(array("id"=>$planId));
		
		return true;
		
				
	}

	public function deleteDay($userId, $planId, $dayId){
			
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_dayTable->delete(array("id"=>$dayId));
		$this->_spotTable->delete(array("trip_day_id"=>$dayId));
		return true;
		
		
	}
	
	public function deleteSpot($userId, $planId, $spotId){
			
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_spotTable->delete(array("id"=>$spotId));
		return true;
		
		
	}
	public function editSpot($userId, $planId, $spotId, $type, $value, $lat = 0, $lng = 0){
			
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		if ($type == "start"){
			$this->_spotTable->update(array("start_time" => $value), "id = ".$spotId);
		}
		if ($type == "end"){
			$this->_spotTable->update(array("finish_time" => $value), "id = ".$spotId);
		}
		if ($type == "point"){
			$this->_spotTable->update(array("start_place" => $value, "lat" => $lat, "lng" => $lng), "id = ".$spotId);
		}
		if ($type == "comment"){
			$this->_spotTable->update(array("comment" => $value), "id = ".$spotId);
		}
		return true;
		
		
	}
	public function recalculateDaysPositions($userId, $planId, $days){
		
		$key = 1;
		foreach ($days as $day){
			
			$id = str_replace("day-", "", $day);
			$this->_dayTable->update(array("position" => $key), "id = ".$id);
			$key++;
		}
		
	}
	public function recalculateSpotsPositions($userId, $planId, $dayId, $spots){
		
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		
		$key = 1;
		foreach ($spots as $spot){
			
			$id = str_replace("spot-", "", $spot);
			$this->_spotTable->update(array("position" => $key, "trip_day_id" => $dayId), "id = ".$id);
			$key++;
		}
		
	}
	public function addBudget($userId, $planId){
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_budgetTypeTable->insert(array("plan_id"=>$planId));	
		return $this->_budgetTypeTable->getLastInsertValue();	
	}
	public function editBudget($userId, $planId, $id, $value){
			
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_budgetTypeTable->update(array("title"=>$value), "id = ".$id);	
		return true;
		
	}
	public function deleteBudget($userId, $planId, $id){
			
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_budgetTypeTable->delete(array("id"=>$id));
		$this->_budgetTable->delete(array("type_id"=>$id));
		return true;
		
	}
	public function saveMoneyBudget($userId, $planId, $dayId, $budgetId, $value){
		
		$checkBudget = $this->_budgetTable->select(array('trip_day_id'=>$dayId, "type_id"=>$budgetId));
		if (sizeof($checkBudget) > 0){
			foreach ($checkBudget as $edit){
				$this->_budgetTable->update(array("amount" => $value), 'trip_day_id = '.$dayId ." AND type_id = ".$budgetId);	
			}
		}else{
			 $this->_budgetTable->insert(array("amount" => $value, 'trip_day_id'=>$dayId, "type_id"=>$budgetId));	
		}
		return true;
		
	}
	public function saveRoadSettings($userId, $planId, $dayId, $value, $type){
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_dayTable->update(array($type => $value), "id = ".$dayId);
		return true;
		
	}
	public function saveSpotState($userId, $planId, $dayId, $state){
		
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->_dayTable->update(array("hideSpots" => $state), "id = ".$dayId);
		return true;
		
	}
	public function savePlan($userId, $planId){
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->update(array("is_saved" => 1), "id = ".$planId);
		return $this->getLastInsertValue();	
	}
	public function updateDayInfo($planId, $dayId, $userId, $value, $type, $lat, $lng, $edited){

		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		switch ($type){
			case "description":
				$data = array();
				$data['description'] = $value;
				$this->_dayTable->update($data, "id = ".$dayId);			
				break;
			case "start":
				$this->_dayTable->update(array("start_place"=>$value, "start_place_lat"=>$lat, "start_place_lng" => $lng), "id = ".$dayId);			
				break;
			case "end":
				$this->_dayTable->update(array("finish_place"=>$value, "finish_place_lat"=>$lat, "finish_place_lng" => $lng), "id = ".$dayId);			
				break;
			case "start_time":
				$this->_dayTable->update(array("start_time"=>$value), "id = ".$dayId);			
				break;
			case "finish_time":
				$data = array();
				$data['finish_time'] = $value;
				$data['finish_time_edited'] = $edited;
				$this->_dayTable->update($data, "id = ".$dayId);			
				break;
			case "way_time":
				$data = array();
				$data['way_time'] = $value;
				$data['way_time_edited'] = $edited;
				$this->_dayTable->update($data, "id = ".$dayId);			
				break;
			case "way_distance":
				$data = array();
				$data['way_distance'] = $value;
				$this->_dayTable->update($data, "id = ".$dayId);			
				break;
			default:
				break;
		}
		
		
		return true;
		
	}
	public function savePlanTitle($userId, $planId, $title){
		
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$this->update(array("title" => $title), "id = ".$planId);
		return $this->getLastInsertValue();	
		
		
	}
	public function isSaveAllowed($userId, $planId){
		if (!$this->checkPlanAndUser($planId, $userId)){
			return false;
		}
		$user = $this->_userTable->select(array("id" => $userId))->current();
		if (isset($user['email']) && $user['email']){
			return true;
		}
		return false;
	}
	
}

