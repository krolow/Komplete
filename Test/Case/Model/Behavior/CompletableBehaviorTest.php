<?php
class Event extends CakeTestModel {
	public $useTable = 'events';

	public $actsAs = array(
		'Komplete.Completable' => array(
			'relations' => array(
				'Technology' => array(
					'multiple' => true,
					'field' => 'name',
				),
				'City' => array(
					'multiple' => false,
					'field' => 'name',
				)
			),
			'separator' => ','
		)
	);

	public $belongsTo = array(
		'City'
	);

	public $hasAndBelongsToMany = array(
		'Technology'
	);
}

class Technology extends CakeTestModel {

	public $useTable = 'technologies';

	public $hasAndBelongsToMany = array(
		'Event'
	);

}

class City extends CakeTestModel {

	public $useTable = 'cities';

	public $hasMany = array(
		'Event'
	);

}

class EventTechnology extends CakeTestModel {

	public $useTable = 'events_technologies';

	public $belongsTo = array(
		'Event',
		'Technology'
	);

}

class CompletableBehaviorTest extends CakeTestCase {
	
	public $fixtures = array(
		'plugin.komplete.city',
		'plugin.komplete.event',
		'plugin.komplete.technology',
		'plugin.komplete.event_technology'
	);


	public function startTest($method) {
		$this->Event = ClassRegistry::init('Event');
		$this->Technology = ClassRegistry::init('Technology');
		$this->EventTechnology = ClassRegistry::init('EventTechnology');
		$this->City = ClassRegistry::init('City');
	}

	public function testSaveSingle() {
		$data = array(
			'Event' => array(
				'name' => 'Komplete Event',
				'City' => 'São Paulo'
			)
		);
		$this->Event->save($data);
		$id = $this->Event->getLastInsertId();
		$cityId = $this->Event->City->getLastInsertId();
		$result = $this->Event->findByName('Komplete Event');
		$expected = array(
			'Event' => array(
				'id' => $id,
				'name' => 'Komplete Event',
				'city_id' => $cityId,
			),
			'City' => array(
				'id' => $cityId,
				'name' => 'São Paulo'
			),
			'Technology' => array(

			)
		);
		$this->assertEqual($result, $expected);

		$data = array(
			'Event' => array(
				'name' => 'Komplete Event - 2',
				'City' => 'São Paulo'
			)
		);
		$this->Event->save($data);
		$id = $this->Event->getLastInsertId();
		$cityId = $this->Event->City->getLastInsertId();

		$result = $this->Event->City->findAllByName('São Paulo');
		$this->assertEqual(count($result), 1);

		$result = $this->Event->findByName('Komplete Event - 2');
		$expected = array(
			'Event' => array(
				'id' => $id,
				'name' => 'Komplete Event - 2',
				'city_id' => $cityId,
			),
			'City' => array(
				'id' => $cityId,
				'name' => 'São Paulo'
			),
			'Technology' => array(

			)
		);
		$this->assertEqual($result, $expected);
	}

	public function testSaveMultiple() {
		$data = array(
			'Event' => array(
				'name' => 'Hack Thrusday Party',
				'Technology' => 'PHP, Javascript'
			)
		);
		$this->Event->create();
		$this->Event->save($data);
		$result = $this->Event->findByName($data['Event']['name']);
		$this->assertEqual(count($result['Technology']), 2);

		$result = $this->Event->Technology->findAllByName('PHP');
		$this->assertEqual(count($result), 1);
		$this->assertEqual(count($result[0]['Event']), 1);

		$result = $this->Event->Technology->findAllByName('Javascript');
		$this->assertEqual(count($result), 1);
		$this->assertEqual(count($result[0]['Event']), 1);

		$data = array(
			'Event' => array(
				'name' => 'Hack Thrusday Party 2',
				'Technology' => 'PHP, Javascript, Java'
			)
		);
		$this->Event->create();
		$this->Event->save($data);
		$result = $this->Event->findByName($data['Event']['name']);
		$this->assertEqual(count($result['Technology']), 3);

		$result = $this->Event->Technology->findAllByName('PHP');
		$this->assertEqual(count($result), 1);
		$this->assertEqual(count($result[0]['Event']), 2);

		$result = $this->Event->Technology->findAllByName('Javascript');
		$this->assertEqual(count($result), 1);
		$this->assertEqual(count($result[0]['Event']), 2);

		$result = $this->Event->Technology->findAllByName('Java');
		$this->assertEqual(count($result), 1);
		$this->assertEqual(count($result[0]['Event']), 1);

		$data = array(
			'Event' => array(
				'name' => 'Hack Thursday 2013',
				'Technology' => 'Ruby',
			),
		);
		$this->Event->create();
		$this->Event->save($data);

		$result = $this->Event->findByName($data['Event']['name']);
		$this->assertEqual(count($result['Technology']), 1);
	}

	public function testSaveSingleAndMultiple() {
		$data = array(
			'Event' => array(
				'name' => 'Hack Thursday',
				'Technology' => 'Javascript, PHP',
				'City' => 'Pelotas - RS'
			)
		);
		$this->Event->create();
		$this->Event->save($data);
		$result = $this->Event->findByName($data['Event']['name']);
		$this->assertEqual($result['City']['name'], $data['Event']['City']);
		$this->assertEqual(count($result['Technology']), 2);

		$data = array(
			'Event' => array(
				'name' => 'Hack Thursday New',
				'Technology' => 'Python',
				'City' => 'Pelotas - RS'
			)
		);
		$this->Event->create();
		$this->Event->save($data);
		$result = $this->Event->findByName($data['Event']['name']);
		$this->assertEqual($result['City']['name'], $data['Event']['City']);
		$this->assertEqual(count($result['Technology']), 1);	
	}

	public function endTest($method) {
		ClassRegistry::flush();
		unset($this->Event);
		unset($this->Technology);
		unset($this->City);
	}

}