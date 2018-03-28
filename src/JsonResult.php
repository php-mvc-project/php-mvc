<?php
namespace PhpMvc;

/**
 * Represents a class that is used to send JSON-formatted content to the response.
 */
class JsonResult {

    /**
     * Gets or sets the data.
     * 
     * @var mixed
     */
    public $data;

    /**
     * Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     * JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     * JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, 
     * JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, 
     * JSON_PARTIAL_OUTPUT_ON_ERROR. 
     */
    public $options;

    /** 
     * Gets or sets the maximum depth. Must be greater than zero.
     */
    public $depth;

    /**
     * Initializes a new instance of JsonResult.
     * 
     * @param mixed $data Data to encode.
     * @param int $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, 
     * JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, 
     * JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, 
     * JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, 
     * JSON_PARTIAL_OUTPUT_ON_ERROR. 
     * @param int $depth Set the maximum depth. Must be greater than zero. Default: 512.
     */
    public function __construct($data, $options = 0, $depth = 512) {
        $this->data = $data;
        $this->options = $options;
        $this->depth = $depth;
    }

}