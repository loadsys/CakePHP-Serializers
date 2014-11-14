# CakePHP-Serializers #

[![Build Status](https://travis-ci.org/loadsys/CakePHP-Serializers.svg?branch=master)](https://travis-ci.org/loadsys/CakePHP-Serializers)

An object oriented solution to CakePHP model data serialization to JSON.

This is close to production ready however there may be edge
cases not yet observed.

## Installation ##

### Composer ###

* Add to your `composer.json` file

```php
"require": {
  "loadsys/cakephp_serializers": "dev-master"
}
```

### Git ###

```bash
git clone git@github.com:loadsys/CakePHP-Serializers.git Plugin/Serializers
```

Load the plugin and be sure that bootstrap is set to true:

```php
// Config/boostrap.php
CakePlugin::load('Serializers', array('bootstrap' => true));
// or
CakePlugin::loadAll(array(
	'Serializers' => array('bootstrap' => true),
));
```

## Usage ##

### Controller Setup ###

Set a `$viewClass` property, either globally in your `Controller/AppController.php` or in specific
controllers as needed:

```php
public $viewClass = 'Serializers.CakeSerializer';
```

To force JSON rendering from all controller responses, set a `$renderAs` property in `Controller/AppController.php`, and override it with 'html' as needed:

``` php
// Serialize and return JSON:
public $renderAs = 'json';

// No serialization and a view will be used:
public $renderAs = 'html';
```

### Default Serializer Setup ###

There will be a default Serializer Class created for every Model. This Serializer will
require every attribute in the Model Schema to be passed to it for output. If all
you want to do is to Serialize the output of every field in a Model with no data
manipulation and all fields required, this is all you need.

### Custom Serializer Setup ###

#### Basic Custom Serializer Classes ####

Create a new directory at the `APP` level (Controller, Model, etc.) named `Serializer`. This
directory will contain your specific model serialization classes. For example, if we have a `User`
model with required fields `id`, `first_name`, and `last_name` create the file
`Serializer/UserSerializer.php`:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');
}
```

This serializer will throw a `SerializerMissingRequiredException` if the data passed
to the serializer does not include all of these properties.

#### Format Return of Data ####

If you need to do any formatting or data manipulation, create a method named after a field. For
example:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	// $data is the pre-serialized data record for the $data[User] from the controller
	// $record is the pre-serialized record for the $data from the controller
	public function first_name($data, $record) {
		return strtoupper($data['first_name']);
	}
}
```

This will return the `first_name` as upper case.

#### Optional Attributes for Serializers  ####

If you have attributes that you wish to have as optional attributes, ie. attributes
that are passed to the output only when provided to the Serializer, you can setup
an array of optional properties.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	public $optional = array('email');
}
```

If `email` is passed to the Serializer, then `email` will be Serialized and passed
to the output.

#### Optional Methods for Serializers  ####

You can also create methods to process required and optional attributes. The
method for an optional attribute will always get called even if the attribute
is not supplied.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	public $optional = array('email');

	// $data is the pre-serialized data record for the $data[User] from the controller
	// $record is the pre-serialized record for the $data from the controller
	public function email($data, $record) {
		if(!array_key_exists('email', $data)) {
			throw new SerializerIgnoreException();
		}

		return strtoupper($data['email']);
	}
}
```

If `email` exists in the data, then it will be upper cased when returned.

If `email` does not exist in the data, then it will be ignored and no data will
be returned for that key. The base Serializer Class will catch Exceptions of type
`SerializerIgnoreException` and unset the data array for that key.

#### AfterSerializer Callbacks ####

There is an afterSerialize callback setup if you wish to do some amount of
post processing after all the data has been serialized.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	public $optional = array('email');

	// $json is the seralized json data
	// $data is the pre-serialized data record for the User
	public afterSerialize($json, $record) {

	}
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

## Contributing ##

### Reporting Issues ###

Please use [GitHub Isuses](https://github.com/loadsys/CakePHP-Serializers/issues) for listing any known defects or issues

### Development ###

When developing this plugin, please fork and issue a PR for any new development.

The Complete Test Suite for the Plugin can be run via this command:

`./lib/Cake/Console/cake test Serializers AllSerializers`

## License

[MIT](https://github.com/loadsys/CakePHP-Serializers/blob/master/LICENSE)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2014
