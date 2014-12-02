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
App::uses('Hash', 'Utility');

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
		$data = $request->input('json_decode', true);

		if (empty($data)) {
			$data = array();
		}
		$deserializedData = array();

		foreach ($data as $key => $dataForKey) {
			$classifiedRootKey = Inflector::classify($key);
			$Serialization = new Serialization($classifiedRootKey, $data);
			$dataForKey = $Serialization->deserialize();
			$deserializedData[$classifiedRootKey] = $dataForKey;
		}

		$request->data = Hash::merge($request->data, $deserializedData);
	}
}
