<?php
namespace PhpMvc;

/**
 * Represents the base class for classes that contain event data, and provides a value to use for events that do not include event data.
 */
class EventArgs {

    /**
     * Provides a value to use with events that do not have event data.
     * 
     * @return EventArgs
     */
    public static function getEmpty() {
        return new EventArgs();
    }

}