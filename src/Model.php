<?php
namespace PhpMvc;

/**
 * Represents methods for annotating a models.
 */
final class Model {

    /**
     * Marks the specified property with the attribute "requred" 
     * and it is expected that the property value must be specified.
     * 
     * @param string $actionName Action name.
     * @param string|array $propertyName Property name or names of properties to mark.
     * It is possible to specify both the property names and the associative array. 
     * In the array, the key is the property's name, and the value contains an error message.
     * 
     * @return void
     */
    public static function required($actionName, ...$propertyName) {
        if ($actionName != PHPMVC_ACTION) {
            return;
        }

        if (empty($actionName)) {
            throw new \Exception('$actionName cannot be empty.');
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName cannot be empty.');
        }

        foreach ($propertyName as $item) {
            $errorMessage = null;

            if (is_array($item)) {
                $key = key($item);
                $errorMessage = $item[$key];
            } else {
                $key = $item;
            }

            self::makeDataAnnotation($key);

            ViewContext::$modelDataAnnotations[$key]->required = array($errorMessage);
        }
    }

    /**
     * Sets a property whose value needs to be compared to the value of the specified property.
     * 
     * @param string $actionName Action name.
     * @param string $propertyName The property name.
     * @param string $compareWith The name of the property with which to compare.
     * @param string $errorMessage The error message.
     * 
     * @return void
     */
    public static function compare($actionName, $propertyName, $compareWith, $errorMessage = null) {
        if ($actionName != PHPMVC_ACTION) {
            return;
        }

        if (empty($actionName)) {
            throw new \Exception('$actionName cannot be empty.');
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName cannot be empty.');
        }

        if (empty($compareWith)) {
            throw new \Exception('$compareWith cannot be empty.');
        }

        if ($propertyName === $compareWith) {
            throw new \Exception('The property names must be different.');
        }

        self::makeDataAnnotation($propertyName);

        ViewContext::$modelDataAnnotations[$propertyName]->compareWith = array($compareWith, $errorMessage);
    }

    /**
     * Specifies the minimum and maximum length of characters that are allowed in a data field.
     * 
     * @param string $actionName Action name.
     * @param string $propertyName The property name to check.
     * @param int $maxLength The maximum length of a string.
     * @param int $minLength The minimum length of a string.
     * @param string The error message.
     * 
     * @return void
     */
    public static function stringLength($actionName, $propertyName, $maxLength, $minLength = null, $errorMessage = null) {
        if ($actionName != PHPMVC_ACTION) {
            return;
        }

        if (empty($actionName)) {
            throw new \Exception('$actionName cannot be empty.');
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName cannot be empty.');
        }

        self::makeDataAnnotation($propertyName);

        ViewContext::$modelDataAnnotations[$propertyName]->stringLength = array($maxLength, $minLength, $errorMessage);
    }

    /**
     * Specifies the numeric range constraints for the value of a data field.
     * 
     * @param string $actionName Action name.
     * @param string $propertyName The property name to check.
     * @param int $max The maximum allowed field value.
     * @param int $min The minimum allowed field value.
     * @param string The error message.
     * 
     * @return void
     */
    public static function range($actionName, $propertyName, $min, $max, $errorMessage = null) {
        if ($actionName != PHPMVC_ACTION) {
            return;
        }

        if (empty($actionName)) {
            throw new \Exception('$actionName cannot be empty.');
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName cannot be empty.');
        }

        if ((int)$min > (int)$max) {
            throw new \Exception('The minimum value cannot be greater than the maximum value.');
        }

        self::makeDataAnnotation($propertyName);

        ViewContext::$modelDataAnnotations[$propertyName]->range = array($min, $max, $errorMessage);
    }

    /**
     * Specifies a custom validation method that is used to validate the property.
     * 
     * @param string $actionName Action name.
     * @param string $propertyName The property name to check.
     * @param callback $callback Function to valid the value. 
     *                           The function takes a value of the property and must return true, 
     *                           if successful, or false if the value is incorrect.
     * @param string The error message.
     * 
     * @return void
     */
    public static function validation($actionName, $propertyName, $callback, $errorMessage = null) {
        if ($actionName != PHPMVC_ACTION) {
            return;
        }

        if (empty($actionName)) {
            throw new \Exception('$actionName cannot be empty.');
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName cannot be empty.');
        }

        if (!is_callable($callback)) {
            throw new \Exception('A function is expected.');
        }

        self::makeDataAnnotation($propertyName);

        ViewContext::$modelDataAnnotations[$propertyName]->customValidation = array($callback, $errorMessage);
    }

    /**
     * Sets a values that is used for display in the UI.
     * 
     * @param string $actionName Action name.
     * @param string $propertyName The property name to set.
     * @param string $name The value that is used for display in the UI.
     * @param string $text The value that is used to display a description in the UI.
     * 
     * @return void
     */
    public static function display($actionName, $propertyName, $name, $text = null) {
        if ($actionName != PHPMVC_ACTION) {
            return;
        }

        if (empty($actionName)) {
            throw new \Exception('$actionName cannot be empty.');
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName cannot be empty.');
        }

        self::makeDataAnnotation($propertyName);

        ViewContext::$modelDataAnnotations[$propertyName]->displayName = $name;
        ViewContext::$modelDataAnnotations[$propertyName]->displayText = $text;
    }

    private static function makeDataAnnotation($propertyName) {
        if (!isset(ViewContext::$modelDataAnnotations[$propertyName])) {
            ViewContext::$modelDataAnnotations[$propertyName] = new ModelDataAnnotation();
        }
    }

}