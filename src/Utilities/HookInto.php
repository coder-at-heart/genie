<?php

namespace Lnk7\Genie\Utilities;

use Lnk7\Genie\Tools;

class HookInto

{


    /**
     * An array of hooks and sequences
     *
     * @var array
     */
    protected $actions = [];


    protected $filters = [];


    protected $callback = '';


    /**
     * constructor.
     *
     * @param string $hook
     * @param int $priority
     * @param string $type
     */
    public function __construct(string $hook, int $priority = 10, $type = 'action')
    {
        $this->add($hook, $priority, $type);
    }


    /**
     * add an hook onto our $hooks array
     *
     * @param $hook
     * @param $sequence
     * @param $type
     */
    protected function add($hook, $sequence, $type)
    {
        if ($type === 'action') {
            $this->actions[$hook] = $sequence;
        } else {
            $this->filters[$hook] = $sequence;
        }
    }


    /**
     * Static constructor
     *
     * @param $action
     * @param int $priority
     *
     * @return static
     */

    public static function action(string $action, $priority = 10)
    {
        return new static($action, $priority, 'action');
    }


    /**
     * Static constructor
     *
     * @param $filter
     * @param int $priority
     *
     * @return static
     */

    public static function filter(string $filter, $priority = 10)
    {
        return new static($filter, $priority, 'filter');
    }


    /**
     * __return_true
     */
    public function returnTrue()
    {
        $this->callback = '__return_true';
        $this->register();

    }


    protected function register()
    {

        $vars = Tools::getCallableParameters($this->callback);

        foreach ($this->actions as $hook => $priority) {
            add_action($hook, $this->callback, $priority, count($vars));
        }

        foreach ($this->filters as $hook => $priority) {
            add_filter($hook, $this->callback, $priority, count($vars));
        }

    }


    /**
     * __return_false
     */
    public function returnFalse()
    {
        $this->callback = '__return_false';
        $this->register();
    }


    /**
     * Allow multiple hooks for the same action
     *
     * @param $hook
     * @param int $priority
     *
     * @return $this
     */
    public function orFilter($hook, $priority = 10)
    {
        $this->add($hook, $priority, 'filter');
        return $this;
    }


    /**
     * Allow multiple hooks for the same action
     *
     * @param $hook
     * @param int $priority
     *
     * @return $this
     */
    public function orAction($hook, $priority = 10)
    {
        $this->add($hook, $priority, 'action');
        return $this;
    }


    /**
     * Set the callback and register the actions and filters
     *
     * @param callable $callback
     */
    public function run(callable $callback)
    {

        $this->callback = $callback;
        $this->register();
    }

}