<?php
/**
 * Custom view class for rending serialized data in the EmberDataFormat
 *
 * @package  Serializers.View
 */
App::uses('CakeSerializerView', 'Serializers.View');
App::uses('Serialization', 'Serializers.Lib');
App::uses('Inflector', 'Utility');

/**
 * EmberDataSerializerView
 */
class EmberDataSerializerView extends CakeSerializerView {

	/**
	 * converts view data to serialized data, adds some customizations to handle
	 * EmberData
	 *
	 * @param  string $name the model name to serialize
	 * @param  array  $data the data to serialize
	 * @return array
	 */
	protected function toJSON($name, $data) {
		$Serialization = new Serialization($name, $data);
		$serializedData = $Serialization->serialize();

		// if the serialized data is empty, simply json_encode and return it
		if (empty($serializedData)) {
			return json_encode($serializedData);
		}

		// get the top level key from the serialized data and build up the
		// inflected versions of said key
		$arrayKeys = array_keys($serializedData);
		$arrayTopLevelKey = $arrayKeys[0];
		$pluralTopLevelKey = Inflector::tableize($arrayTopLevelKey);
		$singularTopLevelKey = Inflector::singularize(Inflector::tableize($arrayTopLevelKey));

		// if the request is an index view, and the key of the serialized data
		// is the singular version of the key, we need to convert to a
		// pluralized version, for the ember data formats
		if (
			($this->request->action === "index")
			&& array_key_exists($singularTopLevelKey, $serializedData)
		) {
			$serializedData[$pluralTopLevelKey][0] = $serializedData[$singularTopLevelKey];
			unset($serializedData[$singularTopLevelKey]);
		}

		return json_encode($serializedData);
	}

}
