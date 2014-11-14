### v0.1 - Use at your own risk

# CakePHP-Serializers

An object oriented solution to CakePHP model data serialization to JSON.

## Installation


### Git ###

``` bash
git clone git@github.com:loadsys/CakePHP-Serializers.git Plugin/Serializers
```

### Composer ###

* Add to your `composer.json` file

``` php
"require": {
  "loadsys/cakephp_serializers": "dev-master"
}
```

Load the plugin and be sure that bootstrap is set to true:

``` php
// Config/boostrap.php
CakePlugin::load('Serializers', array('bootstrap' => true));
// or
CakePlugin::loadAll(array(
	'Serializers' => array('bootstrap' => true),
));
```

## Usage

### Controller Setup ###

Set a `$viewClass` property, either globally in your `Controller/AppController.php` or in specific
controllers as needed:

``` php
public $viewClass = 'Serializers.CakeSerializer';
```

To force JSON rendering from all controller responses, set a `$renderAs` property in `Controller/AppController.php`, and override it with 'html' as needed:

``` php
// Serialize and return JSON:
public $renderAs = 'json';

// No serialization and a view will be used:
public $renderAs = 'html';
```

### Serializer Setup ###

Create a new directory at the `APP` level (Controller, Model, etc.) named `Serializer`. This
directory will contain your specific model serialization classes. For example, if we have a `User`
model with fields `id`, `first_name`, and `last_name` create the file
`Serializer/UserSerializer.php`:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');
}
```

If you need to do any formatting or data manipulation, create a method named after a field. For
example:

``` php
// Serializer/UserSerializer.php
// Uppercase every first_name during serialization
public function first_name($data) {
	return strtoupper($data['first_name']);
}
```

### Controller Usage ###

Simply perform a model find/paginate and set the results to a variable named `$data`.

``` php
// Controller/UsersController.php

public function index() {
	$this->User->recursive = 0;
	$data = $this->paginate();
	$this->set(compact('data'));
}
```

The serializer will transform `$data` to [json:api](http://jsonapi.org/) compliant JSON.

## License

[MIT](https://github.com/loadsys/CakePHP-Serializers/blob/master/LICENSE)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2014
