# Deserializing #

Deseerializing is the terminology we are using for turning JSON object/arrays
provided via an HTTP Response Body into CakePHP Request Data arrays.

1. [Basic Controller Setup - Deserializing](#basic-controller-setup---deserializing)
1. [Advanced Setup - Deserializing](#advanced-setup---deserializing)
  1. [Setup of Deserializer Class](#setup-of-deserializer-class)
  1. [Custom AppSerializer Class](#custom-appserializer-class)
  1. [Custom Deserialize Methods](#custom-deserialize-methods)
  1. [Custom Deserializer AfterDeserialize Callback](#custom-deserializer-afterdeserialize-callback)

# Basic Controller Setup - Deserializing #

The Deserializer Dispatch Filter will transform the JSON payload of an HTTP 
request from [Ember Data](http://emberjs.com/guides/models/the-rest-adapter/) 
compliant  JSON to the `Controller->request->data` property and as a standard 
CakePHP array format. No other code changes are required for basic 
deserilization to work.

# Advanced Setup - Deserializing #

## Setup of Deserializer Class ##

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

## Custom AppSerializer Class ##

Following the pattern of AppModel, AppController, etc in CakePHP,
you can create an AppSerializer class that can extend the base Serializer class
for custom methods that are accessible to all of your Serializer classes.

To use this you can copy the file `app/Plugin/Serializers/Serializer/AppSerializer.php`
and move to `app/Serializer/AppSerializer.php`

All future Serializer Classes can then instead follow this pattern:

``` php
// Serializer/UserSerializer.php
App::uses('AppSerializer', 'Serializer');

class UserSerializer extends AppSerializer {
}
```

All future documentation in the README will follow this pattern for consistency.

## Custom Deserialize Methods ##

If you need to do any formatting or data manipulation when deserializing data,
create a method named after a field with the prefix `deserialize_`. For example:

``` php
// Serializer/UserSerializer.php
App::uses('AppSerializer', 'Serializer');

class UserSerializer extends AppSerializer {

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
App::uses('AppSerializer', 'Serializer');

class UserSerializer extends AppSerializer {

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

## Custom Deserializer AfterDeserialize Callback ###

There is an afterDeserialize callback setup if you wish to do some amount of
post processing after all the data has been deserialized.

``` php
// Serializer/UserSerializer.php
App::uses('AppSerializer', 'Serializer');

class UserSerializer extends AppSerializer {

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
