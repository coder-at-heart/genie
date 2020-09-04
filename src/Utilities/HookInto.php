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



    /**
     * constructor.
     *
     * @param string $hook
     * @param int $sequence
     * @param string $type
     */
    public function __construct(string $hook, int $sequence = 10, $type = 'action')
    {
        $this->add($hook, $sequence, $type);

    }



    /**
     * Static constructor
     *
     * @param $action
     * @param int $sequence
     *
     * @return static
     */

    public static function action(string $action, $sequence = 10)
    {
        return new static($action, $sequence, 'action');

    }



    /**
     * Static constructor
     *
     * @param $filter
     * @param int $sequence
     *
     * @return static
     */

    public static function filter(string $filter, $sequence = 10)
    {
        return new static($filter, $sequence, 'filter');
    }



    /**
     * Allow multiple hooks for the same action
     *
     * @param $hook
     * @param int $sequence
     *
     * @return $this
     */
    public function orFilter($hook, $sequence = 10)
    {
        $this->add($hook, $sequence, 'filter');
        return $this;
    }



    /**
     * Allow multiple hooks for the same action
     *
     * @param $hook
     * @param int $sequence
     *
     * @return $this
     */
    public function orAction($hook, $sequence = 10)
    {
        $this->add($hook, $sequence, 'action');
        return $this;

    }



    /**
     * Set the callback and register the actions and filters
     *
     * @param  $callback
     */
    public function run($callback)
    {
        $vars = Tools::getCallableVariables($callback);

        foreach ($this->actions as $hook => $sequence) {
            add_action($hook, $callback, $sequence, count($vars));
        }

        foreach ($this->filters as $hook => $sequence) {
            add_filter($hook, $callback, $sequence, count($vars));
        }

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


}