<?php
namespace PhpMvc;

/**
 * Helper class for HTML.
 */
class Html {

    /**
     * Defines ViewContext.
     * 
     * @var ViewContext
     */
    private static $viewContext;

    /**
     * Gets the page title.
     * 
     * @param string $default Default value.
     * 
     * @return string
     */
    public static function getTitle($default) {
        if (empty(self::$viewContext->title)) {
            return htmlspecialchars($default);
        }
        else
        {
            return htmlspecialchars(self::$viewContext->title);
        }
    }

    /**
     * Gets display name for the specified field.
     * 
     * @param string $propertyName The name of the property to display the name.
     * 
     * @return string
     */
    public static function displayName($propertyName) {
        if (($dataAnnotation = self::$viewContext->getModelDataAnnotation($propertyName)) !== null) {
            return htmlspecialchars($dataAnnotation->displayName);
        }
    }

    /**
     * Gets display text for the specified field.
     * 
     * @param string $propertyName The name of the property to display the text.
     * 
     * @return string
     */
    public static function displayText($propertyName) {
        if (($dataAnnotation = self::$viewContext->getModelDataAnnotation($propertyName)) !== null) {
            return htmlspecialchars($dataAnnotation->displayText);
        }
    }

    /**
     * Gets an unordered list (ul element) of validation messages.
     * 
     * @param string $validationMessage The message to display if the model contains an error.
     * 
     * @return string
     */
    public static function validationSummary($validationMessage = null) {
        $li = array();

        $errors = self::$viewContext->getModelState()->getErrors();

        foreach ($errors as $key => $value) {
            foreach ($value as $error) {
                if ($error instanceof \Exception) {
                    $message = $error->getMessage();
                }
                else {
                    $message = $error;
                }

                $li[] = '<li>' . htmlspecialchars($message) . '</li>';
            }
        }

        if (!empty($li)) {
            return '<div class="validation-summary-errors">' .
            (!empty($validationMessage) ?  $validationMessage : '') . 
            '<ul>' . implode('', $li) . '</ul>' .
            '</div>';
        }
        else {
            return '';
        }
    }

    /**
     * Gets a validation message if an error exists for the specified field.
     * 
     * @param string $properyName The name of the property that is being validated.
     * @param string $validationMessage The message to display if the field contains an error.
     * 
     * @return string
     */
    public static function validationMessage($propertyName, $validationMessage = null) {
        if (!empty($errors = self::$viewContext->modelState->getErrors($propertyName))) {
            $result = '<span class="field-validation-error">';
            
            if (!empty($validationMessage)) {
                $result = $validationMessage . '<br />';
            }

            $result .= implode('<br />', array_map('htmlspecialchars', $errors));

            $result .= '</span>';

            return $result;
        }
    }

    /**
     * Renders main content of the view.
     * 
     * @return void
     */
    public static function renderBody() {
        // echo self::view(PHPMVC_CURRENT_VIEW_PATH);
        echo self::$viewContext->content;
    }

    /**
     * Renders the specified view.
     * 
     * @param string $path The name or file path of the view.
     * @param string $model The model.
     * 
     * @return void
     */
    public static function render($path, $model = null) {
        echo self::view($path);
    }

    /**
     * Returns the specified view as an HTML-encoded string.
     * 
     * @param string $path The name or file path of the view.
     * @param string $model The model.
     * 
     * @return string
     */
    public static function view($path, $model = null) {
        if (($viewPath = PathUtility::getViewFilePath($path)) !== false) {
            return Make::getView($viewPath, $model);
        }
        else {
            throw new ViewNotFoundException($path);
        }
    }

    /**
     * Returns the path to action.
     * 
     * @param string $actionName The name of the action.
     * @param string $controllerName The name of the controller. Default: current controller.
     * @param array $routeValues An array that contains the parameters for a route.
     * @param string $fragment The URL fragment name (the anchor name).
     * @param string $schema The protocol for the URL, such as "http" or "https".
     * @param string $host The host name for the URL.
     * 
     * @return string
     */
    public static function action($actionName, $controllerName = null, $routeValues = null, $fragment = null, $schema = null, $host = null) {
        return UrlHelper::action(self::$viewContext, $actionName, $controllerName, $routeValues, $fragment, $schema, $host);
    }

    /**
     * Returns a link (<a />) to the specified action.
     * 
     * @param string $linkText The link text.
     * @param string $actionName The name of the action.
     * @param string $controllerName The name of the controller. Default: current controller.
     * @param array $routeValues An array that contains the parameters for a route.
     * @param string $fragment The URL fragment name (the anchor name).
     * @param string $schema The protocol for the URL, such as "http" or "https".
     * @param string $host The host name for the URL.
     * @param string $htmlAttributes Additional HTML attributes that will be used when creating the link.
     * 
     * @return string
     */
    public static function actionLink($linkText, $actionName, $controllerName = null, $routeValues = null, $htmlAttributes = null, $fragment = null, $schema = null, $host = null) {
        $url = UrlHelper::action(self::$viewContext, $actionName, $controllerName, $routeValues, $fragment, $schema, $host);
        $attr = self::buildAttributes($htmlAttributes, array('href'));

        return '<a href="' . $url . '"' . (!empty($attr) ? ' ' : '') . $attr . '>' . htmlspecialchars($linkText) . '</a>';
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public static function getModelState() {
        return View::getModelState();
    }

    /**
     * Generates HTML-attributes string.
     * 
     * @param array $htmlAttributes Associative array of attributes.
     * @param array $ignore List of attributes to ignore.
     * 
     * @return string
     */
    private static function buildAttributes($htmlAttributes, $ignore = array())
    {
        if (empty($htmlAttributes))
        {
            return '';
        }

        if ($ignore === null) {
            $ignore = array();
        }

        $filtered = array_filter($htmlAttributes, function($key) use ($ignore) {
            return !in_array($key, $ignore);
        }, \ARRAY_FILTER_USE_KEY);

        array_walk($filtered, function(&$value, $key) {
            $value = sprintf('%s="%s"', $key, str_replace('"', '\"', $value));
        });

        return implode(' ', $filtered);
    }

}