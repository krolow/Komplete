<?php
class EventFixture extends CakeTestFixture {
	
	public $table = 'events';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'lenght' => 150),
		'city_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
	);

}