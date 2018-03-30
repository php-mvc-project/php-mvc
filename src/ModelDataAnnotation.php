<?php
namespace PhpMvc;

/**
 * Represents metadata for models.
 */
final class ModelDataAnnotation {

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $displayText;

    /**
     * @var array
     */
    public $required;

    /**
     * @var array
     */
    public $compareWith;

    /**
     * @var array
     */
    public $stringLength;

    /**
     * @var array
     */
    public $range;

    /**
     * @var array
     */
    public $customValidation;

}