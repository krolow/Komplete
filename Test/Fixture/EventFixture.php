<?php
class EventFixture extends CakeTestFixture {

	public $table = 'events';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'lenght' => 150),
		'city_id' => array('type' => 'integer', 'null' => false, 'default' => null),
	);

}