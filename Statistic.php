<?php

/**
 * @author Sebastian Schmidt
 * @author-URL: https://github.com/Sidt
 * Date: 22.04.17
 */
class Statistic
{
    /**
     * @var array data for current run
     */
    private $data = array();
    /**
     * @var string current section name. Will be used as headline for different results in the exported csv.
     */
    private $currentSection = '';
    /**
     * @var array the results for the base call. Will be used for comparison and statistic results
     */
    private $baseData = array();
    /**
     * @var array temporary save the last results for comparison.
     */
    private $lastResults = array();

    /**
     * Setter for current section
     *
     * @author Sebastian Schmidt
     * @param $sectionName
     * @return $this
     */
    public function addSection($sectionName)
    {
        $this->currentSection = $sectionName;
        return $this;
    }

    /**
     * Register and prepare the base results for a given url.
     *
     * @see prepareAndRegisterResults
     * @author Sebastian Schmidt
     * @param string $url url
     * @param array $results page load time results. Each entry contains one time.
     * @return $this
     */
    public function addBaseResults($url, $results)
    {
        $this->baseData[$url] = $this->prepareAndRegisterResults($url, $results);
        $this->lastResults[$url] = $this->baseData[$url];

        return $this;
    }

    /**
     * Register and prepare the results for a given url.
     *
     * @see prepareAndRegisterResults
     * @author Sebastian Schmidt
     * @param string $url url
     * @param array $results page load time results. Each entry contains one time.
     * @return $this
     */
    public function addResults($url, $results)
    {
        $this->lastResults[$url] = $this->prepareAndRegisterResults($url, $results);

        return $this;
    }

    /**
     * Prepare the results:
     * - compare with base results
     * - compare with last run for this url with different activated modules
     * - calculate average of results
     * - calculate percentage to total time
     *
     * @author Sebastian Schmidt
     * @param string $url url
     * @param array $results page load time results. Each entry contains one time.
     * @return mixed
     */
    public function prepareAndRegisterResults($url, $results)
    {

        /** @var stdClass $resultObject result object */
        $resultObject = new stdClass();
        $resultObject->url = $url;

        // register results
        $allTime = 0.00;
        $callCount = 0.00;
        $resultArray = array();
        foreach($results AS $time) {
            $resultArray[] = floatval($time);
            $allTime += floatval($time);
            $callCount += 1.00;
        }
        $resultObject->results = $resultArray;

        // calculate average
        $resultObject->average = $allTime / $callCount;

        // compare with last run
        // additional time needed only by this request (compare with last run)
        $resultObject->partOfTotal = $resultObject->average;
        if (isset($this->lastResults[$url])) {
            // calculate the difference of page load time to the last run
            $resultObject->partOfTotal = $resultObject->average - $this->lastResults[$url]->average;
        }

        // get pageload time in percentage compared to total value
        $resultObject->percentageOfTotal = 1.00;
        $baseValue = $this->getBaseValueForUrl($url);
        if ($baseValue) {
            // calculate the difference of page load time to the last run in percentage
            $resultObject->percentageOfTotal = $resultObject->partOfTotal / $baseValue->average * 100;
        }

        // save prepared data
        $this->data[$this->currentSection][$url] = $resultObject;

        return $this->data[$this->currentSection][$url];
    }

    /**
     * Check whether base results for this url are recorded already.
     *
     * @author Sebastian Schmidt
     * @param string $url url
     * @return mixed|null if base results are given then return the result object. else null
     */
    private function getBaseValueForUrl($url)
    {
        if (!isset($this->baseData[$url])) {
            return null;
        }
        return $this->baseData[$url];
    }

    /**
     * This will write all data to the result file given in the Config class.
     *
     * @author Sebastian Schmidt
     */
    public function renderResults()
    {
        // file handler
        $fh = fopen(Config::RESULT_FILE, 'w');

        // generate header
        $headline = array();
        $headline[] = 'URL';
        for ($i = 1; $i <= Config::URL_OPEN_COUNT; $i++) {
            $headline[] = "Load $i";
        }
        $headline[] = "Load average";
        $headline[] = "Load time of this module";
        $headline[] = "Percentage of total";

        fputcsv($fh, $headline);

        // export all saved data by sections
        foreach($this->data AS $sectionName => $sectionData) {

            // write section name
            fputcsv($fh, array(""));
            fputcsv($fh, array($sectionName));

            // export all urls for this section
            foreach($sectionData AS $url => $resultObject) {

                $rowResults = array();
                // begin with url name for first column.
                $rowResults[] = $url;

                // write all results
                foreach($resultObject->results AS $result) {

                    // format numbers to only display last two decimals
                    $rowResults[] = sprintf("%.2f", $result);

                }

                // add calculated values
                $rowResults[] = sprintf("%.2f", $resultObject->average);
                $rowResults[] = sprintf("%.2f", $resultObject->partOfTotal);
                $rowResults[] = sprintf("%.2f", $resultObject->percentageOfTotal);

                fputcsv($fh, $rowResults);

            }


        }

        // close file
        fclose($fh);

    }
}