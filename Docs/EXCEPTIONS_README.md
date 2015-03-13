# Exceptions #

There are custom exception classes used in this plugin to provide for responses
that when formated using the `EmberDataError` and `EmberDataExceptionRender` Classes,
look like:

```javascript
{
	"errors": [
		{
			"id": "1234353",
			"href": "/url/for/more/information",
			"status": 5xx,
			"code": "SomethingWentWrongException",
			"title": "Something went wrong and now this is displayed.",
			"detail": "A detailed response, could be an object/array as well",
			"current_url": "/url/attempted/to/reach"
		}
	]
}
```

The custom Exception classes are also used to return Validation Errors in a
manner that EmberData expects, like so:

```javascript
{
	"errors": {
		"name": "Name must not be empty.",
		"status": "Status? must be true or false.",
	}
}
```

This README is split into the following sections.

1. [StandardJsonApiExceptions](#standardjsonapiexceptions)
1. [UnauthorizedJsonApiException](#unauthorizedjsonapiexception)
1. [ForbiddenByPermissionsException](#forbiddenbypermissionsexception)
1. [ValidationFailedJsonApiException](#validationfailedjsonapiexception)
1. [ModelSaveFailedJsonApiException](#modelsavefailedjsonapiexception)
1. [InvalidPassedDataJsonApiException](#invalidpasseddatajsonapiexception)
1. [ModelDeleteFailedJsonApiException](#modeldeletefailedjsonapiexception)
1. [ModelDeleteFailedValidationJsonApiException](#modeldeletefailedvalidationjsonapiexception)
1. [SerializerMissingRequiredException](#serializermissingrequiredexception)
1. [SerializerIgnoreException](#serializerignoreexception)
1. [DeserializerIgnoreException](#deserializerignoreexception)

## StandardJsonApiExceptions ##

This the base Exception Class for all other Exceptions expect for `SerializerMissingRequiredException`,
`SerializerIgnoreException` and `DeserializerIgnoreException`

The construct method is updated to:

```php
__construct(
	$title = 'JSON API Exception',
	$detail = 'JSON API Exception',
	$code = 400,
	$href = null,
	$id = null
)
```

The `EmberDataExceptionRender` class will render this exception to the front end
as a JSON Array following this pattern:

```javascript
{
	"errors": [
		{
			"id": Exception->id,
			"href": Exception->href,
			"status": Exception->code,
			"code": get_class(Exception),
			"title": Exception->title,
			"detail": Exception->detail,
			"current_url": $this->controller->request->here
		}
	]
}
```

## UnauthorizedJsonApiException ##

Used when an HTTP_AUTHORIZATON header token is not set, expired, or invalid.

This Exception class is not used by the bake templates, but if you write an AUTHORIZATION
solution with your API, this Exception class is designed around dealing with AUTHORIZATION issues.

```php
__construct(
	$title = 'Unauthorized Access',
	$detail = 'Unauthorized Access',
	$code = 401,
	$href = null,
	$id = null
)
```

## ForbiddenByPermissionsException ##

Used when a User does not have permission to access that url.

This Exception class is not used by the bake templates, but if you write an ACL
solution with your API, this Exception class is designed around dealing with ACL issues.

```php
__construct(
	$title = 'Unauthorized Access',
	$detail = 'Access to the requested resource is denied by the Permissions on your account.',
	$code = 403,
	$href = null,
	$id = null
)
```

## ValidationFailedJsonApiException ##

Used when a Model save fails due to validation issues.

This Exception class is actively used by the bake templates. A sample use case:

```php
throw new ValidationFailedJsonApiException(__('ModelName create failed.'), $this->ModelName->invalidFields());
```php

```php
__construct(
	$title = 'Validation Failed',
	array $detail = array(),
	$code = 422,
	$href = null,
	$id = null
)
```

The `detail` property of this exception is required to be an array, typically the
results of `$this->Model->invalidFields()` method call.

## ModelSaveFailedJsonApiException ##

Used when a `$this->Model->save` returns false for reasons other than Validation Errors.

This Exception class is used by the bake templates.

```php
__construct(
	$title = 'Model Save Failed',
	$detail = 'Model Save Failed',
	$code = 400,
	$href = null,
	$id = null
)
```

## InvalidPassedDataJsonApiException ##

Used when the HTTP Request includes invalid data.

This Exception class is not used by the bake templates.

```php
__construct(
	$title = 'Invalid Data Passed',
	$detail = 'Invalid Data Passed',
	$code = 400,
	$href = null,
	$id = null
)
```

## ModelDeleteFailedJsonApiException ##

Used when the `$this->Model->delete` returns false.

This Exception class is used by the bake templates.

```php
__construct(
	$title = 'Model Delete Failed',
	$detail = 'Model Delete Failed',
	$code = 502,
	$href = null,
	$id = null
)
```

## ModelDeleteFailedValidationJsonApiException ##

Used when the `$this->Model->delete` fails due to a Validation issue.

This Exception class is not used by the bake template, however it is useful if
you want to fail a delete method and provide a more specific error response
targeting a validation issue, for instance a related model needs to be deleted
first. It does not provide any of the custom error response handling that the
`ValidationFailedJsonApiException` class provides.

```php
__construct(
	$title = 'Model Delete Failed Due to Validation Issue',
	$detail = 'Model Delete Failed Due to Validation Issue',
	$code = 502,
	$href = null,
	$id = null
)
```

## SerializerMissingRequiredException ##

Used when the Serializer is missing a required property.

This Exception does not currently extend the `StandardJsonApiExceptions` class, it
extends PHP's base Exception Class.

## SerializerIgnoreException ##

Used when you create a custom `serialize_{property_name}` method, if you wish
to not set any data for that property when serializing the Model. This Exception
is caught and will not stop the request from completing.

This Exception does not currently extend the `StandardJsonApiExceptions` class, it
extends PHP's base Exception Class.

## DeserializerIgnoreException ##

Used when you create a custom `deserialize_{property_name}` method, if you wish
to not set any data for that property when deserializing the Model. This Exception
is caught and will not stop the request from completing.

This Exception does not currently extend the `StandardJsonApiExceptions` class, it
extends PHP's base Exception Class.
