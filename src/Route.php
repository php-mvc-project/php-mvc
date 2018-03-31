<?php
namespace PhpMvc;

/**
 * Represents a route.
 */
class Route {

    /**
     * Gets or sets sample of the URL on which the comparison was made.
     * 
     * @var string|null
     */
    protected $url;

    /**
     * Gets or sets unique route name.
     * 
     * @var string
     */
    public $name;

    /**
     * Gets or sets route template.
     * For example: {controller=Home}/{action=index}/{id?}
     * 
     * @var string
     */
    public $template;

    /**
     * Gets or sets default value of the route segments.
     * 
     * @var array
     */
    public $defaults;

    /**
     * Gets or sets route parse constraints.
     * 
     * @var array
     */
    public $constraints;

    /**
     * Gets or sets route values.
     * 
     * @var array
     */
    public $values;

    /**
     * Get value or default.
     * 
     * @param string $key The key whose value is to try to get.
     * @param string $default Default value. Only if the value in the $defaults property is not found.
     * 
     * @return string
     */
    public function getValueOrDefault($key, $default = null) {
        if (!empty($this->values[$key])) {
            return $this->values[$key];
        }
        elseif (!empty($this->defaults[$key])) {
            return $this->defaults[$key];
        }
        else {
            return $default;
        }
    }

    /**
     * [INTERNAL] Sets url. Do not use this function, it is for internal needs.
     * 
     * @return void
     */
    public function setUrl($url) {
        $this->url = $url;
    }

}