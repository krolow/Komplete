<?php
class CompleteController extends KompleteAppController {

    public $components = array(
        'RequestHandler',
    );

    public function search($model, $relation) {
        $modelInstance = ClassRegistry::init($model);
        $options = $modelInstance->search(
            $relation, 
            $this->request->query['search']
        );
        $this->set('options', $options);
        $this->set('_serialize', array('options'));
    }

}