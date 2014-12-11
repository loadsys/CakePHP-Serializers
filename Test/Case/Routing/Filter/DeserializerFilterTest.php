<?php
App::uses('DeserializerFilter', 'Serializers.Routing/Filter');
App::uses('Serializer', 'Serializers.Serializer');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeEvent', 'Event');

class TestDispatchUserSerializer extends Serializer {

	public $required = array(
		'first_name',
		'last_name'
	);
}

/**
 * DeserializerFilterTest
 */
class DeserializerFilterTest extends CakeTestCase {

	/**
	 * test no modification to the request POST data when nothing serialized
	 *
	 * @return void
	 */
	public function testNoModificationOfPostRequestDataWithNoRequestBody() {
		$inputPostData = array(
			'data' => array(
				'Testing' => array(
					'id' => 'stuff',
				),
			),
		);
		$jsonDecodeResponse = null;
		$expectedData = array(
			'Testing' => array(
				'id' => 'stuff',
			),
		);
		$_POST = $inputPostData;
		$filter = new DeserializerFilter();
		$response = $this->getMock('CakeResponse', array('_sendHeader'));
		$request = $this->getMock('CakeRequest', array('input'));

		$request->expects($this->once())
			->method('input')
			->with('json_decode', true)
			->will($this->returnValue($jsonDecodeResponse));

		$event = new CakeEvent('DeserializerFilterTest', $this, compact('request', 'response'));

		$this->assertNull(
			$filter->beforeDispatch($event),
			'No action should be taken, nothing returned'
		);

		$request = $event->data['request'];
		$this->assertSame(
			$request->data,
			$expectedData,
			'Request Data should be unmodified as Serializers Not Used'
		);
	}

	/**
	 * test no modification to the request GET data when nothing serialized
	 *
	 * @return void
	 */
	public function testNoModificationOfGetRequestDataWithNoRequestBody() {
		$inputGetData = array(
			'Testing' => array(
				'id' => 'stuff',
			),
		);
		$jsonDecodeResponse = null;
		$expectedQueryData = array(
			'Testing' => array(
				'id' => 'stuff',
			),
		);
		$_GET = $inputGetData;
		$filter = new DeserializerFilter();
		$response = $this->getMock('CakeResponse', array('_sendHeader'));
		$request = $this->getMock('CakeRequest', array('input'));

		$request->expects($this->once())
			->method('input')
			->with('json_decode', true)
			->will($this->returnValue($jsonDecodeResponse));

		$event = new CakeEvent('DeserializerFilterTest', $this, compact('request', 'response'));

		$this->assertNull(
			$filter->beforeDispatch($event),
			'No action should be taken, nothing returned'
		);

		$request = $event->data['request'];
		$this->assertSame(
			$request->query,
			$expectedQueryData,
			'Request Data should be unmodified as Serializers Not Used'
		);
	}

	/**
	 * test no modification to the request POST data when nothing serialized
	 *
	 * @return void
	 */
	public function testModificationOfPostRequestDataWithRequestBody() {
		$inputPostData = array(
			'data' => array(
				'Testing' => array(
					'id' => 'stuff',
				),
			),
		);
		$jsonDecodeResponse = array(
			'test_dispatch_user' => array(
				'id' => 'testing',
				'first_name' => 'First',
				'last_name' => 'Second',
			),
		);

		$expectedData = array(
			'Testing' => array(
				'id' => 'stuff',
			),
			'TestDispatchUser' => array(
				'id' => 'testing',
				'first_name' => 'First',
				'last_name' => 'Second',
			),
		);

		$_POST = $inputPostData;
		$filter = new DeserializerFilter();
		$response = $this->getMock('CakeResponse', array('_sendHeader'));
		$request = $this->getMock('CakeRequest', array('input'));

		$request->expects($this->once())
			->method('input')
			->with('json_decode', true)
			->will($this->returnValue($jsonDecodeResponse));

		$event = new CakeEvent('DeserializerFilterTest', $this, compact('request', 'response'));

		$this->assertNull(
			$filter->beforeDispatch($event),
			'No action should be taken, nothing returned'
		);

		$request = $event->data['request'];
		$this->assertSame(
			$request->data,
			$expectedData,
			'POST Request Data should be include both the set POST data and the additional Request body'
		);
	}

	/**
	 * test no modification to the request GET data when nothing serialized
	 *
	 * @return void
	 */
	public function testNoModificationOfGetRequestDataWithRequestBody() {
		$inputGetData = array(
			'Testing' => array(
				'id' => 'stuff',
			),
		);
		$jsonDecodeResponse = array(
			'test_dispatch_user' => array(
				'id' => 'testing',
				'first_name' => 'First',
				'last_name' => 'Second',
			),
		);

		$expectedQueryData = array(
			'Testing' => array(
				'id' => 'stuff',
			),
		);
		$expectedPost = array(
			'TestDispatchUser' => array(
				'id' => 'testing',
				'first_name' => 'First',
				'last_name' => 'Second',
			),
		);

		$_GET = $inputGetData;
		$filter = new DeserializerFilter();
		$response = $this->getMock('CakeResponse', array('_sendHeader'));
		$request = $this->getMock('CakeRequest', array('input'));

		$request->expects($this->once())
			->method('input')
			->with('json_decode', true)
			->will($this->returnValue($jsonDecodeResponse));

		$event = new CakeEvent('DeserializerFilterTest', $this, compact('request', 'response'));

		$this->assertNull(
			$filter->beforeDispatch($event),
			'No action should be taken, nothing returned'
		);

		$request = $event->data['request'];
		$this->assertSame(
			$request->query,
			$expectedQueryData,
			'GET data should match the input'
		);

		$this->assertSame(
			$request->data,
			$expectedPost,
			'POST Data should be updated to match the provided request body'
		);
	}

}
