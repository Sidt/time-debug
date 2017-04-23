<?php

/**
 * @author Sebastian Schmidt
 * @author-URL: https://github.com/Sidt
 * Date: 22.04.17
 */

/**
 * Class Runner
 * This class is for the web requests. It uses curl to open the pages and collect the page load for each request.
 */
class Runner
{
    /**
     * @var array request results
     */
    private $data = array();

    /**
     * Getter for data property
     * @author Sebastian Schmidt
     * @return array data. Each row contains one page load time.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This will call the given page XX times and memorize the page load time. The amount of times is defined in the
     * configuration class (Config)
     *
     * @author Sebastian Schmidt
     * @param string $url url to open (with protocol + domain + subpaths)
     * @return $this
     */
    public function recordPage($url)
    {
        echo "Run URL: $url \n";

        for($i = 1; $i <= Config::URL_OPEN_COUNT + 1; $i++) {
            if ($i == 1) {

                // first is for cache warmup -> do not log time
                $this->loadPage($url);

            } else {

                $this->data[$i] = $this->loadPage($url);
                echo "=> Load " . ($i - 1) . " : " . $this->data[$i] . "\n";
                
            }
            // maybe better to run XX + 2 times and eliminate the lowest and highest value
            // for a better average calculation
        }
        return $this;
    }

    /**
     * Open the given page with curl and save the needed page load time.
     *
     * @author Sebastian Schmidt
     * @param string $url url with protocol and domain
     * @return float page load time
     */
    private function loadPage($url)
    {
        $ch = curl_init();

        // set curl options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // get start time
        $startTime = microtime(true);

        // do web call
        $head = curl_exec($ch);

        // calculate needed time
        $duration = microtime(true) - $startTime;

        // special: check if call was succesfully
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode > 301) {
            echo "ERROR: URL open failed: $url (Code: $httpCode)";
            die;
        }

        // cleanup
        curl_close($ch);

        return $duration;
    }
}