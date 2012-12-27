<?php
/**
* Complete App Controller
*
* PHP 5.3
*
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @version       0.1
* @link          https://github.com/krolow/Komplete
* @package       Komplete.Controller.CompleteController
* @author        Vinícius Krolow <krolow@gmail.com>
* @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
*/
class CompleteController extends KompleteAppController {

    /**
     * Components
     * 
     * @var array Components loaded
     * @access  public
     */
    public $components = array(
        'RequestHandler',
    );

    /**
     * Search in the relation model
     * 
     * @param string $model The model
     * @param string $relation The relation model
     * 
     * @access  public
     */
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