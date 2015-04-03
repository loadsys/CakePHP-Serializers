<?php
/**
 * Custom Exceptions for CakePHP Controllers that use Serializers, designed
 * to provide feedback via standard JSON payloads
 */

/**
 * StandardJsonApiExceptions
 *
 * a generic JSON API Exception
 */
class StandardJsonApiExceptions extends CakeException {

	/**
	 * A short, human-readable summary of the problem. It SHOULD NOT change from
	 * occurrence to occurrence of the problem, except for purposes of
	 * localization.
	 *
	 * @var null
	 */
	public $title = 'JSON API Exception';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var null
	 */
	public $detail = 'JSON API Exception';

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var null
	 */
	public $code = 400;

	/**
	 * A URI that MAY yield further details about this particular occurrence
	 * of the problem.
	 *
	 * @var null
	 */
	public $href = null;

	/**
	 * A unique identifier for this particular occurrence of the problem.
	 *
	 * @var null
	 */
	public $id = null;

	/**
	 * The HTTP status code applicable to this problem, expressed as a string
	 * value.
	 *
	 * @var null
	 */
	public $status = null;

	/**
	 * Associated resources which can be dereferenced from the request document.
	 *
	 * @var null
	 */
	public $links = null;

	/**
	 * The relative path to the relevant attribute within the associated
	 * resource(s). Only appropriate for problems that apply to a single
	 * resource or type of resource.
	 *
	 * @var null
	 */
	public $path = null;

	/**
	 * Constructs a new instance of the base JsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'JSON API Exception',
		$detail = 'JSON API Exception',
		$code = 400,
		$href = null,
		$id = null
	) {

		// Set the passed in properties to the properties of the Object
		$this->title = $title;
		$this->detail = $detail;
		$this->code = $code;
		$this->href = $href;
		$this->id = $id;

		parent::__construct($this->title, $code);
	}

	/**
	 * return the title for this Exception
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * return the detail for this Exception
	 *
	 * @return string
	 */
	public function getDetail() {
		return $this->detail;
	}

	/**
	 * return the href for this Exception
	 *
	 * @return string
	 */
	public function getHref() {
		return $this->href;
	}

	/**
	 * return the id for this Exception
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

}

/**
 * Used when an Not Found Exception occurs
 */
class NotFoundJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the NotFoundJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Resource Not Found',
		$detail = 'Resource Not Found',
		$code = 404,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * Used when an HTTP_AUTHORIZATON header token is not set, expired, or invalid.
 */
class UnauthorizedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the UnauthorizedJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Unauthorized Access',
		$code = 401,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * Used when a User's Permissions forbid access to the requested section of
 * the app.
 */
class ForbiddenByPermissionsException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the ForbiddenByPermissionsException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Unauthorized Access',
		$detail = 'Access to the requested resource is denied by the Permissions on your account.',
		$code = 403,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * ValidationFailedJsonApiException
 *
 * a generic JSON API Exception when validation fails
 */
class ValidationFailedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the ValidationFailedJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Validation Failed',
		array $detail = array(),
		$code = 422,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * ModelSaveFailedJsonApiException
 *
 * a generic JSON API Exception when model save fails
 */
class ModelSaveFailedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the ModelSaveFailedJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Model Save Failed',
		$detail = 'Model Save Failed',
		$code = 400,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * InvalidPassedDataJsonApiException
 *
 * a generic JSON API Exception when invalid data passed to the controller
 */
class InvalidPassedDataJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the InvalidPassedDataJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Invalid Data Passed',
		$detail = 'Invalid Data Passed',
		$code = 400,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * ModelDeleteFaildJsonApiException
 *
 * a generic JSON API Exception when the Model->delete method fails
 */
class ModelDeleteFailedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the ModelDeleteFailedJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Model Delete Failed',
		$detail = 'Model Delete Failed',
		$code = 502,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

/**
 * ModelDeleteFailedValidationJsonApiException
 *
 * a generic JSON API Exception when the Model->delete method fails with a
 * validation error
 */
class ModelDeleteFailedValidationJsonApiException extends StandardJsonApiExceptions {

	/**
	 * Constructs a new instance of the ModelDeleteFailedJsonApiException
	 *
	 * @param string $title The title of the exception.
	 * @param string $detail A detailed human readable message.
	 * @param int $code The http status code of the error.
	 * @param string $href A URI that MAY yield further details about this particular occurrence of the problem.
	 * @param string $id A unique identifier for this particular occurrence of the problem.
	 */
	public function __construct(
		$title = 'Model Delete Failed Due to Validation Issue',
		$detail = 'Model Delete Failed Due to Validation Issue',
		$code = 502,
		$href = null,
		$id = null
	) {
		parent::__construct($title, $detail, $code, $href, $id);
	}
}

