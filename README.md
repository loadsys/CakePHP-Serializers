# CakePHP-Serializers #

[![Build Status](https://travis-ci.org/loadsys/CakePHP-Serializers.svg?branch=master)](https://travis-ci.org/loadsys/CakePHP-Serializers)

An object oriented solution to CakePHP model data serialization to JSON and the
corresponding deserialization of a JSON payload to CakePHP data arrays.

This plugin is designed to match the [Ember Data Spec](http://emberjs.com/guides/models/the-rest-adapter/) 
for serialization and deserialization of records.

As a secondary reference the [json:api](http://jsonapi.org/) spec was also used.

Questions on any implementation details can be answered typically using the Test
Cases as the final authoritative answer.

This is currently not fully production ready - be warned bugs/issues may exist.

This Readme is split into the following sections:

1. [Base Use Cases](#basic-use-case)
1. [Requirements](#requirements)
1. [Installation](#installation)
1. [Basic Setup](#basic-setup)
1. [Basic Controller Setup - Serializing](#basic-controller-setup---serializing)
1. [Basic Controller Setup - Deserializing](#basic-controller-setup---deserializing)
1. [Advanced Setup - Serializing](#advanced-setup---serializing)
1. [Advanced Setup - Deserializing](#advanced-setup---deserializing)
1. [Advanced Examples](#advanced-examples)
1. [Contributing](#contributing)
1. [License](#license)
1. [Copyright](#copyright)

## Copyright

## Basic Use Case ##

The basic concept for this plugin is to serialize data when rendering a view:

```php
$data = array(
	'User' => array(
		'id' => 1,
		'username' => 'testusername',
		'first_name' => 'first',
		'last_name' => 'last',
		'is_active' => true,
	)
);
```

into:

```javascript
{
	"user": {
		"id": 1,
		"username": "testusername",
		"first_name": "first",
		"last_name": "last",
		"is_active": true,
	}
}
```

And to perform the reverse, by deserializing data passed in the request body:

```javascript
{
	"users": {
		"id": 1,
		"username": "testusername",
		"first_name": "first",
		"last_name": "last",
		"is_active": true,
	}
}
```

or:

```javascript
{
	"user": {
		"id": 1,
		"username": "testusername",
		"first_name": "first",
		"last_name": "last",
		"is_active": true,
	}
}
```

into

```php
$this->request->data = array(
	'User' => array(
		'id' => 1,
		'username' => 'testusername',
		'first_name' => 'first',
		'last_name' => 'last',
		'is_active' => true,
	)
);
```

## Requirements ##

* PHP >= 5.3.0
* CakePHP >= 2.3

## Installation ##

### Composer ###

* Run this shell command

```bash
php composer.phar require loadsys/cakephp_serializers "dev-master"
```

### Git ###

```bash
git clone git@github.com:loadsys/CakePHP-Serializers.git Plugin/Serializers
```

## Basic Setup ##

Load the plugin and be sure that bootstrap is set to true:

```php
// Config/boostrap.php
CakePlugin::load('Serializers', array('bootstrap' => true));
// or
CakePlugin::loadAll(array(
	'Serializers' => array('bootstrap' => true),
));
```

If you are planning on using this plugin, to deserialize data in an HTTP request 
a few other changes are required:

```php
// Config/boostrap.php
Configure::write('Dispatcher.filters', array(
	'Serializers.DeserializerFilter',
));
```

When deserializing data and setting your CakePHP controller to respond to REST
HTTP requests you will also need to add:

```php
// Config/routes.php
Router::mapResources(array(
	'{controller_name}',
));
Router::parseExtensions('json');
```

The [CakePHP book has more information on doing REST APIs](http://book.cakephp.org/2.0/en/development/rest.html)
with CakePHP and this feature.

### Basic Controller Setup - Serializing ###

Set a `$viewClass` property, either globally in your `Controller/AppController.php` or in specific
controllers as needed:

```php
public $viewClass = 'Serializers.CakeSerializer';
```

To force JSON rendering from all controller responses, set a `$renderAs` property in `Controller/AppController.php`, and override it with 'html' as needed:

``` php
// Serialize and return JSON, no view files are used when rendering the JSON:
public $renderAs = 'json';

// No serialization and a view will be used:
public $renderAs = 'html';
```

At this point, there will be a default Serializer Class created for every Model. 
This Serializer will require every attribute in the Model Schema to be passed to 
it for output. If all you want to do is to Serialize the output of every field 
in a Model with no data manipulation and all fields required, this is all you need.

Create your Controller method that sets whatever data you need to be output as 
JSON, to the view variable `$data`, you must do this for Serializers to
function properly.

``` php
// Controller/UsersController.php

public function index() {
	$this->User->recursive = 0;
	$data = $this->paginate();
	$this->set(compact('data'));
}
```

No view files are used to render the output, simply visit:
http://path/to/cake/app/{controller-name}/{method-name}
And you should see your data rendered as JSON.

This is the minimal use case for serializing data from your CakePHP Controller to
JSON at the view layer.

## Basic Controller Setup - Deserializing ##

The Deserializer Dispatch Filter will transform the JSON payload of an HTTP 
request from [Ember Data](http://emberjs.com/guides/models/the-rest-adapter/) 
compliant  JSON to the `Controller->request->data` property and as a standard 
CakePHP array format. No other code changes are required for basic 
deserilization to work.

## Advanced Setup - Serializing ##

To do anything advanced with serializing data requires a custom Serializer class:

Create a new directory at the `APP` level (Controller, Model, etc.) named `Serializer`. 
This directory will contain your specific Model serialization classes. 
For example, if we have a `User` model with fields `id`, `first_name`, 
`last_name`, `created` and `modified` create the file `Serializer/UserSerializer.php`:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
}
```

You can set the fields that are required to be included in the serialized data, by
adding a `$required` property.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array(
		'id',
		'first_name',
		'last_name'
	);
}
```

All other fields (in this exampled `created` and `modified`) will be suppressed
when rendering the JSON.

## Advanced Setup - Deserializing ##








## Usage ##

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

#### Format Return of Data - Serializing ####

If you need to do any formatting or data manipulation when serializing data,
create a method named after a field with the prefix `serialize_`. For example:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	// $data is the pre-serialized data record for the $data[User] from the controller
	// $value is the pre-serialized value for the $data[User][first_name]
	public function serialize_first_name($data, $value) {
		return strtoupper($data['first_name']);
	}
}
```

This will return the `first_name` as upper case when serializing data.

Methods on serialized data will override calling a SubSerializer if you so desire
it, however you will need to return the array wrapped in the name of the sub-model

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	// $data is an empty array in this case
	// $value is the pre-serialized value for the $data[User]['Permission']
	public function serialize_Permission($data, $value) {
		return array(
			'permissions' => array_value($value),
		);
	}
}
```

Will result in:

```php
$data = array(
	'User' => array(
		'id' => 1,
		'username' => 'testusername',
		'first_name' => 'first',
		'last_name' => 'last',
		'is_active' => true,
	),
	'Permission' => array(
		0 => 'write:testing',
		1 => 'read:secondary',
	),
);
```

being transformed into:

```javascript
{
	"user": {
		"id": 1,
		"username": "testusername",
		"first_name": "first",
		"last_name": "last",
		"is_active": true,
		"permissions": {
			'write:testing',
			'read:secondary',
		}
	}
}
```

#### Format Return of Data - Deserializing ####

If you need to do any formatting or data manipulation when deserializing data,
create a method named after a field with the prefix `deserialize_`. For example:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {
	public $required = array('id', 'first_name', 'last_name');

	// $data is the deserialized data record that will be set to the data CakeRequest property
	// $record is the pre-deserialized record for the {"users":} from the HTTP request
	public function deserialize_first_name($data, $record) {
		return strtoupper($data['first_name']);
	}
}
```

This will return the `first_name` as upper case when serializing data.

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
	// $value is the pre-serialized value for the $data[User][first_name]
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

	/**
	 * Callback method called after automatic serialization. Whatever is returned
	 * from this method will ultimately be used as the JSON response.
	 *
	 * @param multi $serializedData serialized record data
	 * @param multi $unserializedData raw record data
	 * @return multi
	 */
	public function afterSerialize($serializedData, $unserializedData) {
		return $serializedData;
	}
}
```

#### AfterDeserializer Callbacks ####

There is an afterDeserialize callback setup if you wish to do some amount of
post processing after all the data has been deserialized.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	public $required = array('id', 'first_name', 'last_name');

	public $optional = array('email');

	/**
	 * Callback method called after automatic deserialization. Whatever is returned
	 * from this method will ultimately be used as the Controller->data for cake
	 *
	 * @param  multi $deserializedData the deserialized data
	 * @param  multi $serializedData   the original un-deserialized data
	 * @return multi
	 */
	public function afterDeserialize($deserializedData, $serializedData) {
		return $deserializedData;
	}
}
```





## Advanced Examples ##

We can serialize both multiple records:

```php
$data = array(
	'User' => array(
		0 => array(
			'id' => 1,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
		),
		1 => array(
			'id' => 2,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
		),
	)
);
```

into:

```javascript
{
	"users": [
		{
			"id": 1,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
		},
		{
			"id": 2,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
		},
	]
}
```

And serialize sub model records, even if there are multiple records:

```php
$data = array(
	'User' => array(
		0 => array(
			'id' => 1,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
			'SecondaryModel' => array(
				"something" => "blahh",
			),
		),
		1 => array(
			'id' => 2,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
			'SecondaryModel' => array(
				0 => array(
					"something" => "teasdf",
				),
				1 => array(
					"something" => "fgdfghdfg",
				),
			),
		),
	)
);
```

into

```javascript
{
	"users": [
		{
			"id": 1,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
			"secondary_models": {
				"something": "blahh",
			}
		},
		{
			"id": 2,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
			"secondary_models": [
				{
					"something": "teasdf",
				},
				{
					"something": "fgdfghdfg",
				}
			]
		},
	]
}
```

The same with deserialize both multiple records:

```javascript
{
	"users": [
		{
			"id" : 1,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
		},
		{
			"id": 2,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
		},
	]
}
```

into

```php
$this->request->data = array(
	'User' => array(
		0 => array(
			'id' => 1,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
		),
		1 => array(
			'id' => 2,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
		),
	)
);
```

And deserialize sub model records, even if there are multiple records:

```javascript
{
	"users": [
		{
			"id": 1,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
			"secondary_models": {
				"something": "blahh",
			}
		},
		{
			"id": 2,
			"username": "testusername",
			"first_name": "first",
			"last_name": "last",
			"is_active": true,
			"secondary_models": [
				{
					"something": "teasdf",
				},
				{
					"something": "fgdfghdfg",
				}
			]
		},
	]
}
```

into

```php
$this->request->data = array(
	'User' => array(
		0 => array(
			'id' => 1,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
			'SecondaryModel' => array(
				"something" => "blahh",
			),
		),
		1 => array(
			'id' => 2,
			'username' => 'testusername',
			'first_name' => 'first',
			'last_name' => 'last',
			'is_active' => true,
			'SecondaryModel' => array(
				0 => array(
					"something" => "teasdf",
				),
				1 => array(
					"something" => "fgdfghdfg",
				),
			),
		),
	)
);
```

If there is a second top level model in the data to be serialized it is moved
to a property of the first model

```php
$data = array(
	'User' => array(
		'id' => 1,
		'username' => 'testusername',
		'first_name' => 'first',
		'last_name' => 'last',
		'is_active' => true,
	),
	'SecondModel' => array(
		'id' => 1,
		'name' => 'asdflkjasdf',
	),
);
```

into:

```javascript
{
	"user": {
		"id": 1,
		"username": "testusername",
		"first_name": "first",
		"last_name": "last",
		"is_active": true,
		"second_model": {
			'id': 1,
			'name': 'asdflkjasdf',
		}
	}
}
```

If there is a second top level model in the data to be deserialized, it is
ignored:

```javascript
{
	"users": {
		"id": 1,
		"username": "testusername",
		"first_name": "first",
		"last_name": "last",
		"is_active": true,
	},
	"second_models": {
		"id": 1,
		"something": "data",
	}
}
```

into

```php
$this->request->data = array(
	'User' => array(
		'id' => 1,
		'username' => 'testusername',
		'first_name' => 'first',
		'last_name' => 'last',
		'is_active' => true,
	)
);
```

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
