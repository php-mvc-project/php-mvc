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
 * Represents the application context.
 */
final class AppContext {

    /**
     * The config of the application.
     * 
     * @var array
     */
    protected $config = array();

    /**
     * The handlers for the "preInit" event.
     * 
     * @var callback[]
     */
    protected $preInit = array();

    /**
     * The handlers for the "init" event.
     * 
     * @var callback[]
     */
    protected $init = array();

    /**
     * The handlers for the "actionContextInit" event.
     * 
     * @var callback[]
     */
    protected $actionContextInit = array();

    /**
     * The handlers for the "flush" event.
     * 
     * @var callback[]
     */
    protected $flush = array();

    /**
     * The handlers for the "end" event.
     * 
     * @var callback[]
     */
    protected $end = array();

    /**
     * The handlers for the "preSend" event.
     * 
     * @var callback[]
     */
    protected $preSend = array();

    /**
     * Application error handler.
     * 
     * @var callback[]
     */
    protected $errorHandler = array();

    /**
     * Initializes a new instance of the AppContext.
     * 
     * @param array $config The application config.
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Gets config.
     * 
     * @param string|null $key The parameter name to get. Default: null - all paramters.
     * 
     * @return array|mixed
     */
    public function getConfig($key = null) {
        return InternalHelper::getSingleKeyOrAll($this->config, $key);
    }

    /**
     * Sets config.
     * 
     * @param string|array $configOrKey The config or key to set.
     * @param array $config The config to set.
     * 
     * @return void
     */
    public function setConfig($configOrKey, $config = null) {
        if ($config !== null) {
            $this->config[$configOrKey] = $config;
        }
        else {
            $this->config = ($configOrKey !== null ? $configOrKey : array());
        }
    }

    /**
     * @return callback[]
     */
    public function getPreInit() {
        return $this->preInit;
    }

    /**
     * @return callback[]
     */
    public function getInit() {
        return $this->init;
    }

    /**
     * @return callback[]
     */
    public function getActionContextInit() {
        return $this->actionContextInit;
    }

    /**
     * @return callback[]
     */
    public function getFlush() {
        return $this->flush;
    }

    /**
     * @return callback[]
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * @return callback[]
     */
    public function getErrorHandler() {
        return $this->errorHandler;
    }

    /**
     * @return callback[]
     */
    public function getPreSend() {
        return $this->preSend;
    }

    /**
     * Adds the "preInit" handler.
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addPreInit($callback, $key = null) {
        $this->add('preInit', $callback, $key);
    }

    /**
     * Adds the "init" handler.
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addInit($callback, $key = null) {
        $this->add('init', $callback, $key);
    }

    /**
     * Adds the "actionContextInit" handler.
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addActionContextInit($callback, $key = null) {
        $this->add('actionContextInit', $callback, $key);
    }

    /**
     * Adds the "flush" handler.
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addFlush($callback, $key = null) {
        $this->add('flush', $callback, $key);
    }

    /**
     * Adds the "end" handler.
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addEnd($callback, $key = null) {
        $this->add('end', $callback, $key);
    }

    /**
     * Adds the "errorHandler".
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addErrorHandler($callback, $key = null) {
        $this->add('errorHandler', $callback, $key);
    }

    /**
     * Adds the "preSend" handler.
     * 
     * @param callback $callback The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function addPreSend($callback, $key = null) {
        $this->add('preSend', $callback, $key);
    }

    /**
     * Adds a handler.
     * 
     * @param string $eventName The event name.
     * @param callback $eventHandler The function of the handler.
     * @param string $key The unique name of the handler.
     * 
     * @return void
     */
    public function add($eventName, $eventHandler, $key = null) {
        if (!is_callable($eventHandler)) {
            throw new \Exception('Function is expected.');
        }

        if ($key !== null) {
            $this->{$eventName}[$key] = $eventHandler;
        }
        else {
            $this->{$eventName}[] = $eventHandler;
        }
    }

}