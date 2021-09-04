<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      HighlightDocument.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   HighlightDocument.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

namespace App\Http\Classes;

/**
 * @uses
 */
use App\Helpers\MongoHelper;
use App\Http\Classes\ExportDocument;

/**
 * Class HighlightDocument
 * @package App\Http\Classes
 */
class HighlightDocument
{
    /**
     * @var
     */
    private $render;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @return mixed
     */
    private function getRender()
    {
        return $this->render;
    }

    /**
     * @param mixed $render
     */
    private function setRender($render): void
    {
        $this->render = $render;
    }

    /**
     * Set an Object ID
     *
     * @param   array $arr
     * @return  string
     */
    private function setObjectId(array $arr): string
    {
        $id = 'n/a';
        if (isset($arr[2]) && false === strpos($arr[2], '"')) {
            $id = $arr[2];
        }
        return '<span class="string" style="color: blue">ObjectId</span>' .
            '<span style="color: blue">(</span>' .
            $this->wrapQuotes($id) .
            '<span style="color: blue">)</span>';
    }

    /**
     * Wrap a key value
     *
     * @param   array   $arr
     * @return  string
     */
    private function setKeyWrapper(array $arr): string
    {
        $key = 'n/a';
        if (isset($arr[2]) && false === strpos($arr[2], '"')) {
            $key = $arr[2];
        }
        return '<span class="field" data-field="' . $key . '">' .
            $this->wrapQuotes($key) .
            '</span>';
    }

    /**
     * Analyse a value element
     *
     * @param   array $arr
     * @return  string
     */
    private function analyseValue(array $arr): string
    {
        $value = 'n/a';
        if (isset($arr[2]) && false === strpos($arr[2], '"')) {
            $value = $arr[2];
        }
        if (is_scalar($value) || is_null($value)) {
            switch (gettype($value)) {
                case "integer":
                    return '<span class="string" style="color: blue">NumberInt</span>' .
                        '<span style="color: blue">(</span>' .
                        '<span class="value">' .
                        $this->wrapQuotes($value) .
                        '</span>' .
                        '<span style="color: blue">)</span>';
                default:
                    return $this->wrapValue($value);
            }
        }
        // default for the moment
        return $this->wrapValue($value);
    }

    /**
     * Wrap a value with value class span
     *
     * @param   $value
     * @return  string
     */
    private function wrapValue($value): string
    {
        return '<span class="value">' .
            $this->wrapQuotes($value) .
            '</span>';
    }

    /**
     * Wrap with quotes
     *
     * @param   mixed $value
     * @return  string
     */
    private function wrapQuotes($value): string
    {
        return '"' . $value . '"';
    }

    /**
     * Wrap a bracket
     *
     * @param   mixed   $char
     * @return  string
     */
    private function wrapBracket($char): string
    {
        return '<span style="color: green">' . $char . '</span>';
    }

    /**
     * Wrap a colon
     *
     * @param   string|null $input
     * @return  string
     */
    private function wrapColon(?string $input): string
    {
        return $input ? $input . '<span style="color: green">:</span>' : '<span style="color: green">:</span>';
    }

    /**
     * Default value wrap method with fixed color
     *
     * @param   $value
     * @return  string
     */
    private function wrapValueDefault($value)
    {
        return '<span class="value" style="color: #DD0000">' . $value . '</span>';
    }

    /**
     * Wrap value with red class
     *
     * @param   mixed   $value
     * @return  string
     */
    private function wrapValueRed($value)
    {
        return '<span class="value" style="color: red">' . $value . '</span>';
    }

    /**
     * Add a comma
     *
     * @param   string|null $input
     * @return  string
     */
    private function addComma(?string $input)
    {
        return $input ? $input . "," : ",";
    }

    /**
     * Add a break
     *
     * @param   string|null $input
     * @return  string
     */
    private function addBr(?string $input)
    {
        return $input ? $input . '<br>' : '<br>';
    }

    /**
     * Add a tab
     *
     * @param   string|null $input
     * @return  string
     */
    private function addTab(?string $input)
    {
        return $input . '&nbsp;&nbsp;';
    }

    /**
     * HighlightDocument constructor.
     * @param string $render
     */
    public function __construct(string $render = 'default')
    {
        $this->setRender($render);
    }

    /**
     * Export var as string then highlight it.
     *
     * @param   mixed       $var        variable to be exported
     * @param   string      $format     data format, array|json
     * @param   boolean     $label      if add label to field
     * @return  string
     */
    public function highlight($var, string $format = "array", bool $label = false): string
    {
        $json = json_encode($var);
        $output = MongoHelper::jsonFormatHtml($json);

        // set the Object ID
        $output = preg_replace_callback(
            "/(['\"])id\\.(.+)\\.id(['\"])/U",
            function ($match) {
                return $this->setObjectId($match);
            },
            $output
        );

        // set all 'key' | 'field' wrappers
        $output = preg_replace_callback(
            "/(['\"])key\\.(.+)\\.key(['\"])/U",
            function ($match) {
                return $this->setKeyWrapper($match);
            },
            $output
        );

        // set all 'value' wrappers - perform minor content analysis
        return preg_replace_callback(
            "/(['\"])value\\.(.+)\\.value(['\"])/U",
            function ($match) {
                return $this->analyseValue($match);
            },
            $output
        );
    }
}
