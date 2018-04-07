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
     * Initializes a new instance of the ActionContext for the current request.
     * 
     * @param string $name The unique name of the route.
     * @param string $template The template by which the route will be searched.
     * Use curly braces to denote the elements of the route.
     * For example: {controller}/{action}/{id}
     * {controller=Home}/{action=index}/{id?}
     * @param array $defaults An associative array containing the default values for the elements defined in the $template.
     * For example, $template is {controller}/{action}/{id}
     * $defaults = array('controller' => 'Home', 'action' => 'index', id => \PhpMvc\UrlParameter.OPTIONAL)
     * @param array $constraints An associative array containing regular expressions for checking the elements of the route.
     * For example, $template is {controller}/{action}/{id}
     * $constraints = array('id' => '\w+')
     */
    public function __construct($name = null, $template = null, $defaults = null, $constraints = null) {
        $this->name = $name;
        $this->template = $template;
        $this->defaults = $defaults;
        $this->constraints = $constraints;
    }

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
     * @param string $url Url to set.
     * 
     * @return void
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * [INTERNAL] Gets url.
     * 
     * @return void
     */
    public function getUrl() {
        return $this->url;
    }

}