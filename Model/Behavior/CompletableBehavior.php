<?php

class CompletableBehavior extends ModelBehavior {
    

    public $settings;

    /**
     * Setup this behavior with the specified configuration settings.
     *
     * @param Model $model  Model using this behavior
     * @param array $config Configuration settings for $model
     *
     * @return void
     * @access public
     */
    public function setup(Model $model, $config = array()) {
        $this->settings[$model->alias] = $config;
    }

    public function beforeSave(Model $model, $created) {
        $separator = $this->settings[$model->alias]['separator'];
        
        foreach ($this->settings[$model->alias]['relations'] as $relation => $value) {
            $model->set(
                $this->insertDataInModel(
                    $model,
                    $relation,
                    $this->processKeywords($model, $relation, $value)
                )
            );
        }

        return true;
    }

    protected function insertDataInModel($model, $relation, $processed)
    {
        if (is_string($processed)) {
            $assocs = $model->getAssociated();
            $foreignKey = ($model->{$assocs[$relation]}[$relation]['foreignKey']);
            unset($model->data[$model->alias][$relation]);
            $model->data[$model->alias][$foreignKey] = $processed;

            return $model->data;
        }

        $model->data[$relation] = array(
            $relation => $processed
        );

        return $model->data;
    }

    protected function processKeywords($model, $relation, $value) {
        $keyword = $model->data[$model->alias][$relation];
        if (!isset($value['multiple']) || $value['multiple'] == false) {
            return $this->processSingleKeywordRelation($model, $relation, $keyword);
        }

        return $this->processMultipleKeywordRelation($model, $relation, $keyword);
    }

    protected function processSingleKeywordRelation(Model $model, $relation, $keyword) {
        $value = trim($keyword);
        $keyword = $this->getKeyword($model, $relation, $value);

        if (!$keyword) {
            $keyword = $this->addKeyword($model, $relation, $value);
        }

        return $keyword[$relation][$model->{$relation}->primaryKey];
    }

    protected function processMultipleKeywordRelation(Model $model, $relation, $keyword) {
        $keywords = explode(', ', $keyword);

        $dataToSave = array();

        foreach ($keywords as $keyword) {
            $key = $this->processSingleKeywordRelation($model, $relation, $keyword);
            $dataToSave[$key] = $key;
        }

        return $dataToSave;
    }

    protected function getKeyword($model, $relation, $keyword)
    {
        return $model->{$relation}->find(
            'first',
            array(
                'conditions' => array(
                    $this->getSearchFieldOfRelation($model, $relation) => $keyword
                )
            )
        );
    }

    protected function addKeyword($model, $relation, $keyword)
    {

        $toSave = array(
            $relation => array(
                $this->getSearchFieldOfRelation($model, $relation) => $keyword
            )
        );

        return $model->{$relation}->save($toSave);
    }

    public function search(Model $model, $relation, $term) {
        $data = $model->{$relation}->find(
            'list',
            array(
                'conditions' => array(
                    $this->getSearchFieldOfRelation($model, $relation) . ' LIKE ' => '%' . $term . '%'
                )
            )
        );

        $options = array();
        foreach ($data as $value) {
            array_push($options, $value);
        }
        
        return $options;        
    }

    protected function getSearchFieldOfRelation(Model $model, $relation) {
        return $this->settings[$model->alias]['relations'][$relation]['field'];
    }

}