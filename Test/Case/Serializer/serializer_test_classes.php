<?php
/**
 * Test Serialize Classes used in the various test files for testing the core
 * Serialization functionality, extracted to reduce duplication
 */

/**
 * TestRootKeySerializer
 */
class TestRootKeySerializer extends Serializer {
}

/**
 * TestRootKeySerializer
 */
class TestChangedRootKeySerializer extends Serializer {

	/**
	 * the root key, enables testing that the Serializer works correctly if the
	 * property is changed
	 *
	 * @var string
	 */
	public $rootKey = "changed-root-key";
}

/**
 * TestUserSerializer
 */
class TestUserSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('first_name', 'last_name');
}

/**
 * TestSecondLevelUserSerializer
 */
class TestSecondLevelUserSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('first_name', 'last_name');
}

/**
 * TestSecondLevelUserWithMethodSerializer
 */
class TestSecondLevelUserWithMethodSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('first_name', 'last_name');

	/**
	 * deserialize the first name
	 *
	 * @param array $data
	 * @param array $record
	 * @return string
	 */
	public function deserialize_first_name($data, $record) {
		return 'FIRST';
	}

	/**
	 * serialize the first name
	 *
	 * @param array $data
	 * @param array $record
	 * @return string
	 */
	public function serialize_first_name($data, $record) {
		return 'FIRST';
	}
}

/**
 * TestSecondLevelDifferentClassSerializer
 */
class TestSecondLevelDifferentClassSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('id', 'name');

}

/**
 * TestCallbackSerializer
 */
class TestCallbackSerializer extends Serializer {
	public function afterSerialize($serializedData, $unserializedData) {
		return "after serialize";
	}

	public function afterDeserialize($deserializedData, $serializedData) {
		return "after deserialize";
	}
}

/**
 * TestBadOptionalSerializer
 */
class TestBadOptionalSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('title', 'body');
	public $optional = 'notanarray';
}

/**
 * TestOptionalSerializer
 */
class TestOptionalSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('title', 'body');

	/**
	 * optional properties
	 *
	 * @var array
	 */
	public $optional = array('summary', 'published');

	public function serialize_body($data, $record) {
		return 'BODY';
	}

	public function serialize_summary($data, $record) {
		return 'SUMMARY';
	}

	public function deserialize_body($data, $record) {
		return 'BODY';
	}

	public function deserialize_summary($data, $record) {
		return 'SUMMARY';
	}
}

/**
 * TestMethodSubSerializeSerializer
 */
class TestMethodSubSerializeSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('title', 'body');

	/**
	 * optional properties
	 *
	 * @var array
	 */
	public $optional = array('summary', 'published', 'tags', 'created');

	public function serialize_tests($data, $record) {
		return array(
			'tests' => $record,
		);
	}

	public function serialize_UpperCaseTest($data, $record) {
		return array(
			'upper_case_tests' => $record,
		);
	}

}

/**
 * TestMethodOptionalSerializer
 */
class TestMethodOptionalSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('title', 'body');

	/**
	 * optional properties
	 *
	 * @var array
	 */
	public $optional = array('summary', 'published', 'tags', 'created');

	public function serialize_tags($data, $record) {
		return 'Tags';
	}
}

class TestIgnoreSerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('title', 'body');

	/**
	 * optional properties
	 *
	 * @var array
	 */
	public $optional = array('created');

	/**
	 * serialize created field
	 *
	 * @param array $data
	 * @param array $record
	 * @return array
	 * @throws SerializerIgnoreException
	 */
	public function serialize_created($data, $record) {
		throw new SerializerIgnoreException();
	}

	/**
	 * deserialize created field
	 *
	 * @param array $data
	 * @param array $record
	 * @return array
	 * @throws DeserializerIgnoreException
	 */
	public function deserialize_created($data, $record) {
		throw new DeserializerIgnoreException();
	}

}

/**
 * TestPrimarySerializer
 */
class TestPrimarySerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('id', 'name');
}

/**
 * TestSubSecondarySerializer
 */
class TestSubSecondarySerializer extends Serializer {

	/**
	 * required properties
	 *
	 * @var array
	 */
	public $required = array('test_field');
}
