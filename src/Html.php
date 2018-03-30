<?php
namespace PhpMvc;

/**
 * Helper class for HTML.
 */
class Html {

    /**
     * Gets the page title.
     * 
     * @param string $default Default value.
     * 
     * @return string
     */
    public static function getTitle($default) {
        if (empty(ViewContext::$title)) {
            return htmlspecialchars($default);
        }
        else
        {
            return htmlspecialchars(ViewContext::$title);
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
        if (($dataAnnotation = ViewContext::getModelDataAnnotation($propertyName)) !== null) {
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
        if (($dataAnnotation = ViewContext::getModelDataAnnotation($propertyName)) !== null) {
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

        $errors = ViewContext::$modelState->getErrors();

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
        if (!empty($errors = ViewContext::$modelState->getErrors($propertyName))) {
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
        echo ViewContext::$content;
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
        if (($viewPath = Make::getViewFilePath($path)) !== false) {
            return Make::getView($viewPath, $model);
        }
        else {
            throw new ViewNotFoundException($path);
        }
    }

    /**
     * Returns the path to action.
     * 
     * @return string
     */
    public static function action($actionName, $controllerName = null) {
        $params = '';

        if (empty($controllerName)) {
            $controllerName = PHPMVC_VIEW;
        }
        elseif (is_array($controllerName)) {
            $params = '&' . http_build_query($controllerName);
            $controllerName = PHPMVC_VIEW;
        }

        // TODO: url mode
        return '/?controller=' . $controllerName . '&action=' . $actionName . $params;
        // return '/' . $controllerName . '/' . $actionName;
    }

    /**
     * Gets model state.
     * 
     * @return ModelState
     */
    public static function getModelState() {
        return ViewContext::$modelState;
    }
    
}