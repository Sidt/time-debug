<?php
/**
 * @author Sebastian Schmidt
 * @author-URL: https://github.com/Sidt
 * Date: 22.04.17
 */
class Config
{
    /**
     * Type of CMS system. This should be the same spelling as the file you need under /cms/....php.
     * Will be used to load the correct cms modul
     */
    const TYPE = 'CMS';
    /**
     * Absolute path to the CMS root.
     */
    const CMS_ROOT = '/path/to/cms/root';
    /**
     * protocol and domain and if needed subpath to CMS root for web call.
     */
    const BASE_URL = 'http://CMS-DOMAIN.DE';
    /**
     * The statistic csv file will be written here.
     */
    const RESULT_FILE = 'result.csv';
    /**
     * count of web calls for each url.
     */
    const URL_OPEN_COUNT = 10;

    /**
     * @var array list of urls (sub/script paths) to check. On call the domain will be added automatically.
     */
    public static $urlList = array(
        '/',
        '/sub/page'
    );

    /**
     * @var array list of plugins to control the pageload when they are toggled.
     * structure is depends on the cms system.
     * WORDPRESS:
     * 'PATH/FILE':
     *   - PATH: dirname of module
     *   - FILE: main php file of the plugin (contains the "Plugin Name:" comment code)
     * Example:
     * 'contact-form-7/wp-contact-form-7.php'
     * (Other CMS will follow)
     */
    public static $pluginsList = array(



    );

}