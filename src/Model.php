<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpMvc;

/**
 * Represents methods for annotating a models.
 */
final class Model {

    /**
     * Defines ActionContext.
     * 
     * @var ActionContext
     */
    private static $actionContext;

    /**
     * Gets or sets metadata of Model.
     * 
     * @var ModelDataAnnotation[]
     */
    private static $annotations = array();

    /**
     * Gets or sets type of the current model.
     * 
     * @var string|object
     */
    private static $modelType;

    /**
     * Specifies the model type for the action.
     * 
     * @param string|string[] $actionName The action name.
     * @param string|object $modelType The type name of model.
     * 
     * @return void
     */
    public static function set($actionName, $modelType) {
        if (empty($actionName)) {
            throw new \Exception('$actionName must not be empty.');
        }

        if (empty($modelType)) {
            throw new \Exception('$modelType must not be empty.');
        }

        $canUse = false;

        if ($actionName !== '.') {
            if (!is_array($actionName)) {
                $actionName = array($actionName);
            }

            foreach ($actionName as $action) {
                if (self::$actionContext->actionNameEquals($action)) {
                    $canUse = true;
                    break;
                }
            }
        }
        else {
            $canUse = true;
        }

        if (!$canUse) {
            return;
        }

        if (is_object($modelType)) {
            $modelType = get_class($modelType);
        }

        self::$modelType = $modelType;
    }

    /**
     * Marks the specified property with the attribute "requred" 
     * and it is expected that the property value must be specified.
     * 
     * @param string|object $modelType The name type of model.
     * @param string $propertyName The property name.
     * @param string $errorMessage The error message.
     * 
     * @return void
     */
    public static function required($modelType, $propertyName, $errorMessage = null) {
        if (!self::canAnnotate($modelType)) {
            return;
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName must not be empty.');
        }

        self::makeDataAnnotation($propertyName);

        self::$annotations[$propertyName]->required = array($errorMessage);
    }

    /**
     * Sets a property whose value needs to be compared to the value of the specified property.
     * 
     * @param string|object $modelType The name type of model.
     * @param string $propertyName The property name.
     * @param string $compareWith The name of the property with which to compare.
     * @param string $errorMessage The error message.
     * 
     * @return void
     */
    public static function compare($modelType, $propertyName, $compareWith, $errorMessage = null) {
        if (!self::canAnnotate($modelType)) {
            return;
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName must not be empty.');
        }

        if (empty($compareWith)) {
            throw new \Exception('$compareWith must not be empty.');
        }

        if ($propertyName === $compareWith) {
            throw new \Exception('The property names must be different.');
        }

        self::makeDataAnnotation($propertyName);

        self::$annotations[$propertyName]->compareWith = array($compareWith, $errorMessage);
    }

    /**
     * Specifies the minimum and maximum length of characters that are allowed in a data field.
     * 
     * @param string|object $modelType The name type of model.
     * @param string $propertyName The property name to check.
     * @param int $maxLength The maximum length of a string.
     * @param int $minLength The minimum length of a string.
     * @param string The error message.
     * 
     * @return void
     */
    public static function stringLength($modelType, $propertyName, $maxLength, $minLength = null, $errorMessage = null) {
        if (!self::canAnnotate($modelType)) {
            return;
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName must not be empty.');
        }

        self::makeDataAnnotation($propertyName);

        self::$annotations[$propertyName]->stringLength = array($maxLength, $minLength, $errorMessage);
    }

    /**
     * Specifies the numeric range constraints for the value of a data field.
     * 
     * @param string|object $modelType The name type of model.
     * @param string $propertyName The property name to check.
     * @param int $max The maximum allowed field value.
     * @param int $min The minimum allowed field value.
     * @param string The error message.
     * 
     * @return void
     */
    public static function range($modelType, $propertyName, $min, $max, $errorMessage = null) {
        if (!self::canAnnotate($modelType)) {
            return;
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName must not be empty.');
        }

        if ((int)$min > (int)$max) {
            throw new \Exception('The minimum value must not be greater than the maximum value.');
        }

        self::makeDataAnnotation($propertyName);

        self::$annotations[$propertyName]->range = array($min, $max, $errorMessage);
    }

    /**
     * Specifies a custom validation method that is used to validate the property.
     * 
     * @param string|object $modelType The name type of model.
     * @param string $propertyName The property name to check.
     * @param callback $callback Function to valid the value. 
     *                           The function takes a value of the property and must return true, 
     *                           if successful, or false if the value is incorrect.
     * @param string The error message.
     * 
     * @return void
     */
    public static function validation($modelType, $propertyName, $callback, $errorMessage = null) {
        if (!self::canAnnotate($modelType)) {
            return;
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName must not be empty.');
        }

        if (!is_callable($callback)) {
            throw new \Exception('A function is expected.');
        }

        self::makeDataAnnotation($propertyName);

        self::$annotations[$propertyName]->customValidation = array($callback, $errorMessage);
    }

    /**
     * Sets a values that is used for display in the UI.
     * 
     * @param string|object $modelType The name type of model.
     * @param string $propertyName The property name to set.
     * @param string $name The value that is used for display in the UI.
     * @param string $text The value that is used to display a description in the UI.
     * 
     * @return void
     */
    public static function display($modelType, $propertyName, $name, $text = null) {
        if (!self::canAnnotate($modelType)) {
            return;
        }

        if (empty($propertyName)) {
            throw new \Exception('$propertyName must not be empty.');
        }

        self::makeDataAnnotation($propertyName);

        self::$annotations[$propertyName]->displayName = $name;
        self::$annotations[$propertyName]->displayText = $text;
    }

    private static function canAnnotate($modelType) {
        if (empty($modelType)) {
            throw new \Exception('$modelType must not be empty.');
        }

        if (is_object($modelType)) {
            $modelType = get_class($modelType);
        }

        return self::$modelType === $modelType;
    }

    private static function makeDataAnnotation(&$propertyName) {
        $propertyName = is_array($propertyName) ? implode('_', $propertyName) : $propertyName;

        if (!isset(self::$annotations[$propertyName])) {
            self::$annotations[$propertyName] = new ModelDataAnnotation();
        }
    }

}