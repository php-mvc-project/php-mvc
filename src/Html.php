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
     * Defines parent action ViewContext.
     * 
     * @var ViewContext
     */
    private static $parentActionViewContext;

    /**
     * Gets the page title.
     * 
     * @param string $default Default value.
     * 
     * @return string
     */
    public static function getTitle($default = '') {
        if (empty(self::$viewContext->title)) {
            if (isset(self::$viewContext->body) && !empty(self::$viewContext->body->title)) {
                return htmlspecialchars(self::$viewContext->body->title);
            }
            else {
                return htmlspecialchars($default);
            }
        }
        else
        {
            return htmlspecialchars(self::$viewContext->title);
        }
    }

    /**
     * Gets display name for the specified field.
     * 
     * @param string|array $propertyName The name of the property to display the name.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * 
     * @return string
     */
    public static function displayName($propertyName) {
        $propertyName = (is_array($propertyName) ? implode('_', $propertyName) : $propertyName);

        if (($dataAnnotation = self::$viewContext->getModelDataAnnotation($propertyName)) !== null) {
            return htmlspecialchars($dataAnnotation->displayName);
        }
        else {
            return '';
        }
    }

    /**
     * Gets display text for the specified field.
     * 
     * @param string|array $propertyName The name of the property to display the text.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * 
     * @return string
     */
    public static function displayText($propertyName) {
        $propertyName = (is_array($propertyName) ? implode('_', $propertyName) : $propertyName);

        if (($dataAnnotation = self::$viewContext->getModelDataAnnotation($propertyName)) !== null) {
            return htmlspecialchars($dataAnnotation->displayText);
        }
        else {
            return '';
        }
    }

    /**
     * Gets an unordered list (ul element) of validation messages.
     * 
     * @param string $validationMessage The message to display if the model contains an error.
     * @param string $tag The tag to wrap the message in the generated HTML. Default: div.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function validationSummary($validationMessage = null, $tag = 'div', $htmlAttributes = array()) {
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;
        $tag =  empty($tag) ? 'div' : trim($tag, '<>');

        if (!isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = 'validation-summary-errors';
        }
        else {
            $htmlAttributes['class'] .= ' validation-summary-errors';
        }

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
            return '<' . $tag . ' ' . self::buildAttributes($htmlAttributes) . '>' .
            (!empty($validationMessage) ? htmlspecialchars($validationMessage) : '') . 
            '<ul>' . implode('', $li) . '</ul>' .
            '</' . $tag . '>';
        }
        else {
            return '';
        }
    }

    /**
     * Gets a validation message if an error exists for the specified field.
     * 
     * @param string|array $propertyName The name of the property that is being validated.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $validationMessage The message to display if the field contains an error.
     * @param string $tag The tag to wrap the message in the generated HTML. Default: span.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function validationMessage($propertyName, $validationMessage = null, $tag = 'span', $htmlAttributes = array()) {
        $propertyName = (is_array($propertyName) ? implode('_', $propertyName) : $propertyName);
        $htmlAttributes = ($htmlAttributes === null ? array() : $htmlAttributes);
        $tag = empty($tag) ? 'div' : trim($tag, '<>');

        if (!isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = 'field-validation-error';
        }
        else {
            $htmlAttributes['class'] .= ' field-validation-error';
        }

        if (!empty($errors = self::$viewContext->getModelState()->getErrors($propertyName))) {
            $result = '<' . $tag . ' ' . self::buildAttributes($htmlAttributes) . '>';
            
            if (!empty($validationMessage)) {
                $result .= htmlspecialchars($validationMessage) . '<br />';
            }

            $result .= implode('<br />', array_map('htmlspecialchars', $errors));

            $result .= '</' . $tag . '>';

            return $result;
        }
    }

    /**
     * Renders main content of the view.
     * 
     * @return void
     */
    public static function renderBody() {
        echo self::$viewContext->body->content;
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
        echo self::view($path, $model);
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
            $viewResult = new ViewResult($viewPath, $model);
            $viewContext = InternalHelper::makeViewContext(
                $viewPath, 
                self::$viewContext,
                $viewResult,
                $model,
                null,
                self::$parentActionViewContext,
                self::$viewContext,
                null
            );

            return $viewContext->content; //InternalHelper::getViewContent($viewPath, self::$viewContext, $model);
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
     * Returns a link (<a>) to the specified action.
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

    private static $token = null;

    /**
     * Returns a <hidden> element (antiforgery token) that will be validated when the containing <form> is submitted.
     * 
     * @param string $dynamic Specifies whether JavaScript should be used to create the hidden element.
     * This can improve protection. Default: FALSE.
     * 
     * @return string
     */
    public static function antiForgeryToken($dynamic = false) {
        if (self::$token === null) {
            self::$token = bin2hex(function_exists('random_bytes') ? random_bytes(64) : uniqid('', true));
            $response = self::$viewContext->getHttpContext()->getResponse();
            $response->addCookie('__requestVerificationToken', self::$token, 0, '/', '', false, true);
        }

        if ($dynamic === true) {
            return '<script>document.write(\'<input type="hidden" name="__requestVerificationToken" id="__requestVerificationToken" value="' . self::$token . '" />\');</script>';
        }
        else {
            return '<input type="hidden" name="__requestVerificationToken" id="__requestVerificationToken" value="' . self::$token . '" />';
        }
    }

    /**
     * Renders a <form> start tag to the response. When the user submits the form, the current action will process the request.
     * 
     * @param string $actionName The name of the action method.
     * @param string $controllerName The name of the controller.
     * @param array $routeValues The parameters for a route.
     * @param string $method The HTTP method for processing the form, either GET or POST. Default: POST.
     * @param array $htmlAttributes The HTML attributes for the element.
     * @param bool|array $antiforgery If TRUE, <form> elements will include an antiforgery token.
     * If is array, then antiforgery token will be dynamic.
     * 
     * @return string
     */
    public static function beginForm($actionName, $controllerName = null, $routeValues = null, $method = 'post', $antiforgery = false, $htmlAttributes = array()) {
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;

        if (!empty($method)) {
            $htmlAttributes['method'] = $method;
        }
        elseif (empty($htmlAttributes['method'])) {
            $htmlAttributes['method'] = 'post';
        }

        $htmlAttributes['action'] = UrlHelper::action(self::$viewContext, $actionName, $controllerName, $routeValues);

        $attr = self::buildAttributes($htmlAttributes);

        $result = '<form ' . $attr . '>';

        if ($antiforgery === true) {
            $result .= self::antiForgeryToken();
        }
        elseif (is_array($antiforgery)) {
            $result .= self::antiForgeryToken(true);
        }
        else {
            self::$viewContext->getHttpContext()->getResponse()->addCookie('__requestVerificationToken', 'false', 0, '/', '', false, true);
        }

        return $result;
    }

    /**
     * Returns the </form> end tag to the response.
     * 
     * @return string
     */
    public static function endForm() {
        return '</form>';
    }

    /**
     * Returns an <input> element of type "checkbox".
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param bool|null $checked If true, checkbox is initially checked.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function checkBox($name, $checked = null, $htmlAttributes = array()) {
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;

        if (self::getModelValue($name, $modelValue, \FILTER_VALIDATE_BOOLEAN) === true && $modelValue === true) {
            $htmlAttributes['checked'] = 'checked';
            $checked = null;
        }

        if (filter_var($checked, \FILTER_VALIDATE_BOOLEAN) === true) {
            $htmlAttributes['checked'] = 'checked';
        }

        return self::input($name, 'checkbox', 'true', $htmlAttributes, true);
    }

    /**
     * Returns a single-selection HTML <select> element.
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param array|SelectListItem[] $list The list of values.
     * @param string|array|null $selectedValue If non-null, this value will be used as the selected value.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function dropDownList($name, $list, $selectedValue = null, $htmlAttributes = array()) {
        $result = '';

        $name = is_array($name) ? $name : rtrim($name, '[]');

        if (self::getModelValue($name, $modelValue) === true) {
            $selectedValue = $modelValue;
        }

        $name = is_array($name) ? implode('_', $name) : $name;

        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;
        $htmlAttributes['name'] = isset($htmlAttributes['name']) ? $htmlAttributes['name'] : $name;
        $htmlAttributes['id'] = isset($htmlAttributes['id']) ? $htmlAttributes['id'] : $name;

        $result .= '<select ' . self::buildAttributes($htmlAttributes) . '>';

        $groups = array_filter($list, function($item) {
            return $item instanceof SelectListItem && isset($item->group);
        });

        $groups = array_unique(array_map(function($item) {
            return $item->group;
        }, $groups), SORT_REGULAR);

        if (count($groups) == 1 && $groups[0] === null) {
            $groups = array();
        }

        if (!empty($groups)) {
            $listByGroup = array();

            foreach ($groups as $group) {
                $listByGroup = array_filter($list, function($item) use ($group) {
                    return $item->group === $group;
                });

                $groupAttr = array();

                if ($group->name !== '') {
                    $groupAttr['lable'] = $group->name;
                }

                if ($group->disabled === true) {
                    $groupAttr['disabled'] = 'disabled';
                }

                $attr = self::buildAttributes($groupAttr);
                $result .= '<optgroup' . (!empty($attr) ? ' ' : '') . $attr . '>';
                $result .= self::getSelectOptions($listByGroup, $selectedValue);
                $result .= '</optgroup>';
            }

            $listByGroup = array_filter($list, function($item) {
                return $item->group === null;
            });

            if (!empty($listByGroup)) {
                $result .= self::getSelectOptions($listByGroup, $selectedValue);
            }
        }
        else {
            $result .= self::getSelectOptions($list, $selectedValue);
        }

        $result .= '</select>';

        return $result;
    }

    /**
     * Returns list of <option> for <select>.
     * 
     * @param array|SelectListItem[] $list The list of values.
     * @param string|array|null The selected value.
     * 
     * @return string
     */
    private static function getSelectOptions($list, $selectedValue) {
        $result = '';

        if ($selectedValue !== null && !is_array($selectedValue)) {
            $selectedValue = array($selectedValue);
        }

        foreach ($list as $item) {
            $itemAttr = array();
            $text = '';

            if ($item instanceof SelectListItem) {
                $text = $item->text;
                $itemAttr['value'] = $item->value;

                if ($item->disabled === true) {
                    $itemAttr['disabled'] = 'disabled';
                }

                if ($selectedValue === null && $item->selected === true) {
                    $itemAttr['selected'] = 'selected';
                }
                elseif ($selectedValue !== null && in_array($item->value, $selectedValue) === true) {
                    $itemAttr['selected'] = 'selected';
                }
            }
            else {
                $text = $item;

                if ($selectedValue !== null && in_array($item, $selectedValue)) {
                    $itemAttr['selected'] = 'selected';
                }
            }

            $attr = self::buildAttributes($itemAttr);

            $result .= '<option' . (!empty($attr) ? ' ' : '') . $attr . '>';
            $result .= htmlspecialchars($text);
            $result .= '</option>';
        }

        return $result;
    }

    /**
     * Returns an <input> element of type "hidden".
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $value If non-null, value to include in the element.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function hidden($name, $value = null, $htmlAttributes = array()) {
        return self::input($name, 'hidden', $value, $htmlAttributes);
    }

    /**
     * Returns a <label> element.
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $text The inner text of the element.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function label($name, $text = null, $htmlAttributes = array()) {
        $name = is_array($name) ? implode('_', $name) : $name;
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;
        $htmlAttributes['for'] = isset($htmlAttributes['for']) ? $htmlAttributes['for'] : $name;

        if (empty($text)) {
            if (!empty($annotation = self::$viewContext->getModelState()->getAnnotation($name))) {
                $text = $annotation->displayName;
            }
        }

        return '<label ' . self::buildAttributes($htmlAttributes) . '>' . htmlspecialchars($text) . '</label>';
    }

    /**
     * Returns a multi-selection <select> element.
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param array|SelectListItem[] $list The list of values.
     * @param int $size The size of the list.
     * @param string|array|null $selectedValue If non-null, this value will be used as the selected value.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function listBox($name, $list, $size = 1, $selectedValue = null, $htmlAttributes = array()) {
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;

        if (empty($size)) {
            $size = 1;
        }

        $nameString = is_array($name) ? implode('_', $name) : $name;
        $name = isset($htmlAttributes['name']) ? rtrim($htmlAttributes['name'], '[]') . '[]' : $name;

        if (is_array($name)) {
            $name[count($name) - 1] = rtrim($name[count($name) - 1], '[]') . '[]';
        }

        $htmlAttributes['name'] = rtrim(isset($htmlAttributes['name']) ? $htmlAttributes['name'] : $nameString, '[]') . '[]';
        $htmlAttributes['id'] = isset($htmlAttributes['id']) ? $htmlAttributes['id'] :  rtrim($nameString, '[]');

        $htmlAttributes['size']  = $size;
        $htmlAttributes['multiple']  = 'multiple';

        return self::dropDownList($name, $list, $selectedValue, $htmlAttributes);
    }

    /**
     * Returns an <input> element of type "password".
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $value If non-null, value to include in the element.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function password($name, $value = null, $htmlAttributes = array()) {
        return self::input($name, 'password', $value, $htmlAttributes);
    }

    /**
     * Returns an <input> element of type "email".
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $value If non-null, value to include in the element.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function email($name, $value = null, $htmlAttributes = array()) {
        return self::input($name, 'email', $value, $htmlAttributes);
    }

    /**
     * Returns an <input> element of type "radio".
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param mixed $value If non-null, value to include in the element. Must not be null if $checked is also null and no "checked" entry exists in $htmlAttributes.
     * @param bool|null $checked If true, checkbox is initially checked.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function radioButton($name, $value = null, $checked = null, $htmlAttributes = array()) {
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;

        if ($value === null && $checked === null && $htmlAttributes['checked'] === null) {
            throw new \Exception('The $value must not be null if $checked is also null and no "checked" entry exists in $htmlAttributes.');
        }

        if (self::getModelValue($name, $modelValue) === true) {
            if ($modelValue == $value) {
                $htmlAttributes['checked'] = 'checked';
            }

            $checked = null;
        }

        if ($checked === true) {
            $htmlAttributes['checked'] = 'checked';
        }

        return self::input($name, 'radio', $value, $htmlAttributes, true);
    }

    /**
     * Returns a <textarea> element.
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $value If non-null, value to include in the element.
     * @param int $rows Number of rows in the textarea.
     * @param int $columns Number of columns in the textarea.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function textArea($name, $value = '', $rows = null, $columns = null, $htmlAttributes = array()) {
        $result = '';
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;
        $nameString = is_array($name) ? implode('_', $name) : $name;

        $htmlAttributes['name'] = isset($htmlAttributes['name']) ? $htmlAttributes['name'] : $nameString;
        $htmlAttributes['id'] = isset($htmlAttributes['id']) ? $htmlAttributes['id'] : $nameString;

        if (!isset($htmlAttributes['required'])) {
            if (!empty($annotation = self::$viewContext->getModelState()->getAnnotation($name))) {
                if (isset($annotation->required)) {
                    $htmlAttributes['required'] = 'required';
                }
            }
        }

        if (self::getModelValue($name, $modelValue) === true) {
            $value = $modelValue;
        }

        if ($rows !== null &&  (int)$rows > 0) {
            $htmlAttributes['rows'] = (int)$rows;
        }

        if ($columns !== null &&  (int)$columns > 0) {
            $htmlAttributes['cols'] = (int)$columns;
        }

        $result .= '<textarea ' . self::buildAttributes($htmlAttributes) . '>';
        $result .= htmlspecialchars($value);
        $result .= '</textarea>';

        return $result;
    }

    /**
     * Returns an <input> element of type "text".
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $value If non-null, value to include in the element.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    public static function textBox($name, $value = null, $htmlAttributes = array()) {
        return self::input($name, 'text', $value, $htmlAttributes);
    }

    /**
     * Converts the specified string to an HTML-encoded string.
     * 
     * @param string $value String to encode.
     * 
     * @return string
     */
    public static function encode($value) {
        return htmlspecialchars($value);
    }

    /**
     * Gets the data with the specified key.
     * If the specified key does not exist, function returns null.
     * If no key is specified, returns all data.
     * 
     * @param string $key The key to get the data.
     * 
     * @return mixed|array|null
     */
    public static function getData($key = null) {
        if (!isset($key)) {
            return self::$viewContext->viewData;
        }
        else {
            return isset(self::$viewContext->viewData[$key]) ? self::$viewContext->viewData[$key] : null;
        }
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
     * Returns an <input> element.
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param string $type The element type. Default: text.
     * @param string $value If non-null, value to include in the element.
     * @param array $htmlAttributes The HTML attributes for the element.
     * 
     * @return string
     */
    private static function input($name, $type = 'text', $value = null, $htmlAttributes = null, $ignoreModelValue = false) {
        $htmlAttributes = $htmlAttributes === null ? array() : $htmlAttributes;

        $nameString = is_array($name) ? implode('_', $name) : $name;

        $htmlAttributes['type'] = isset($htmlAttributes['type']) ? $htmlAttributes['type'] : $type;
        $htmlAttributes['name'] = isset($htmlAttributes['name']) ? $htmlAttributes['name'] : $nameString;
        $htmlAttributes['id'] = isset($htmlAttributes['id']) ? $htmlAttributes['id'] : $nameString;

        if (!isset($htmlAttributes['required'])) {
            if (!empty($annotation = self::$viewContext->getModelState()->getAnnotation($name))) {
                if (isset($annotation->required)) {
                    $htmlAttributes['required'] = 'required';
                }
            }
        }

        if ($ignoreModelValue !== true && self::getModelValue($name, $modelValue) === true) {
            $value = $modelValue;
        }

        if ($value !== null) {
            $htmlAttributes['value'] = $value;
        }

        return '<input ' . self::buildAttributes($htmlAttributes) . ' />';
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
            $value = sprintf('%s="%s"', $key, self::encodeHtmlAttributeString($value));
        });

        return implode(' ', $filtered);
    }

    /**
     * Escapes special characters in the specified value for its use as the value of an HTML-attribute.
     * 
     * @param string $value String to encode.
     * 
     * @return string
     */
    private static function encodeHtmlAttributeString($value) {
        if ($value === null) {
            return '';
        }

        $result = $value;

        $result = str_replace('&', '&amp;', $result);
        $result = str_replace('<', '&lt;', $result);
        $result = str_replace('"', '&quot;', $result);
        $result = str_replace('\'', '&#39;', $result);

        return $result;
    }

    /**
     * Gets value from model state or model.
     * 
     * @param string|array $name The name of the element.
     * It can be a string or an array.
     * If an array is specified, the property chain in the model will be searched.
     * For example: array('A', 'B') is $model->A->B.
     * @param mixed $value The result.
     * @param int $filter See http://php.net/manual/en/filter.filters.php
     * 
     * @return bool
     */
    private static function getModelValue($name, &$value, $filter = \FILTER_DEFAULT) {
        $modelState = self::$viewContext->getModelState();
        $name = is_array($name) ? $name : array($name);

        if (empty($modelState->items)) {
            $model = self::$viewContext->model;
        }
        else {
            if (isset($modelState->items[implode('_', $name)])) {
                $model = $modelState->items;
            }
            else {
                $model = self::$viewContext->model;
            }
        }
        
        if (empty($model)) {
            $value = filter_var(null, $filter);
            return false;
        }

        if (is_object($model) && $model instanceof ModelStateEntry) {
            if (is_array($model->value)) {
                $value = $model->value;
            }
            else {
                $value = filter_var($model->value, $filter);
            }

            return true;
        }
        elseif (is_object($model)) {
            $current = $model;

            while (count($name) > 1) {
                $key = array_shift($name);

                if (substr($key, -2) == '[]') {
                    $key = rtrim($key, '[]');
                }
    
                if (isset($current->$key)) {
                    $current = $current->$key;
                }
                else {
                    $current = null;
                    break;
                }
            }

            $name = rtrim($name[0], '[]');

            if (is_object($current) && isset($current->$name)) {
                if (is_array($current->$name)) {
                    $value = $current->$name;
                }
                else {
                    $value = filter_var($current->$name, $filter);
                }

                return true;
            }
            elseif (is_array($current) && isset($current[$name])) {
                if (is_array($current->$name)) {
                    $value = $current->$name;
                }
                else {
                    $value = filter_var($current->$name, $filter);
                }

                return true;
            }
            else {
                $value = filter_var(null, $filter);
                return false;
            }
        }
        elseif (is_array($model)) {
            $name = implode('_', $name);

            if (isset($model[$name])) {
                $value = ($model[$name] instanceof ModelStateEntry ? $model[$name]->value : $model[$name]);

                if (!is_array($value)) {
                    $value = filter_var($value, $filter);
                }

                return true;
            }
            else {
                $value = filter_var(null, $filter);
                return false;
            }
        }
        else {
            throw new \Exception('The data type "' . gettype($model) . '" is not supported.');
        }

        return true;
    }

}