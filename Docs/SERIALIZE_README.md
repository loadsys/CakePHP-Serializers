# Serializing #

Serializing is the terminology we are using for turning CakePHP data arrays
into JSON and rendering it to the front end.

1. [Basic Controller Setup - Serializing](#basic-controller-setup---serializing)
1. [Advanced Setup - Serializing](#advanced-setup---serializing)
  1. [Setup of Serializer Class](#setup-of-serializer-class)
  1. [Required Property of Serializer Class](#required-property-of-serializer-class)
  1. [Optional Property of Serializer Class](#optional-property-of-serializer-class)
  1. [Custom Serialize Methods](#custom-serialize-methods)
  1. [Custom Serializer AfterSerialize Callback](#custom-serializer-afterSerialize-callback)

# Basic Controller Setup - Serializing #

Set a `$viewClass` property, either globally in your `Controller/AppController.php` or in specific
controllers as needed:

```php
public $viewClass = 'Serializers.EmberDataSerializer';
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
JSON, to the standard CakePHP view variables `${controllerNamePluarlized}` or
`${controllerNameSingular}`. As an example if you have a Controller named Users,
Serializers will look for your data first at the view variable `$users`, then `$user`.
As a final fallback, Serializers will use the `$data` variable for output.

``` php
// Controller/UsersController.php

public function index() {
	$this->User->recursive = 0;
	$users = $this->paginate();
	$this->set(compact('users'));
}
```

No view files are used to render the output, simply visit:
http://path/to/cake/app/{controller-name}/{method-name}
And you should see your data rendered as JSON.

This is the minimal use case for serializing data from your CakePHP Controller to
JSON at the view layer.

# Advanced Setup - Serializing #

## Setup of Serializer Class ##

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

## Required Property of Serializer Class ##

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

This Serializer will throw a `SerializerMissingRequiredException` if the data passed
to the Serializer does not include all of the required properties.

All other fields (in this example `created` and `modified`) will be suppressed
when rendering the JSON.

## Optional Property of Serializer Class ##

If you have attributes that you wish to have as optional attributes, ie. attributes
that are passed to the output only when provided to the Serializer, you can setup
an array of optional properties.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	public $required = array(
		'id',
		'first_name',
		'last_name'
	);

	public $optional = array(
		'created'
	);
}
```

If `created` is passed to the Serializer, then `created` will be Serialized
and passed to the output.

## Custom Serialize Methods ##

If you need to do any formatting or data manipulation when serializing data,
create a method named after a field with the prefix `serialize_`. For example:

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	public $required = array(
		'id',
		'first_name',
		'last_name'
	);

	/**
	 * On Serializing the data, modify the first_name value by converting to
	 * UPPER_CASE
	 * @param  array  $data   the data for the overall User record being serialized
	 * @param  string $record the single value for the property being serialized
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
		//	$record = "Doe";
		return strtoupper($record);
	}
}
```

If you want to return an optional attribute only in certain cases and otherwise
not include that property in the JSON array, you can throw a `SerializerIgnoreException`
and the optional property will be ignored.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	public $required = array(
		'id',
		'first_name',
		'last_name'
	);

	public $optional = array(
		'created'
	);

	/**
	 * On Serializing the data, modify the first_name value by converting to
	 * UPPER_CASE
	 * @param  array  $data   the data for the overall User record being serialized
	 * @param  string $record the single value for the property being serialized
	 * @return multi
	 * @throws SerializerIgnoreException if the User's id is not 2
	 */
	public function serialize_created($data, $record) {
		//	$data = array(
		//		'id' => 1,
		//		'first_name' => 'Jane',
		//		'last_name' => 'Doe',
		//		'created' => '2014-11-18 19:22:17',
		//		'modified' => '2014-11-18 19:22:17'
		//	);
		//	$record = "2014-11-18 19:22:17";
		if ($data['id'] === 2) {
			return $record;
		}

		throw new SerializerIgnoreException();
	}
}
```

If you throw this Exception on a required attribute you will still have a `SerializerMissingRequiredException`
thrown as you are now missing a required attribute.

## Custom Serializer AfterSerialize Callback ###

There is an `afterSerialize` callback if you wish to do some amount of
post processing after all the data has been serialized.

``` php
// Serializer/UserSerializer.php
App::uses('Serializer', 'Serializers.Serializer');

class UserSerializer extends Serializer {

	public $required = array(
		'id',
		'first_name',
		'last_name'
	);

	public $optional = array(
		'created'
	);

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
