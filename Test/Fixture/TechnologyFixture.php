<?php
class TechnologyFixture extends CakeTestFixture {
	
	public $table = 'technologies';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'lenght' => 150)
	);

}