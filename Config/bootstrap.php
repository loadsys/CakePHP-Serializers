<?php

// Adds the Serializer object type so serializer objects
// can be found in conventional places
App::build(array(
	'Serializer' => array('%s' . 'Serializer' . DS)
), App::REGISTER);

