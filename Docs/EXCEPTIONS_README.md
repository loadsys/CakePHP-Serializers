# Exceptions #

There are custom exception classes used in this plugin to provide for responses
that when formated using the `SerializersErrors` Plugin, look like:


* For JSON API Requests, identified using 
`Accepts: application/vnd.api+json`
```javascript
{
	"errors": [
		{
			"id": "1234353",
			"href": "/url/for/more/information",
			"status": 5xx,
			"code": "5xx",
			"title": "Something went wrong and now this is displayed.",
			"detail": "A detailed response, could be an object/array as well",
			"links": "",
			"paths": ""
		}
	]
}
```

* For JSON Requests, identified using 
`Accepts: application/json`
```javascript
{
    "id": "1234353",
    "href": "/url/for/more/information",
    "status": "404",
    "code": "5xx",
    "detail": "Something went wrong and now this is displayed.",
    "links": "",
    "paths": ""
}
```

* For all other, render as Standard HTML exceptions.

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

1. [NotFoundJsonApiException](#notfoundjsonapiexception)
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

## NotFoundJsonApiException

Used when a resource can not be found for an endpoint.

This Exception class is used by the bake templates, in place of the standard
NotFoundException.

```php
__construct(
	$title = 'Resource Not Found',
	$detail = 'Resource Not Found',
	$status = 404,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## UnauthorizedJsonApiException

Used when an HTTP_AUTHORIZATON header token is not set, expired, or invalid.

This Exception class is not used by the bake templates, but if you write an AUTHORIZATION
solution with your API, this Exception class is designed around dealing with AUTHORIZATION issues.

```php
__construct(
	$title = 'Unauthorized Access',
	$detail = 'Unauthorized Access',
	$status = 401,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## ForbiddenByPermissionsException

Used when a User does not have permission to access that url.

This Exception class is not used by the bake templates, but if you write an ACL
solution with your API, this Exception class is designed around dealing with ACL issues.

```php
__construct(
	$title = 'Unauthorized Access',
	$detail = 'Access to the requested resource is denied by the Permissions on your account.',
	$status = 403,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## ValidationFailedJsonApiException

Used when a Model save fails due to validation issues.

This Exception class is actively used by the bake templates. A sample use case:

```php
throw new ValidationFailedJsonApiException(__('ModelName create failed.'), $this->ModelName->invalidFields());
```

```php
__construct(
	$title = 'Validation Failed',
	array $detail = array(),
	$status = 422,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

The `detail` property of this exception is required to be an array, typically the
results of `$this->Model->invalidFields()` method call.

## ModelSaveFailedJsonApiException

Used when a `$this->Model->save` returns false for reasons other than Validation Errors.

This Exception class is used by the bake templates.

```php
__construct(
	$title = 'Model Save Failed',
	$detail = 'Model Save Failed',
	$status = 400,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## InvalidPassedDataJsonApiException

Used when the HTTP Request includes invalid data.

This Exception class is not used by the bake templates.

```php
__construct(
	$title = 'Invalid Data Passed',
	$detail = 'Invalid Data Passed',
	$status = 400,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## ModelDeleteFailedJsonApiException

Used when the `$this->Model->delete` returns false.

This Exception class is used by the bake templates.

```php
__construct(
	$title = 'Model Delete Failed',
	$detail = 'Model Delete Failed',
	$status = 502,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## ModelDeleteFailedValidationJsonApiException

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
	$status = 502,
	$id = null,
	$href = null,
	$links = null,
	$paths = null
)
```

## SerializerMissingRequiredException

Used when the Serializer is missing a required property.

This Exception does not currently extend the `StandardJsonApiExceptions` class, it
extends PHP's base Exception Class.

## SerializerIgnoreException

Used when you create a custom `serialize_{property_name}` method, if you wish
to not set any data for that property when serializing the Model. This Exception
is caught and will not stop the request from completing.

This Exception does not currently extend the `StandardJsonApiExceptions` class, it
extends PHP's base Exception Class.

## DeserializerIgnoreException

Used when you create a custom `deserialize_{property_name}` method, if you wish
to not set any data for that property when deserializing the Model. This Exception
is caught and will not stop the request from completing.

This Exception does not currently extend the `StandardJsonApiExceptions` class, it
extends PHP's base Exception Class.
