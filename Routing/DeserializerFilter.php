<?php
/**
 *
 * @package  Serializers.Routing
 */
App::uses('DispatcherFilter', 'Routing');

/**
 *
 */
class DeserializerFilter extends DispatcherFilter {

	public $priority = 9;

	public function beforeDispatch(CakeEvent $event) {
		$request = $event->data['request'];
		$response = $event->data['response'];

		if ($request->url === 'hello-world') {
			$response->body('Hello World');
			$event->stopPropagation();
			return $response;
		}
	}
}
