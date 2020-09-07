<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Plugins\ACF;
use Lnk7\Genie\Traits\HasData;
use Lnk7\Genie\Utilities\HookInto;

/**
 * Class Genie
 *
 * @package Lnk7\Genie
 * @property string $type
 * @property array $components
 * @property string $filename
 * @property array $viewFolders
 */
class Genie
{


    use HasData;

    /**
     * Genie constructor.
     */
    function __construct()
    {
        $this->fill([
            'type'        => 'plugin',  // plugin || theme
            'filename'    => false,
            'components'  => [
                Session::class,
                AjaxHandler::class,
                WordPress::class,
                BackgroundJob::class,
                View::class,
                CacheBust::class,
            ],
            'viewFolders' => [],
        ]);
    }


    public static function createPlugin()
    {
        $genie = new static();
        $genie->type('plugin');
        return $genie;
    }


    /**
     * Set how genie is being used (defaults to plugin)
     *
     * @param string $type
     *
     * @return $this
     */
    public function type(string $type)
    {
        $this->type = $type;
        return $this;
    }


    public static function createTheme()
    {
        $genie = new static();
        $genie->type('theme');
        return $genie;
    }


    public static function activation()
    {
        do_action('genie_activation');
        flush_rewrite_rules();
    }


    public static function uninstall()
    {
        do_action('genie_uninstall');
    }


    public static function deactivation()
    {
        do_action('genie_deactivation');
    }


    /**
     * get a value from Genie's COnfig
     *
     * @param $var
     *
     * @return mixed|null
     */
    public static function config($var)
    {
        return Registry::get('genie_config', $var);
    }


    /**
     * Add a bunch  of components that should be loaded by Genie
     *
     * @param array $components
     *
     * @return $this
     */
    public function withComponents(array $components)
    {
        $this->components = array_merge($this->components, $components);
        return $this;
    }


    /**
     * Add a folder to the array of view Folders
     *
     * @param string $folder
     *
     * @return $this
     */
    public function useViewsFrom(string $folder)
    {
        $this->viewFolders = array_merge($this->viewFolders, [$folder]);
        return $this;
    }


    /**
     * Set the __FILE__ for the plugin - this is needed for activation, deactivation and uninstall hooks
     *
     * @param $filename
     *
     * @return $this
     */
    public function setFilename($filename)
    {
        $config['filename'] = $filename;
        return $this;
    }


    /**
     * get Genie Going.
     */
    public function start()
    {
        // We can't do anything without ACF
        if (ACF::isDisabled()) {
            return;
        }

        $config = apply_filters('genie_config', $this->getData());

        //Load all our classes.
        if (is_array($config['components']))
            foreach ($config['components'] as $class) {
                if (method_exists($class, 'Setup')) {
                    $class::Setup();
                }
            }

        // Register hooks
        if ($config['type'] === 'plugin') {
            if (isset($config['filename']) && $config['filename']) {
                register_activation_hook($config['filename'], [static::class, 'activation']);
                register_deactivation_hook($config['filename'], [static::class, 'deactivation']);
                register_uninstall_hook($config['filename'], [static::class, 'uninstall']);
            }
        } else {
            HookInto::action('after_setup_theme')
                ->run([static::class, 'activation']);

            HookInto::action('switch_theme')
                ->run([static::class, 'deactivation']);
        }
        // Send a message to the outside world!
        do_action('genie_started');

        Registry::set('genie_config', $config);
    }

}