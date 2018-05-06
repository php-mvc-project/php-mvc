<?php
namespace PhpMvc;

/**
 * Represents the application context.
 */
class AppContext {

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
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Sets config.
     * 
     * @param array $config The config to set.
     * 
     * @return void
     */
    public function setConfig($config) {
        $this->config = $config;
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
            $this->$eventName[$key] = $eventHandler;
        }
        else {
            $this->$eventName[] = $eventHandler;
        }
    }

}