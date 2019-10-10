<?php

use Codeception\Actor;
use Codeception\Lib\Friend;
use Codeception\PHPUnit\Constraint\JsonType as JsonTypeConstraint;
use Helper\Utils;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions;
    use Codeception\Util\Shared\Asserts;

    public function getHeaderValue($header)
    {
        return $this->grabHttpHeader($header);
    }

    /**
     * Send POST request to given path
     *
     * @param string $path API's path
     * @param array $headers
     * @param string $filename
     * @param array $fields
     * @return void
     */
    public function sendPOSTWithFile($path, $headers, $filename, $fields = [])
    {
        $endpoint = $this->getCurrentConfigUrl() . $path;

        // Create a CURLFile object / procedural method
        $cFile = curl_file_create(codecept_data_dir($filename));

        // Assign POST data
        $data = array('file' => $cFile);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array_merge($data, $fields));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false); // set to true if you want to get full message
        $response = curl_exec($curl);
        curl_close($curl);

        $this->assertTrue($response);
    }

    public function getCurrentConfigUrl()
    {
        return $this->getScenario()->current('modules')['PhpBrowser']->_getConfig('url');
    }

    public function getResponseStatus()
    {
        return $this->getScenario()->current('modules')['PhpBrowser']->_getResponseCode();
    }

    /**
     * Assert that the JSON response has a given structure.
     *
     * @param array|null $structure
     * @param array|null $responseData
     * @return $this
     */
    public function seeJsonStructure(array $structure, $responseData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $responseData);

                foreach ($responseData as $responseDataItem) {
                    $this->seeJsonStructure($structure['*'], $responseDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $responseData);
                $this->seeJsonStructure($structure[$key], $responseData[$key]);
            } else {
                $this->assertArrayHasKey($value, $responseData);
            }
        }

        return $this;
    }

    /**
     * Get value from endpoint with query string
     *
     * @param $endpoint
     * @param $param
     * @return mixed
     */
    public function grabEndpointParameterValue($endpoint, $param)
    {
        parse_str(parse_url($endpoint, PHP_URL_QUERY), $output);
        return $output[$param];
    }

    /**
     * @param $bugID string example: 'TT-1234'
     * Comment out: Bug URL - Status - Summary
     */
    public function noticedBug($bugID)
    {
        $headers = array(
            'Authorization: Basic <token>',
            'Content-Type: application/json'
        );
        $response = $this->sendGETByCURL('https://org.atlassian.net/rest/api/2/issue/' . $bugID, $headers)['data'];
        $summary = $response['fields']['summary'];
        $status = $response['fields']['status']['name'];
        switch (strtolower($status)) {
            case 'resolved':
            case 'closed':
                $backColor = 'green';
                $color = 'white';
                break;
            case 'in progress':
            case 'in review':
            case 'verified':
                $backColor = 'blue';
                $color = 'white';
                break;
            case 'open':
            case 'reopened':
                $backColor = 'lightgrey';
                $color = 'black';
                break;
            default:
                $backColor = '';
                $color = '';
                break;
        }

        $this->comment('<a target="_blank" rel="noopener noreferrer" href=https://org.atlassian.net/browse/' . $bugID . '>' . $bugID
            . '</a> <h style="background-color:' . $backColor . '; color: ' . $color . '">' . strtoupper($status) . '</h> ' . $summary);
    }

    /**
     * @param $path string e.g. /API/%s/sessions/%s
     * @param $headers array e.g. array(Api::HEADER_ACCESS_TOKEN . ': ' . $this->sessionInfo['accessToken']);
     * @return array
     */
    public function sendGETByCURL($path, $headers)
    {
        $path = str_replace(' ', '%20', $path);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $path);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // stop verifying certificate
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // set to true if you want to get full message
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $httpContentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);

        $ret = [];
        $ret['data'] = json_decode($response, true);
        $ret['httpCode'] = $httpCode;
        $ret['contentType'] = $httpContentType;

        return $ret;
    }

    /**
     * Working same with canSeeResponseMatchesJsonType, just count 1 failures on HTML report
     * @param array $jsonType
     * @param null $jsonPath
     * @param $failures
     * @throws Exception
     */
    public function canSeeResponseMatchesJsonTypeV2(array $jsonType, $jsonPath, &$failures)
    {
        try {
            if ($jsonPath) {
                $observed = $this->grabDataFromResponseByJsonPath($jsonPath);
            } else {
                $observed = \GuzzleHttp\json_decode($this->grabResponse(), true);
            }

            Assert::assertThat($observed, new JsonTypeConstraint($jsonType));
        } catch (ExpectationFailedException $f) {
            $message = $f->getMessage();
            if (!Utils::inArrayContains($message, $failures)) {
                array_push($failures, $message);
            }
        }
    }

    /**
     * @param $urls
     */
    public function canSeeURLsWork($urls)
    {
        if (empty($urls)) return;

        foreach ($urls as $url) {
            $this->comment($url);
            $this->sendGET($url);
            $this->canSeeResponseCodeIs(200);
        }
    }
}
