<?php
/**
 * @author Sebastian Schmidt
 * @author-URL: https://github.com/Sidt
 * Date: 22.04.17
 */
require_once(__DIR__ . '/../Config.php');
require_once('CMS.php');

/**
 * Class Wordpress
 * This module is for the communication with wordpress to control the module activation.
 */
class Wordpress extends CMS
{

    const LOAD_WP = "/wp-load.php";
    const PLUGIN_PHP = "/wp-admin/includes/plugin.php";
    const PATH_WP_CONTENT = '/wp-content';
    const PATH_PLUGINS = '/plugins';
    protected $pathRoot;
    private $pathContent;
    private $pathPlugins;

    /**
     * Wordpress constructor.
     * @param string $wpRoot absolute path to wordpress installation directory.
     */
    public function __construct($wpRoot)
    {
        parent::__construct($wpRoot);

        $this->init();

        return $this;
    }

    /**
     * Initialize this modul for wordpress by loading the wp core.
     *
     * @author Sebastian Schmidt
     * @return $this
     */
    public function init()
    {
        // set some paths
        $this->pathContent = $this->pathRoot . self::PATH_WP_CONTENT;
        $this->pathPlugins = $this->pathRoot . self::PATH_WP_CONTENT . self::PATH_PLUGINS;

        // load some files required to work with plugins functions.
        require_once($this->pathRoot . self::LOAD_WP);
        require_once($this->pathRoot . self::PLUGIN_PHP);

        return $this;
    }

    /**
     * Deactivate a given wordpress plugin by using wp plugin core functions.
     * Require the dirname/main-php-file.php as string as used by the wordpress function
     * "deactivate_plugins"
     *
     * @author Sebastian Schmidt
     * @param string $pluginSubPath dirname/main-php-file.php
     * @return $this
     */
    public function deactivateModule($pluginSubPath)
    {
        if(is_plugin_active($pluginSubPath)) {
            deactivate_plugins($this->pathPlugins . "/" . $pluginSubPath);
        }

        return $this;

    }

    /**
     * Activate a given wordpress plugin by using wp plugin core functions.
     * Require the dirname/main-php-file.php as string as used by the wordpress function
     * "deactivate_plugins"
     *
     * @author Sebastian Schmidt
     * @param string $pluginSubPath dirname/main-php-file.php
     * @return $this
     */
    public function activateModule($pluginSubPath)
    {
        if(!is_plugin_active($pluginSubPath)) {
            activate_plugins($this->pathPlugins . "/" . $pluginSubPath);
        }

        return $this;

    }

}
