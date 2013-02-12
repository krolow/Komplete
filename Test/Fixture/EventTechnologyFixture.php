<?php
class EventTechnologyFixture extends CakeTestFixture {
	
	public $table = 'events_technologies';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'event_id' => array('type' =>  'integer'),
		'technology_id' => array('type' =>  'integer'),
	);

}