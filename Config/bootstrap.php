<?php

// Adds the Serializer object type so serializer objects
// can be found in conventional places
App::build(array(
	'Serializer' => array('%s' . 'Serializer' . DS),
	'Serializer' => array('%s' . 'Plugin' . DS . 'Serializers' . DS . 'Serializer' . DS),
), App::REGISTER);

// Ensure we loaded the SerializersErrors Plugin
CakePlugin::load('SerializersErrors', array('bootstrap' => true));

// Load CakePHP Serializers Exceptions
App::import('Lib/Error', 'Serializers.SerializersCustomExceptions');
