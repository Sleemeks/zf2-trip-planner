<?php
namespace Beeewithus\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Sql;

use Beeewithus\Model\PlanTable;
use Beeewithus\Model\SpotTable;


class DayTable extends AbstractTableGateway
{
	
	protected $table = 'trip_day';
	
	protected $_planTable = null;
	protected $_spotTable = null;
	
	public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
	
	
}

