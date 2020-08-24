<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/15
 * Time: 11:18
 */

namespace App\Utility;

use Exception;

class JsonHelper
{
    /**
     * Json encode/decode error messages
     *
     * @var array
     */
    protected static $messages = [
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    ];

    /**
     * Encode data to json
     *
     * @param mixed $value Data to be encoded
     * @param mixed $options Json constants
     *
     * @return string
     * @throws Exception
     */
    public static function encode($value, $options = 0): string
    {
        $result = json_encode($value, $options);

        $lastError = json_last_error();
        if ($lastError !== JSON_ERROR_NONE) {
            throw new Exception(static::$messages[$lastError]);
        }

        if ($result === false) {
            throw new Exception('Json encode problem');
        }

        return $result;
    }

    /**
     * Decode from Json
     *
     * @param string $json Json string to be decoded
     * @param bool $assoc Should objects be converted to associative arrays
     *
     * @return mixed
     * @throws Exception
     */
    public static function decode(string $json, bool $assoc = true)
    {
        $result = json_decode($json, $assoc);
        $lastError = json_last_error();
        if ($lastError === JSON_ERROR_NONE) {
            return $result;
        }
        throw new Exception(static::$messages[$lastError]);
    }

    /**
     * @param mixed|string $strJson JSON to be validated
     *
     * @return bool
     */
    public static function isValidJson($strJson): bool
    {
        if (!is_string($strJson)) {
            return false;
        }
        json_decode($strJson);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    /**
     * JsonHelper::convertNewLinesToCRLF(array('wo\x0D\x0Arks' => 'fi\x0D\x0Ane'));
     * @param string|array $item Data to replace lines
     *
     * @return string|array
     */
    public static function convertNewLinesToCRLF($item)
    {
        if (is_string($item)) {
            return static::replaceNewLines($item);
        }
        if (is_array($item)) {
            array_walk_recursive($item, function (&$value, &$key) {
                if (is_string($key)) {
                    $key = static::replaceNewLines($key);
                }
                if (is_string($value)) {
                    $value = static::replaceNewLines($value);
                }
            });
        }
        return $item;
    }
    /**
     * @param string $string String for replacing new lines
     *
     * @return string
     */
    private static function replaceNewLines(string $string): string
    {
        $replaced = preg_replace('~\R~u', "\r\n", $string);

        if ($replaced === null) {
            return $string;
        }

        return $replaced;
    }
}
