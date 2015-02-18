<?php
/**
 * Custom Exceptions for the CakePHP Serializers Class
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
	public function __construct($title = null, $detail = null, $code = null, $href = null, $id = null) {
		if (!empty($title)) {
			$this->title = $title;
		}

		if (!empty($detail)) {
			$this->detail = $detail;
		}

		if (!empty($code)) {
			$this->code = $code;
		}

		if (!empty($href)) {
			$this->href = $href;
		}

		if (!empty($id)) {
			$this->id = $id;
		}

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
 * Used when an HTTP_AUTHORIZATON header token is not set, expired, or invalid.
 */
class UnauthorizedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 401;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Unauthorized Access';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Unauthorized Access';
}

/**
 * Used when a User's Permissions forbid access to the requested section of
 * the app.
 */
class ForbiddenByPermissionsException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 403;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Unauthorized Access';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Access to the requested resource is denied by the Permissions on your account.';
}

/**
 * ValidationFailedJsonApiException
 *
 * a generic JSON API Exception when validation fails
 */
class ValidationFailedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 422;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Validation Failed';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Validation Failed';
}

/**
 * ModelSaveFailedJsonApiException
 *
 * a generic JSON API Exception when model save fails
 */
class ModelSaveFailedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 400;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Model Save Failed';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Model Save Failed';
}

/**
 * InvalidPassedDataJsonApiException
 *
 * a generic JSON API Exception when invalid data passed to the controller
 */
class InvalidPassedDataJsonApiException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 400;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Invalid Data Passed';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Invalid Data Passed';
}

/**
 * ModelDeleteFaildJsonApiException
 *
 * a generic JSON API Exception when the Model->delete method fails
 */
class ModelDeleteFailedJsonApiException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 502;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Model Delete Failed';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Model Delete Failed';
}

/**
 * ModelDeleteFailedValidationJsonApiException
 *
 * a generic JSON API Exception when the Model->delete method fails with a
 * validation error
 */
class ModelDeleteFailedValidationJsonApiException extends StandardJsonApiExceptions {

	/**
	 * An application-specific error code, expressed as a string value.
	 *
	 * @var int
	 */
	public $code = 502;

	/**
	 * A short, human-readable summary of the problem.
	 *
	 * @var string
	 */
	public $title = 'Model Delete Failed Due to Validation Issue';

	/**
	 * A human-readable explanation specific to this occurrence of the problem.
	 *
	 * @var string
	 */
	public $detail = 'Model Delete Failed Due to Validation Issue';
}

