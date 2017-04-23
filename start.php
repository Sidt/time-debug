<?php
/**
 * @author Sebastian Schmidt
 * @author-URL: https://github.com/Sidt
 * Date: 22.04.17
 * Time: 21:52
 */

require_once('Config.php');
require_once('Runner.php');
require_once('Statistic.php');

$cmsType = Config::TYPE;
require_once("$cmsType.php");

$cmsBaseClass = new $cmsType(Config::CMS_ROOT);

$statistic = new Statistic();

$runner = new Runner();

echo "###########\n";
echo "## START ##\n";
echo "###########\n";

flush();

echo "==> CONFIG-INFORMATIONS <==\n";
echo "CMS: $cmsType \n";
echo "CMS-Root-Path: " . Config::CMS_ROOT . "\n";
echo "Plugins to check: " . implode(', ', Config::$pluginsList) . "\n";
echo "\n";

flush();

echo "==> INIT <==\n";
echo "Activate all plugins for base time checks.\n";
foreach(Config::$pluginsList AS $pluginSubPath) {
    $cmsBaseClass
        ->activateModule($pluginSubPath);
}

flush();

echo "Run Init Pageload record\n";
foreach(Config::$urlList AS $url) {

    $statistic->addSection('Pageload with all Plugins Loaded');
    $runner->recordPage(Config::BASE_URL . $url);

    $statistic->addBaseResults($url, $runner->getData());
}

flush();

echo "Deactivate all listed plugins\n";
foreach(Config::$pluginsList AS $pluginSubPath) {
    $cmsBaseClass
        ->deactivateModule($pluginSubPath);
}

flush();

echo "Run Pageload with enable modules step by step\n";
foreach(Config::$pluginsList AS $pluginSubPath) {

    $cmsBaseClass->activateModule($pluginSubPath);

    echo "module: " . dirname($pluginSubPath) . "\n";

    $statistic->addSection('Pageload with plugin ' . dirname($pluginSubPath));

    foreach (Config::$urlList AS $url) {

        $runner->recordPage(Config::BASE_URL . $url);
        //var_dump($runner->getData());die;

        $statistic->addResults($url, $runner->getData());
    }

    flush();
}

$statistic->renderResults();
