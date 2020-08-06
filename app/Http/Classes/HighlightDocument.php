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
    private $_render;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @return mixed
     */
    private function _getRender()
    {
        return $this->_render;
    }

    /**
     * @param mixed $render
     */
    private function _setRender($render): void
    {
        $this->_render = $render;
    }

    /**
     * HighlightDocument constructor.
     * @param string $render
     */
    public function __construct(string $render = 'default')
    {
        $this->_setRender( $render );
    }

    /**
     * Export var as string then highlight it.
     *
     * @param   mixed       $var        variable to be exported
     * @param   string      $format     data format, array|json
     * @param   boolean     $label      if add label to field
     * @return  string
     */
    public function highlight( $var, $format = "array", $label = false)
    {
        $json = json_encode( $var );
        $output = MongoHelper::json_format_html( $json);

        // set the Object ID
        $output = preg_replace_callback("/(['\"])id\\.(.+)\\.id(['\"])/U", function($match) {
            return $this->_setObjectId( $match );

        }, $output);

        // set all 'key' | 'field' wrappers
        $output = preg_replace_callback("/(['\"])key\\.(.+)\\.key(['\"])/U", function($match) {
            return $this->_setKeyWrapper( $match );

        }, $output);

        // set all 'value' wrappers - perform minor content analysis
        $output = preg_replace_callback("/(['\"])value\\.(.+)\\.value(['\"])/U", function($match) {
            return $this->_analyseValue( $match );

        }, $output);

        return $output;
    }

    /**
     * Set an Object ID
     *
     * @param array $arr
     * @return string
     */
    private function _setObjectId(array $arr )
    {
        $id = 'n/a';
        if (isset($arr[2]) && strpos($arr[2], '"') === false) {
            $id = $arr[2];
        }
        return '<span class="string" style="color: blue">ObjectId</span>' .
            '<span style="color: blue">(</span>' .
            $this->_wrapQuotes( $id ) .
            '<span style="color: blue">)</span>';
    }

    /**
     * Wrap a key value
     *
     * @param array $arr
     * @return string
     */
    private function _setKeyWrapper( array $arr)
    {
        $key = 'n/a';
        if (isset($arr[2]) && strpos($arr[2], '"') === false) {
            $key = $arr[2];
        }
        return '<span class="field" data-field="' . $key . '">' .
            $this->_wrapQuotes( $key ) .
            '</span>';
    }

    /**
     * Analyse a value element
     *
     * @param array $arr
     * @return string
     */
    private function _analyseValue(array $arr)
    {
        $value = 'n/a';
        if (isset($arr[2]) && strpos($arr[2], '"') === false) {
            $value = $arr[2];
        }
        if (is_scalar($value) || is_null($value)) {
            switch (gettype($value)) {
                case "integer":
                    return '<span class="string" style="color: blue">NumberInt</span>' .
                        '<span style="color: blue">(</span>' .
                        '<span class="value">' .
                        $this->_wrapQuotes( $value ) .
                        '</span>' .
                        '<span style="color: blue">)</span>';
                default:
                    return $this->_wrapValue( $value );
            }
        }
        // default for the moment
        return $this->_wrapValue( $value );
    }

    /**
     * Wrap a value
     *
     * @param $value
     * @return string
     */
    private function _wrapValue( $value)
    {
        return '<span class="value">' .
            $this->_wrapQuotes( $value ) .
            '</span>';
    }

    /**
     * Wrap with quotes
     *
     * @param $value
     * @return string
     */
    private function _wrapQuotes( $value ) {
        return '"' . $value . '"';
    }

    /**
     * Wrap a bracket
     *
     * @param $char
     * @return string
     */
    private function _wrapBracket( $char )
    {
        return '<span style="color: green">' . $char . '</span>';
    }

    /**
     * Wrap a colon
     *
     * @param bool $input
     * @return string
     */
    private function _wrapColon( $input = false )
    {
        return $input . '<span style="color: green">:</span>';
    }

    /**
     * Default value wrap method
     *
     * @param $value
     * @return string
     */
    private function _wrapValueDefault( $value ) {
        return '<span class="value" style="color: #DD0000">' . $value . '</span>';
    }

    /**
     * Wrap value with red
     *
     * @param $value
     * @return string
     */
    private function _wrapValueRed( $value ) {
        return '<span class="value" style="color: red">' . $value . '</span>';
    }

    /**
     * Add a comma
     *
     * @param bool $input
     * @return string
     */
    private function _addComma( $input = false)
    {
        return $input . ",";
    }

    /**
     * Add a break
     *
     * @param bool $input
     * @return string
     */
    private function _addBr( $input = false )
    {
        return $input . '<br>';
    }

    /**
     * Add a tab
     *
     * @param bool $input
     * @return string
     */
    private function _addTab( $input = false )
    {
        return $input . '&nbsp;&nbsp;';
    }
}
