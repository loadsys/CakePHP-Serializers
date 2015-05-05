<?php
/**
 * Tests the StandardJsonApiExceptions Classes to ensure it matches the expected
 * format
 *
 * @package Serializers.Test.Case.Lib.Error
 */
App::uses('Lib/Error', 'Serializers.StandardJsonApiExceptions');

/**
 * StandardJsonApiExceptionsTest
 */
class StandardJsonApiExceptionsTest extends CakeTestCase {

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testNotFoundJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new NotFoundJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('NotFoundJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testUnauthorizedJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new UnauthorizedJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('UnauthorizedJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testForbiddenByPermissionsExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new ForbiddenByPermissionsException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('ForbiddenByPermissionsException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testValidationFailedJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = array("something" => "Custom detail message");
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new ValidationFailedJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('ValidationFailedJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"The exception `detail` property does not match what we passed"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testModelSaveFailedJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new ModelSaveFailedJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('ModelSaveFailedJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testInvalidPassedDataJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new InvalidPassedDataJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('InvalidPassedDataJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testModelDeleteFailedJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new ModelDeleteFailedJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('ModelDeleteFailedJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

	/**
	 * Confirm that the construct sets our values properly
	 *
	 * @return void
	 */
	public function testModelDeleteFailedValidationJsonApiExceptionConstructor() {
		$title = "New Title";
		$detail = "Custom detail message";
		$status = 406;
		$id = "13242134-456657-asdfasdf";
		$href = 'https://www.asdfasdfasdf.com/';
		$links = array('link' => 'link');
		$paths = array('something' => 'something');

		$exception = new ModelDeleteFailedValidationJsonApiException(
			$title,
			$detail,
			$status,
			$id,
			$href,
			$links,
			$paths
		);

		$this->assertInstanceOf('ModelDeleteFailedValidationJsonApiException', $exception);
		$this->assertInstanceOf('BaseSerializerException', $exception);
		$this->assertInstanceOf('CakeException', $exception);

		$this->assertEquals(
			$title,
			$exception->title,
			"Title does not match {$title}"
		);
		$this->assertEquals(
			$detail,
			$exception->detail,
			"Detail does not match {$detail}"
		);
		$this->assertEquals(
			$status,
			$exception->status,
			"Status does not match {$status}"
		);
		$this->assertEquals(
			$id,
			$exception->id,
			"Id does not match {$id}"
		);
		$this->assertEquals(
			$href,
			$exception->href,
			"Href does not match {$href}"
		);
		$this->assertEquals(
			$links,
			$exception->links,
			"Links does not match our expectation"
		);
		$this->assertEquals(
			$paths,
			$exception->paths,
			"Paths does not match expectation"
		);
	}

}
