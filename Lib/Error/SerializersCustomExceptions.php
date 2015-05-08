<?php
/**
 * Custom Exceptions for the CakePHP Serializers Object
 *
 * @package Serializers.Lib.Error
 */
App::uses('BaseSerializerException', 'SerializersErrors.Error');

/**
 * Used when an Not Found Exception occurs
 */
class NotFoundJsonApiException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base NotFoundJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Resource Not Found',
		$detail = 'Resource Not Found',
		$status = 404,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * Used when an HTTP_AUTHORIZATON header token is not set, expired, or invalid.
 */
class UnauthorizedJsonApiException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base UnauthorizedJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Unauthorized Access',
		$status = 401,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * Used when a User's Permissions forbid access to the requested section of
 * the app.
 */
class ForbiddenByPermissionsException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base ForbiddenByPermissionsException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Access to the requested resource is denied by the Permissions on your account.',
		$status = 403,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * ValidationFailedJsonApiException
 *
 * a generic JSON API Exception when validation fails
 */
class ValidationFailedJsonApiException extends ValidationBaseSerializerException {

	/**
	 * Constructs a new instance of the base ValidationFailedJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param array $validationErrors A CakePHP Model array of validation errors
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Validation Failed',
		array $validationErrors = array(),
		$status = 422,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $validationErrors, $status, $id, $href, $links, $paths);
	}

}

/**
 * ModelSaveFailedJsonApiException
 *
 * a generic JSON API Exception when model save fails
 */
class ModelSaveFailedJsonApiException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base ModelSaveFailedJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Model Save Failed',
		$detail = 'Model Save Failed',
		$status = 400,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * InvalidPassedDataJsonApiException
 *
 * a generic JSON API Exception when invalid data passed to the controller
 */
class InvalidPassedDataJsonApiException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base InvalidPassedDataJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Invalid Data Passed',
		$detail = 'Invalid Data Passed',
		$status = 400,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * ModelDeleteFaildJsonApiException
 *
 * a generic JSON API Exception when the Model->delete method fails
 */
class ModelDeleteFailedJsonApiException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base ModelDeleteFailedJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Model Delete Failed',
		$detail = 'Model Delete Failed',
		$status = 502,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * ModelDeleteFailedValidationJsonApiException
 *
 * a generic JSON API Exception when the Model->delete method fails with a
 * validation error
 */
class ModelDeleteFailedValidationJsonApiException extends ValidationBaseSerializerException {

	/**
	 * Constructs a new instance of the base ModelDeleteFailedValidationJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param array $validationErrors A CakePHP Model array of validation errors
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Model Delete Failed Due to Validation Issue',
		array $validationErrors = array(),
		$status = 502,
		$id = null,
		$href = null,
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $validationErrors, $status, $id, $href, $links, $paths);
	}

}

/**
 * SerializerMissingRequiredException
 *
 * Custom exception when the Serializer is missing a required attribute
 */
class SerializerMissingRequiredException extends BaseSerializerException {

	/**
	 * Constructs a new instance of the base ModelDeleteFailedValidationJsonApiException
	 *
	 * @param string $title The title of the exception, passed to parent CakeException::__construct
	 * @param string $detail A human-readable explanation specific to this occurrence of the problem.
	 * @param int $status The http status code of the error, passed to parent CakeException::__construct
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param array $links An array of JSON Pointers [RFC6901] to the associated resource(s) within the request document [e.g. ["/data"] for a primary data object].
	 * @param array $paths An array of JSON Pointers to the relevant attribute(s) within the associated resource(s) in the request document. Each path MUST be relative to the resource path(s) expressed in the error object's "links" member [e.g. ["/first-name", "/last-name"] to reference a couple attributes].
	 */
	public function __construct(
		$title = 'Serializer Is Missing A Required Attribute',
		$detail = 'Serialization of a Data Object is missing a required property and failed.',
		$status = 500,
		$id = null,
		$href = "https://github.com/loadsys/CakePHP-Serializers/blob/master/Docs/EXCEPTIONS_README.md#serializermissingrequiredexception",
		$links = array(),
		$paths = array()
	) {
		parent::__construct($title, $detail, $status, $id, $href, $links, $paths);
	}

}

/**
 * SerializerIgnoreException
 *
 * Custom exception when the Serializer is set to ignore an attribute
 */
class SerializerIgnoreException extends Exception {
}

/**
 * DeserializerIgnoreException
 *
 * Custom exception when the Deserializer is set to ignore an attribute
 */
class DeserializerIgnoreException extends Exception {
}
