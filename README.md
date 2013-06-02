# Komplete


CakePHP 2.0 Plugin that aims to make easily the autocomplete fuctionality

## Requirements

- PHP 5.3
- CakePHP 2.0 or >

## Installation

- Clone from github: in your app directory type `git clone git@github.com:krolow/Komplete.git Plugin/Komplete`
- Download an archive from github and extract it in `app/Plugin/Komplete`


## When use it?

### Autocomplete functionality

Several times you want to make one input autcomplete, for the front-end side we have serveral solutions that works pretty well, but in the back-end side we have always to code that feature.

Komplete aims to make plug and play the autocomplete featurein the back-end side.


#### How?

It provides to you one controller action that answer as json the autocomplete functionality, and one behavior to save the data tha comes from the input field.

* It look for existent data
* Performs update/insert
* Performs the relationship between data
* Works with multiple data
* Works with sngle data

### Import data

Import data is always another boring task, we have always to check if the data already exists in the database case it does not exists we should insert and create the relations.

Using Komplete you don't need more to care about this, you just enable Komplete and let the behavior handle that task to you, you pass one field as string, define what will be the property of database that you want to use as the search, and it will look for in database to you, case it exists in database will create the relations need, case not it will insert and also create the relations.


### How it works?

#### Using as autocomplete

**In your model:**

```php
<?php
App::uses('AppModel', 'Model');
/**
 * Event Model
 *
 * @property City $City
 * @property Technology $Technology
 */
class Event extends AppModel {

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
                ),
            ),
            'separator' => ','
        )
    );


/**
 * belongsTo associations
 *
 * @var array
 */
    public $belongsTo = array(
        'City',
    );

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
    public $hasAndBelongsToMany = array(
        'Technology' => array(
            'className' => 'Technology',
            'joinTable' => 'technologies_events',
            'foreignKey' => 'event_id',
            'associationForeignKey' => 'technology_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );
```

**In your view:**

```php
<div class="events add">
    <fieldset>
        <legend><?php echo __('Register your event'); ?></legend>
        <?php 
            echo $this->Form->create('Event', array('type' => 'file'));
            echo $this->Form->input(
                'Event.City', 
                array(
                    'type' => 'text', 
                    'class' => 'komplete input-xlarge',
                    'data-multiple' => false,
                    'data-link' => $this->Html->url(
                        array(
                            'controller' => 'complete',
                            'action' => 'search',
                            'plugin' => 'komplete',
                            'Event',
                            'City',
                            'ext' => 'json'
                        ),
                        true
                    )
                )
            );
            echo $this->Form->input(
                'Event.Technology', 
                array(
                    'type' => 'text',
                    'class' => 'komplete input-xlarge',
                    'data-multiple' => true,
                    'data-link' => $this->Html->url(
                        array(
                            'controller' => 'complete',
                            'action' => 'search',
                            'plugin' => 'komplete',
                            'Event',
                            'Technology',
                            'ext' => 'json'
                        ),
                        true
                    )
                )
            );
        ?>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?php echo __('Submit'); ?></button>
            <button type="button" class="btn"><?php echo __('Cancel'); ?></button>
        </div>
        </form>
    </fieldset>
</div>
<?php
    $this->Html->script(
        array(
            '/js/autocomplete.js',
        ),
        array(
            'inline' => false
        )
    );
?>
```

**The javascript example, in this case using bootstrap of github and jquery:**

```javascript
$(document).ready(function () {

    $('.komplete').typeahead({
        source : function (query, process) {

            var multiple = $(this)[0].$element[0].dataset.multiple;

            if (multiple) {
                query = $.trim(query.split(',').pop());
            }

            $.getJSON(
                $(this)[0].$element[0].dataset.link, 
                {search : query},
                function (data) {
                    process(data.options);
                }
            );
        },
        updater : function (item) {
            var field = $($(this)[0].$element[0]);
            var previous_items = field.val();
            var terms = previous_items.split(',');
            terms.pop();
            terms.push(item);
            $.each(terms, function(idx, val) { terms[idx] = $.trim(val); });

            return terms.join(', ');            
        },
        matcher: function() { return true; },
        autoselect: false,

        highlighter : function (item) {
            var terms = this.query.split(',');
            var query = $.trim(terms.pop(-1))
            return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
                return '<strong>' + match + '</strong>'
            });
        }
    });

});
``` 

#### Using to import

**In your model:**

```php
<?php
App::uses('AppModel', 'Model');
/**
 * Event Model
 *
 * @property City $City
 * @property Technology $Technology
 */
class Event extends AppModel {

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
                ),
            ),
            'separator' => ','
        )
    );


/**
 * belongsTo associations
 *
 * @var array
 */
    public $belongsTo = array(
        'City',
    );

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
    public $hasAndBelongsToMany = array(
        'Technology' => array(
            'className' => 'Technology',
            'joinTable' => 'technologies_events',
            'foreignKey' => 'event_id',
            'associationForeignKey' => 'technology_id',
            'unique' => 'keepExisting',
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );
```

**Importing:**

```php
<?php
class ImportController extends AppController {
 
    public $uses = array(
        'Event'
    );

    public function import() {
        $data = array(
            'Event' => array(
                'name' => 'Testing',
                'City' => 'Pelotas',
                'Technology' => 'PHP, Python, Javascript'
            )
        );

        $this->Event->save($data);
    }

}
```

## License

Licensed under <a href="http://www.opensource.org/licenses/mit-license.php">The MIT License</a>
Redistributions of files must retain the above copyright notice.

## Author

Vinícius Krolow - krolow[at]gmail.com

