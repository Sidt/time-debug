<?php
/**
 * @author Sebastian Schmidt
 * @author-URL: https://github.com/Sidt
 * Date: 22.04.17
 */
require_once(__DIR__ . '/../Config.php');

/**
 * Class CMS
 * This is the base modul for communication with cms systems.
 */
abstract class CMS
{

    protected $pathRoot;

    /**
     * Wordpress constructor.
     * @param string $wpRoot absolute path to wordpress installation directory.
     */
    public function __construct($pathToRoot)
    {
        $this->pathRoot = $pathToRoot;
        return $this;
    }

    /**
     * Deactivate a given module of the cms. The details will be done in the assigned cms class.
     *
     * @author Sebastian Schmidt
     * @param string $pluginId
     * @return $this
     */
    abstract public function deactivateModule($pluginId);

    /**
     * Activate a given module of the cms. The details will be done in the assigned cms class.
     *
     * @author Sebastian Schmidt
     * @param string $pluginId
     * @return $this
     */
    abstract public function activateModule($pluginId);

}
