<?php
/**
 * Custom Exceptions for the CakePHP Serializer Class
 */

/**
 * Custom exception when the Serializer is missing a required attribute
 */
class SerializerMissingRequiredException extends Exception {
}

/**
 * Custom exception when the Serializer is set to ignore an attribute
 */
class SerializerIgnoreException extends Exception {
}

/**
 * Custom exception when the Deserializer is set to ignore an attribute
 */
class DeserializerIgnoreException extends Exception {
}
