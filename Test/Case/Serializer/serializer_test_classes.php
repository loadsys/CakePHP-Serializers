<?php

class TestRootKeySerializer extends Serializer {}

class TestUserSerializer extends Serializer {
	public $required = array('first_name', 'last_name');
}

class TestSecondLevelUserSerializer extends Serializer {
	public $required = array('first_name', 'last_name');
}

class TestSecondLevelUserWithMethodSerializer extends Serializer {
	public $required = array('first_name', 'last_name');

	public function deserialize_first_name($data, $record) {
		return 'FIRST';
	}

	public function serialize_first_name($data, $record) {
		return 'FIRST';
	}
}

class TestSecondLevelDifferentClassSerializer extends Serializer {
	public $required = array('id', 'name');
}

class TestCallbackSerializer extends Serializer {
	public function afterSerialize($serializedData, $unserializedData) {
		return "after serialize";
	}

	public function afterDeserialize($deserializedData, $serializedData) {
		return "after deserialize";
	}
}

class TestBadOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = 'notanarray';
}

class TestOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
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

class TestMethodSubSerializeSerializer extends Serializer {
	public $required = array('title', 'body');
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

class TestMethodOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('summary', 'published', 'tags', 'created');

	public function serialize_tags($data, $record) {
		return 'Tags';
	}
}

class TestIgnoreSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('created');

	public function serialize_created($data, $record) {
		throw new SerializerIgnoreException();
	}

	public function deserialize_created($data, $record) {
		throw new DeserializerIgnoreException();
	}
}

class TestPrimarySerializer extends Serializer {
	public $required = array('id', 'name');
}

class TestSubSecondarySerializer extends Serializer {
	public $required = array('test_field');
}
