# CakePHP-Serializers #

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/loadsys/CakePHP-Serializers.svg?branch=master&style=flat-square)](https://travis-ci.org/loadsys/CakePHP-Serializers)
[![Total Downloads](https://img.shields.io/packagist/dt/loadsys/cakephp_serializers.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp_serializers)

An object oriented solution to serialize CakePHP response to JSON and 
correspondingly deserialize JSON into CakePHP data arrays.

This plugin is designed to match the [Ember Data](http://emberjs.com/guides/models/the-rest-adapter/)
and the [DS.ActiveModelAdapter](http://emberjs.com/api/data/classes/DS.ActiveModelAdapter.html) for
serialization and deserialization of CakePHP generated responses.

Questions on any implementation details can be answered typically using the Test
Cases as the final authoritative answer.

This is currently not fully production ready - be warned bugs/issues may exist.

This README is split into the following sections, with additional README documents
covering certain topics.

1. [Base Use Case](#basic-use-case)
1. [Requirements](#requirements)
1. [Installation](#installation)
1. [Basic Setup](#basic-setup)
1. [Basic Controller Setup - Deserializing](#basic-controller-setup---deserializing)
1. [Error and Exception Handling Setup](#error-and-exception-handling-setup)
1. [Custom Bake Templates](#custom-bake-templates)
1. [Advanced Setup - Deserializing](#advanced-setup---deserializing)
1. [Advanced Examples](#advanced-examples)
1. [Contributing](#contributing)
1. [License](#license)
1. [Copyright](#copyright)


* [Serialization](/SERIALIZE_README.md)

## Basic Use Case ##

The basic concept for this plugin is to create an end to end solution for serializing
and deserializing CakePHP respones into JSON. This plugin is primarily designed around
the use of Ember and Ember Data.

So serializing a CakePHP model data array:

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

### Basic Controller Setup - Deserializing ###

The Deserializer Dispatch Filter will transform the JSON payload of an HTTP 
request from [Ember Data](http://emberjs.com/guides/models/the-rest-adapter/) 
compliant  JSON to the `Controller->request->data` property and as a standard 
CakePHP array format. No other code changes are required for basic 
deserilization to work.

### Error and Exception Handling Setup ###

Errors and Exceptions can also be handled with this plugin.

Modify your `app/Config/core.php` file to use the Custom Exceptions/Error
handling in this plugin with this code:

``` php
Configure::write('Error', array(
	'handler' => 'EmberDataError::handleError',
	'level' => E_ALL & ~E_DEPRECATED,
	'trace' => true
));

Configure::write('Exception', array(
	'handler' => 'EmberDataError::handleException',
	'renderer' => 'Serializers.EmberDataExceptionRenderer',
	'log' => true
));
```

This does two things:

* Errors and Exceptions get output as correctly formatted JSON
* Allows the use of Custom Exceptions that match Ember Data exceptions for error cases

### Custom Bake Templates ###

There are custom bake templates included in this project for baking your CakePHP 
Controller classes. Use the `serializers` template when baking a Controller, to 
generate a Controller to work with the Serializers Plugin.

## Advanced Setup - Deserializing ##

### Setup of Deserializer Class ###

To do anything advanced with deserializing data requires a custom Serializer class:

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

This is the same `Serializer` class that is also used for serializing data. 
While serializing data uses the `$required` and `$optional` properties of the 
`UserSerializer` class, deserializing does not. All data passed to the Deserializer
will be passed through to the CakePHP Controller.

### Custom Deserialize Methods ###

If you need to do any formatting or data manipulation when deserializing data,
create a method named after a field with the prefix `deserialize_`. For example:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	/**
	 * On Deserializing the data, modify the first_name value by converting to 
	 * UPPER_CASE
	 * @param  array  $data   the current deserialized data for the overall User record
	 * @param  string $record the current User record being deserialized
	 * @return multi
	 */
	public function serialize_first_name($data, $record) {
		//	$data = array(
		//		'id' => 1,
		//		'first_name' => 'Jane', 
		//		'last_name' => 'Doe',
		//		'created' => '2014-11-18 19:22:17',
		//		'modified' => '2014-11-18 19:22:17'
		//	);
		//	$record = array(
		//		'id' => 1,
		//		'first_name' => 'Jane', 
		//		'last_name' => 'Doe',
		//		'created' => '2014-11-18 19:22:17',
		//		'modified' => '2014-11-18 19:22:17'
		//	);
		return strtoupper($record['first_name']);
	}
}
```

If you want to return an attribute only in certain cases and otherwise
not include that property in the CakePHP data array, you can throw a 
`DeserializerIgnoreException` and the property will be ignored.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	/**
	 * On Deserializing the data, only return the created timestamp if the id === 
	 * @param  array  $data   the current deserialized data for the overall User record
	 * @param  string $record the current User record being deserialized
	 * @return multi
	 * @throws DeserializerIgnoreException if the User's id is not 2
	 */
	public function deserialize_created($data, $record) {
		//	$data = array(
		//		'id' => 1,
		//		'first_name' => 'Jane', 
		//		'last_name' => 'Doe',
		//		'created' => '2014-11-18 19:22:17',
		//		'modified' => '2014-11-18 19:22:17'
		//	);
		//	$record = array(
		//		'id' => 1,
		//		'first_name' => 'Jane', 
		//		'last_name' => 'Doe',
		//		'created' => '2014-11-18 19:22:17',
		//		'modified' => '2014-11-18 19:22:17'
		//	);
		if ($record['id'] === 1) {
			return $record['created'];
		}

		throw new DeserializerIgnoreException();
	}
}
```

### Custom Deserializer AfterDeserialize Callback ####

There is an afterDeserialize callback setup if you wish to do some amount of
post processing after all the data has been deserialized.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

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

Please use [GitHub Isuses](https://github.com/loadsys/CakePHP-Serializers/issues) for listing any known defects or issues.

### Development ###

When developing this plugin, please fork and issue a PR for any new development.

The Complete Test Suite for the Plugin can be run via this command:

`./lib/Cake/Console/cake test Serializers AllSerializers`

## License

[MIT](https://github.com/loadsys/CakePHP-Serializers/blob/master/LICENSE.md)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2015
