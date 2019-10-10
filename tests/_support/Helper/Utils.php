<?php

namespace Helper;

use DateTime;
use DateTimeZone;
use DOMDocument;
use DOMXPath;
use ZipArchive;

class Utils
{
    /**
     * @param $string
     * @param $start
     * @param $end
     * @return bool|string
     */
    public static function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     *
     * @param string $file
     * @param int $timeWait seconds
     * @return boolean
     */
    public static function doesFileExist($file, $timeWait)
    {
        $fileExists = file_exists($file);
        while (!$fileExists && $timeWait > 0) {
            sleep(1);
            $fileExists = file_exists($file);
            $timeWait -= 1;
        }

        return $fileExists;
    }

    public static function getDateTimeWithStrTz($strTimezone, $timeStamp, $returnFormat)
    {
        $dt = new DateTime("now", new DateTimeZone($strTimezone)); //first argument "must" be a string
        $dt->setTimestamp($timeStamp); //adjust the object to correct timestamp
        return $dt->format($returnFormat);
    }

    /**
     * @param $html
     * @return DOMXpath
     */
    public static function htmlToDOMXpath($html)
    {
        return new DOMXpath(self::htmlToDOM($html));
    }

    /**
     * @param $html
     * @return DOMDocument
     */
    public static function htmlToDOM($html)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        return $dom;
    }

    /**
     * @param $csv array e.g.
     * [
     *      ["hostname","instanceNr"]
     *      ["sapepp","0"]
     *      ["sapepp","1"]
     * ]
     * @return string JSON e.g.
     * [
     * {
     * "hostname": "sapepp",
     * "instanceNr": "0"
     * },
     * {
     * "hostname": "sapepp",
     * "instanceNr": "1"
     * }
     * ]
     */
    public static function csvToJson($csv)
    {
        $data = [];
        $column_names = [];
        foreach ($csv[0] as $single_csv) {
            $column_names[] = $single_csv;
        }

        foreach ($csv as $key => $row) {
            if ($key === 0) {
                continue;
            }

            foreach ($column_names as $column_key => $column_name) {
                $data[$key - 1][$column_name] = $row[$column_key];
            }
        }

        return json_encode($data);
    }

    public static function deleteElementInArrayByName(&$array, $name)
    {
        if (($key = array_search($name, $array)) !== false) {
            unset($array[$key]);
        }
    }

    /**
     * Compare 2 multidimensional arrays
     * @return array the difference
     */
    public static function checkDiffMulti($array1, $array2)
    {
        $result = [];
        foreach ($array1 as $key => $val) {
            if (array_key_exists($key, $array2)) {
                if (is_array($val) || is_array($array2[$key])) {
                    if (false === is_array($val) || false === is_array($array2[$key])) {
                        $result[$key] = $val;
                    } else {
                        $result[$key] = self::checkDiffMulti($val, $array2[$key]);
                        if (sizeof($result[$key]) === 0) {
                            unset($result[$key]);
                        }
                    }
                }
            } else {
                $result[$key] = $val;
            }
        }

        return $result;
    }

    /**
     * Checking elements in array contain string
     * @param $string
     * @param $array
     * @return bool
     */
    public static function inArrayContains($string, $array)
    {
        foreach ($array as $element) {
            if (strpos($string, $element) !== FALSE || strpos($element, $string) !== FALSE) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @param int $precision
     * @return int|string
     */
    public static function round($value, $precision = 0)
    {
        if (!empty($value) && is_numeric($value)) {
            $value = (float)number_format($value, $precision, '.', '');
        }
        return $value;
    }

    public static function getTimeZoneOffSet($timezone)
    {
        $dtZone = new DateTimeZone($timezone);
        $offset = $dtZone->getOffset(new DateTime('now', new DateTimeZone('UTC'))) / 3600;

        return $offset;
    }

    public static function stripData($data)
    {
        $data = str_replace(array("\r\n", "\r", "\n", "\t", " "), ' ', $data);
        $data = preg_replace('/(\s){2,}/', ' ', $data);
        $data = trim($data);

        return $data;
    }

    public static function getDownloadDirectoryPath()
    {
        return $_SERVER['USERPROFILE'] ?? $_SERVER['HOME'];
    }

    public static function unzipFile($filePath, $destination)
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            $zip->extractTo($destination);
            $zip->close();
            return true;
        }

        return false;
    }

    /**
     * Recursively remove a directory
     * @param $dir String path to directory
     */
    public static function deleteDirectory($dir)
    {
        if (!is_dir(($dir))) return;

        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (is_dir($dir . '/' . $object)) {
                    self::deleteDirectory($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }

        rmdir($dir);
    }

    /**
     * strpost with $needles like array
     * @param $string
     * @param $array
     * @param $offset
     * @return bool
     */
    function strposa($string, $needles = array(), $offset = 0)
    {
        $chr = array();
        foreach ($needles as $needle) {
            $res = strpos($string, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
        }
        if (empty($chr)) return false;

        return min($chr);
    }
}
