<?php
namespace Beeewithus\Model;

class Plan
 {
 	
     public $id;
     public $user_id;
     public $title;
	 public $is_saved;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->user_id = (!empty($data['user_id'])) ? $data['user_id'] : null;
         $this->title  = (!empty($data['title'])) ? $data['title'] : null;
		 $this->is_saved  = (!empty($data['is_saved'])) ? $data['is_saved'] : null;
     }
 }