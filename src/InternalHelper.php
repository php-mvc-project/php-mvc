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
 * Internal helper class.
 */
final class InternalHelper {

    /**
     * Gets the property value.
     * If the property is unavailable, makes it available.
     * 
     * @param mixed $class The class instance, that contains the property.
     * @param string $name The name of the property.
     * 
     * @return mixed
     */
    public static function getPropertyValue($class, $name) {
        $property = new \ReflectionProperty($class, $name);
        $property->setAccessible(true);
        return $property->getValue($class);
    }

    /**
     * Sets the value to the specified property of class instance.
     * If the property is unavailable, makes it available.
     * 
     * @param mixed $class The class instance, that contains the property.
     * @param string $name The name of the property.
     * @param mixed $value The value to set.
     * 
     * @return ReflectionProperty
     */
    public static function setPropertyValue($class, $name, $value) {
        $property = new \ReflectionProperty($class, $name);
        $property->setAccessible(true);
        $property->setValue($class, $value);

        return $property;
    }

    /**
     * Gets the property value of the base class.
     * If the property is unavailable, makes it available.
     * 
     * @param mixed $instance The class instance, that contains the property.
     * @param string $name The name of the property.
     * 
     * @return mixed
     */
    public static function getPropertyValueOfParentClass($instance, $name) {
        $class = new \ReflectionClass($instance);
        $parent = $class->getParentClass();
        $property = $parent->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }

    /**
     * Gets the property value of the static class.
     * If the property is unavailable, makes it available.
     * 
     * @param mixed $class The class name, that contains the property.
     * @param string $name The name of the property.
     * 
     * @return mixed
     */
    public static function getStaticPropertyValue($class, $name) {
        $property = new \ReflectionProperty($class, $name);
        $property->setAccessible(true);
        return $property->getValue(null);
    }

    /**
     * Sets the value to the specified property of static class.
     * If the property is unavailable, makes it available.
     * 
     * @param mixed $class The class name, that contains the property.
     * @param string $name The name of the property.
     * @param mixed $value The value to set.
     * 
     * @return ReflectionProperty
     */
    public static function setStaticPropertyValue($class, $name, $value) {
        $property = new \ReflectionProperty($class, $name);
        $property->setAccessible(true);
        $property->setValue(null, $value);

        return $property;
    }

    /**
     * Calls the specified method. If the method is protected, this restriction is ignored.
     * 
     * @param mixed $class The class name, that contains the method.
     * @param string $name The name of the method.
     * 
     * @return mixed
     */
    public static function invokeMethod($class, $name) {
        $property = new \ReflectionMethod($class, $name);
        $property->setAccessible(true);

        return $property->invoke($class);
    }

    /**
     * Gets the content of the view.
     * 
     * @param string $path File name or path to the view file.
     * 
     * @return string
     */
    public static function getViewContent($path) {
        if ($path === false) {
            throw new \Exception('The view file is not specified. Probably the correct path to the file was not found. Make sure that all paths are specified correctly, the files exists and is available.');
        }

        ob_start();

        require($path);

        $result = ob_get_contents();

        ob_end_clean();

        return $result;
    }

    /**
     * 
     * @return ViewContext
     */
    public static function makeViewContext($viewFile, $actionContext, $actionResult, $model, $viewData, $parentActionViewContext, $parent, $body) {
        // create context for view
        $viewContext = new ViewContext($viewFile, $actionContext, $actionResult, $model, $viewData, $parentActionViewContext, $parent, $body);

        if ($parentActionViewContext === null) {
            $parentActionViewContext = $body = $viewContext;
        }

        // set view context
        self::setViewContext($parentActionViewContext, $viewContext);

        if ($viewContext->actionResult !== null) {
            // execute result with view context
            $viewContext->actionResult->execute($viewContext);
        }

        // get view file path
        $viewFile = PathUtility::getViewFilePath($viewContext->viewFile);

        // check view file path
        if ($viewFile === false) {
            if ($actionResult instanceof ExceptionResult) {
                throw $actionResult->getException();
            }
            else {
                throw new ViewNotFoundException($viewContext->viewFile);
            }
        }

        // get view content
        $viewContext->content = self::getViewContent($viewContext->viewFile);

        // get layout
        if (!empty($viewContext->layout)) {
            if (($layoutFile = PathUtility::getViewFilePath($viewContext->layout)) !== false) {
                // create layout context and set the context as the parent to the view context
                $layoutResult = new ViewResult($layoutFile);
                $viewContext->parent = self::makeViewContext(
                    $layoutFile,
                    $actionContext,
                    $layoutResult,
                    $parentActionViewContext->model,
                    $parentActionViewContext->viewData,
                    $parentActionViewContext,
                    null,
                    $viewContext
                );
                // restore view context
                self::setViewContext($parentActionViewContext, $viewContext->parent !== null ? $viewContext->parent : $viewContext);
            }
            else {
                throw new ViewNotFoundException($viewContext->layout);
            }
        }
        else {
            // restore context to back
            if ($viewContext->parent !== null) {
                self::setViewContext($parentActionViewContext, $viewContext->parent);
            }
        }

        return $viewContext;
    }

    public static function getTopLevelViewContext($viewContext) {
        $result = $viewContext;

        while (isset($result->parent)) {
            $result = self::getTopLevelViewContext($result->parent);
        }

        return $result;
    }

    /**
     * Converts array to object,
     * 
     * @param array $array The array to convert.
     * @param \stdClass|mixed|null $object The result object.
     * @param string $type The type of data that the function should return. Default: \stdClass
     * 
     * @return void
     */
    public static function arrayToObject($array, &$object = null, $type = null) {
        $object = isset($object) ? $object : (isset($type) ? new $type() : new \stdClass());

        ksort($array);

        foreach ($array as $key => $value)
        {
            if (strpos($key, '_') === false) {
                $activeObject = $object;
                
                if (is_array($object) && $type !== null) {
                    $activeObject = new $type();
                    $object[] = $activeObject;
                }

                // is array
                if (substr($key, -1) == ']') {
                    $key = substr($key, 0, strrpos($key, '['));

                    if (!isset($activeObject->$key) || !is_array($activeObject->$key)) {
                        $activeObject->$key = array();
                    }

                    if (is_array($value)) {
                        foreach ($value as $v) {
                            $activeObject->{$key}[] = $v;
                        }
                    }
                    else {
                        $activeObject->{$key}[] = $value;
                    }
                }
                else {
                    $activeObject->$key = $value;
                }
            } else {
                $name = explode('_', $key);
                $activeObject = $object;

                while (count($name) > 1) {
                    $current = array_shift($name);
                    $isArray = false;

                    // is array
                    if (substr($current, -1) == ']') {
                        $index = substr($current, strrpos($current, '[') + 1, -1);
                        $current = substr($current, 0, strrpos($current, '['));
                        $isArray = true;
                    }

                    if (isset($activeObject->$current)) {
                        if ($isArray) {
                            if (!isset($activeObject->$current[$index])) {
                                $activeObject->$current[$index] = (isset($type) ? new $type() : new \stdClass());
                            }

                            $activeObject = $activeObject->$current[$index];
                        }
                        else {
                            $activeObject = $activeObject->$current;
                        }

                        continue;
                    }

                    $reflection = new \ReflectionClass($activeObject);

                    if (isset($reflection) &&  $reflection->hasProperty($current)) {
                        $property = $reflection->getProperty($current);

                        if (($propertyComment = $property->getDocComment()) !== null && $propertyComment !== '' && preg_match('/@var\s+(?<name>[^\n]+)/', $propertyComment, $m) === 1) {
                            $propertyType = trim($m[1]);

                            if (substr($propertyType, -1) == ']') {
                                $propertyType = substr($propertyType, 0, strrpos($propertyType, '['));
                            }

                            if (!class_exists($propertyType)) {
                                if (is_object($object)) {
                                    $objectReflection = new \ReflectionClass($object);
                                    $propertyTypeNamesapce = $objectReflection->getNamespaceName();
                                }
                                elseif ($type !== null && $type != '\stdClass') {
                                    $propertyTypeNamesapce = substr($type, 0, strrpos($type, '\\'));
                                }

                                if (class_exists($propertyTypeNamesapce . '\\' . $propertyType)) {
                                    $propertyType = $propertyTypeNamesapce . '\\' . $propertyType;
                                }
                                else {
                                    $propertyType = '\stdClass';
                                }
                            }
                        }
                        else {
                            $propertyType = '\stdClass';
                        }
                    }
                    else {
                        $propertyType = '\stdClass';
                    }

                    if ($isArray) {
                        $newObject = array();
                        $newObject[$index] = new $propertyType();
                        $type = $propertyType;
                    }
                    else {
                        $newObject = new $propertyType();
                        $type = null;
                    }

                    if (isset($property)) {
                        $property->setValue($activeObject, $newObject);
                    }
                    else {
                        $activeObject->$current = $newObject;
                    }

                    $activeObject = ($isArray ? $newObject[$index] : $newObject);
                }

                self::arrayToObject(array($name[0] => $value), $activeObject, $type);
            }
        }
    }

    /**
     * Gets single key from array or all keys, if key is null.
     * 
     * @param array $array The array to work.
     * @param string|null $key The key to get, or null to get the $array.
     * @param string|null $default The default value if the key is not null, and the value with the specified key was not found. The default value is null.
     * @param bool $nullIfEmpty Check the value with the empty() function. If empty() returns true, the function returns $default.
     * 
     * @return array|mixed|null
     */
    public static function getSingleKeyOrAll($array, $key, $default = null, $nullIfEmpty = false) {
        if ($key !== null) {
            if ($nullIfEmpty === true) {
                return (isset($array[$key])) && !empty($array[$key]) ? $array[$key] : $default;
            }
            else {
                return (isset($array[$key])) ? $array[$key] : $default;
            }
        }
        else {
            return $array;
        }
    }

    /**
     * Sets view context to static classes.
     * 
     * @param ViewContext $parentActionViewContext The ViewContext instance of the action.
     * @param ViewContext $viewContext The ViewContext instance to set.
     */
    private static function setViewContext($parentActionViewContext, $viewContext) {
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\View', 'viewContext', $viewContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Html', 'viewContext', $viewContext);
        InternalHelper::setStaticPropertyValue('\\PhpMvc\\Html', 'parentActionViewContext', $parentActionViewContext);
    }

}