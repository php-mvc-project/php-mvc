<?php
namespace PhpMvc;

/**
 * Represents metadata for models.
 */
final class ModelDataAnnotation {

    /**
     * The display name of the model element.
     * 
     * @var string
     */
    public $displayName;

    /**
     * The description of the model element.
     * 
     * @var string
     */
    public $displayText;

    /**
     * Indicates that the model element is required and must contain a non-empty value.
     * 
     * @var array
     */
    public $required;

    /**
     * Specifies the comparison of the model value with the value of another model parameter.
     * 
     * @var array
     */
    public $compareWith;

    /**
     * Specifies the text size check parameter in the model value.
     * 
     * @var array
     */
    public $stringLength;

    /**
     * Gets or sets parameters to check whether the model value falls within the specified range.
     * 
     * @var array
     */
    public $range;

    /**
     * Specifies a user-defined function to check the model value.
     * 
     * @var array
     */
    public $customValidation;

}