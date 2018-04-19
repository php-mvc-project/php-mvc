<?php
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

}