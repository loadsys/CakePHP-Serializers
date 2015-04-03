<?php

// Adds the Serializer object type so serializer objects
// can be found in conventional places
App::build(array(
	'Serializer' => array('%s' . 'Serializer' . DS)
), App::REGISTER);

// Load JSON API Exceptions
App::import('Lib/Error', 'Serializers.StandardJsonApiExceptions');

// Load Serializer Exceptions
App::import('Lib/Error', 'Serializers.SerializerExceptions');

// Load the EmberDataError Class
App::uses('EmberDataError', 'Serializers.Error');
