<?php
/**
 * Completable Behavior
 *
 * PHP 5.3
 *
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version       0.1
 * @link          https://github.com/krolow/Komplete
 * @package       Komplete.Model.Behavior.CompletableBehavior
 * @author        Vinícius Krolow <krolow@gmail.com>
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class CompletableBehavior extends ModelBehavior {

/**
 * Model settings
 * 
 * @var array
 */
	public $settings;

/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model  Model using this behavior
 * @param array $config Configuration settings for $model
 *
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings[$model->alias] = $config;
	}

/**
 * beforeSave is called before a model is saved.  Returning false from a beforeSave callback
 * will abort the save operation.
 *
 * @param Model $model Model using this behavior
 * 
 * @return mixed False if the operation should abort. Any other result will continue.
 */
	public function beforeSave(Model $model) {
		$separator = $this->settings[$model->alias]['separator'];

		foreach ($this->settings[$model->alias]['relations'] as $relation => $value) {
			if (!isset($model->data[$model->alias][$relation])) {
				continue;
			}

			$model->set(
				$this->_insertDataInModel(
					$model,
					$relation,
					$this->_processKeywords($model, $relation, $value)
				)
			);

		}

		return true;
	}

/**
 * Insert the data inside the model
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $processed  The keyword proccessed
 * 
 * @return  array
 */
	protected function _insertDataInModel(Model $model, $relation, $processed) {
		unset($model->data[$model->alias][$relation]);
		if (is_string($processed)) {
			$assocs = $model->getAssociated();
			$foreignKey = ($model->{$assocs[$relation]}[$relation]['foreignKey']);
			$model->data[$model->alias][$foreignKey] = $processed;

			return $model->data;
		}

		$model->data[$relation] = array(
			$relation => $processed
		);

		return $model->data;
	}

/**
 * Normalize the given keyword
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $value  The given config of the relation
 * 
 * @return  array
 */
	protected function _processKeywords(Model $model, $relation, $value) {
		$keyword = $model->data[$model->alias][$relation];
		if (!isset($value['multiple']) || $value['multiple'] == false) {
			return $this->_processSingleKeywordRelation($model, $relation, $keyword);
		}

		return $this->_processMultipleKeywordRelation($model, $relation, $keyword);
	}

/**
 * Normalize when it is a single keyword
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $keyword  The given config of the relation
 * 
 * @return  array
 */
	protected function _processSingleKeywordRelation(Model $model, $relation, $keyword) {
		$value = trim($keyword);
		$keyword = $this->getKeyword($model, $relation, $value);

		if (!$keyword) {
			$keyword = $this->addKeyword($model, $relation, $value);
		}

		return $keyword[$relation][$model->{$relation}->primaryKey];
	}

/**
 * Normalize when it is a single keyword
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $keyword  The given config of the relation
 * 
 * @return  array
 */
	protected function _processMultipleKeywordRelation(Model $model, $relation, $keyword) {
		$keywords = explode(', ', $keyword);

		$dataToSave = array();
		foreach ($keywords as $keyword) {
			$key = $this->_processSingleKeywordRelation($model, $relation, $keyword);
			$dataToSave[] = $key;
		}

		return $dataToSave;
	}

/**
 * Look for at database for the given keyword
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $keyword  The given config of the relation
 * 
 * @return  array
 */
	public function getKeyword(Model $model, $relation, $keyword) {
		return $model->{$relation}->find(
			'first',
			array(
				'conditions' => array(
					$this->getSearchFieldOfRelation($model, $relation) => $keyword
				)
			)
		);
	}

/**
 * Insert the given keyword at database
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $keyword  The given config of the relation
 * 
 * @return  mixed
 */
	public function addKeyword($model, $relation, $keyword) {
		$toSave = array(
			$relation => array(
				$this->getSearchFieldOfRelation($model, $relation) => $keyword
			)
		);

		$model->{$relation}->create();
		$data = $model->{$relation}->save($toSave);
		$data[$relation][$model->{$relation}->primaryKey] = $model->{$relation}->getLastInsertId();

		return $data;
	}

/**
 * Search for the existence of the given keyword
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * @param string $keyword  The given config of the relation
 * 
 * @return  array
 */
	public function search(Model $model, $relation, $keyword) {
		$data = $model->{$relation}->find(
			'list',
			array(
				'conditions' => array(
					$this->getSearchFieldOfRelation($model, $relation) . ' LIKE ' => '%' . $keyword . '%'
				)
			)
		);

		$options = array();
		foreach ($data as $value) {
			array_push($options, $value);
		}

		return $options;
	}

/**
 * Retrive what is the search field for the given keyword
 * 
 * @param Model  $model  Model using this behavior
 * @param string $relation  The relation to insert data
 * 
 * @return  string
 */
	public function getSearchFieldOfRelation(Model $model, $relation) {
		return $this->settings[$model->alias]['relations'][$relation]['field'];
	}

}