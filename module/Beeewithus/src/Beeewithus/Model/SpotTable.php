<?php
namespace Beeewithus\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;

class SpotTable extends AbstractTableGateway
{
	
	protected $table = 'trip_spot';
	
	public function __construct(Adapter $adapter) {

        $this->adapter = $adapter;
	
	}
	
	
}

