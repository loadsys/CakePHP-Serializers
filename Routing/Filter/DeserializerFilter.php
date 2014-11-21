<?php
/**
 * DeserializerFilter - request filter to deserialize the json payload
 * of a request before being passed onto the controller
 *
 * @package  Serializers.Routing/Filter
 */
App::uses('DispatcherFilter', 'Routing');
App::uses('Serialization', 'Serializers.Lib');
App::uses('Inflector', 'Utility');

/**
 * DeserializerFilter
 */
class DeserializerFilter extends DispatcherFilter {

	/**
	 * Method called before the controller is instantiated and called to serve a
	 * request. If used with default priority, it will be called after the Router
	 * has parsed the URL and set the routing params into the request object.
	 *
	 * We will process the request for the json data and de-serialize it
	 *
	 * @param  CakeEvent $event the CakeEvent being triggered
	 * @return void
	 */
	public function beforeDispatch(CakeEvent $event) {
		// get the request data
		$request = $event->data['request'];
		$data = $request->input('json_decode');

		if (empty($data)) {
			$data = array();
		}

		$data = $this->objectToArray($data);
		$deserializedData = array();

		foreach ($data as $key => $dataForKey) {
			$classifiedRootKey = Inflector::classify($key);
			$Serialization = new Serialization($classifiedRootKey, $data);
			$dataForKey = $Serialization->deserialize();
			$deserializedData[$classifiedRootKey] = $dataForKey;
		}

		$request->data = $deserializedData;
	}

	/**
	 * converts an object to an array
	 *
	 * @param  object $obj the object to convert
	 * @return array
	 */
	private function objectToArray($obj) {
		if (is_object($obj)) {
			$obj = (array)$obj;
		}

		if (is_array($obj)) {
			$new = array();
			foreach ($obj as $key => $val) {
				$new[$key] = $this->objectToArray($val);
			}
		} else {
			$new = $obj;
		}

		return $new;
	}
}
