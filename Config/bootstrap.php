<?php

// Adds the Serializer object type so serializer objects
// can be found in conventional places
App::build(array(
	'Serializer' => array('%s' . 'Serializer' . DS),
	'Serializer' => array('%s' . 'Plugin' . DS . 'Serializers' . DS . 'Serializer' . DS),
), App::REGISTER);

// Load CakePHP Serializers Exceptions
App::import('Lib/Error', 'Serializers.StandardJsonApiExceptions');

// Load the EmberDataError Class
App::uses('EmberDataError', 'Serializers.Error');
