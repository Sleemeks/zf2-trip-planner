<?php
namespace Beeewithus\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

class BudgetTypeTable extends AbstractTableGateway
{
	
	protected $table = 'trip_budget_type';
	
	public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
	
	
}

