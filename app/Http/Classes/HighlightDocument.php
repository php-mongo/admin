<?php


namespace App\Http\Classes;

use App\Helpers\MongoHelper;
use App\Http\Classes\ExportDocument;


class HighlightDocument
{
    private $_render;

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
    public function highlight( $var, $format = "array", $label = false) {

    //    echo '<pre>'; var_dump($var); echo '</pre>'; die;

        $json = json_encode( $var );

     //   echo '<pre>'; var_dump($json); echo '</pre>'; die;

        $output = MongoHelper::json_format_html( $json);

     //   echo '<pre>'; var_dump($output); echo '</pre>'; die;

        // set the Object ID
        $output = preg_replace_callback("/(['\"])id\\.(.+)\\.id(['\"])/U", function($match) {

            //echo '<pre>'; var_dump($match); echo '</pre>'; die;
            return $this->_setObjectId( $match );

        }, $output);

        // set all 'key' | 'field' wrappers
        $output = preg_replace_callback("/(['\"])key\\.(.+)\\.key(['\"])/U", function($match) {

        //    echo '<pre>'; var_dump($match); echo '</pre>'; die;
            return $this->_setKeyWrapper( $match );

        }, $output);

        // set all 'value' wrappers - perform minor content analysis
        $output = preg_replace_callback("/(['\"])value\\.(.+)\\.value(['\"])/U", function($match) {

            //echo '<pre>'; var_dump($match); echo '</pre>'; die;
            return $this->_analyseValue( $match );

        }, $output);

    //    echo '<pre>'; var_dump($output); echo '</pre>'; die;

        return $output;

        //$exporter  = new ExportDocument();
        //$exporter->setParams([]);
        //$exporter->setVar( $var );
     //   $exporter->setVar(json_encode($var) );
     //   $varString = null;
      //  $highlight = true;

     //   echo '<pre>'; var_dump(json_encode($var)); echo '</pre>'; die;

       /* switch ($this->_getRender()) {
            case "default":
                $varString = $exporter->export($format, $label);
                break;

            case "plain":
                $varString = $exporter->export($format, false);
                $label = false;
                $highlight = false;
                break;

            default:
                $varString = $exporter->export($format, $label);
                break;
        }
        echo '<pre>'; var_dump($varString); echo '</pre>'; die;
        $string = null;
        if ($highlight) {
            if ($format == "array") {
                $string = highlight_string("<?php " . $varString, true);
                $string = preg_replace("/" . preg_quote('<span style="color: #0000BB">&lt;?php&nbsp;</span>', "/") . "/", '', $string, 1);
            }
            else {
                $string =  MongoHelper::json_format_html($varString);
            }
        }
        else {
            $string = "<div><css style=\"width:600px; overflow:auto\">" . $varString . "</css></div>";
        }
        echo '<pre>'; var_dump($string); echo '</pre>'; die;*/
   //     echo '<pre>'; var_dump($label); echo '</pre>'; die;
        /*if ($label) {
            $id = addslashes(isset($var["_id"]) ? MongoHelper::id_string($var["_id"]) : "");

           /* $string = preg_replace_callback("/(['\"])field\\.(.+)\\.field(['\"])/U", function($match) {
                        $fields = explode(".field.", $match[2]);

                        return $fields;

                        echo '<pre>'; var_dump($fields); echo '</pre>'; die;

					    return "<span class=\"field\" ref=\"" . implode(".", $fields) . "\">" . $match[1] . array_pop($fields) . $match[3] . "</span>";
					}, $string);*/ /*

            $string = preg_replace_callback("/field\\.(.+)\\.field/U", function($match) {

            //    echo '<pre>'; var_dump($match); echo '</pre>'; die;

                $fields = explode(".field.", $match[0]);

                $field = $match[1]; // explode(".field.", $match[0]);

            //    return $fields;

           //     echo '<pre>'; var_dump($fields); echo '</pre>'; die;

           //     return "<span class=\"field\" ref=\"" . implode(".", $field) . "\">" . $match[1] . array_pop($fields) . $match[3] . "</span>";

                return "<span class=\"field\" ref=\"" . $field . "\">\"" . $field . "\"</span>";

            }, $string);

         //   echo '<pre>'; var_dump($string); echo '</pre>'; die;

            $string = preg_replace_callback("/__more\\.(.+)\\.more__/U", function($match) use ($id) {
                    $field = str_replace("field.", "", $match[1]);
			            return "<a href=\"#\" v-on:click=\"fieldOpMore(\'" . $field . "\',\'" . $id . "'\');\" title=\"More text\">[...]</a>";
			        }, $string);
        }
   //     echo '<pre>'; var_dump($string); echo '</pre>'; die; echo '<pre>'; var_dump($string); echo '</pre>'; die;
        return $string;*/
    }

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

    private function _wrapValue( $value)
    {
        return '<span class="value">' .
            $this->_wrapQuotes( $value ) .
            '</span>';
    }

    private function _wrapQuotes( $value ) {
        return '"' . $value . '"';
    }

    private function _wrapBracket( $char )
    {
        return '<span style="color: green">' . $char . '</span>';
    }

    private function _wrapColon( $input = false )
    {
        return $input . '<span style="color: green">:</span>';
    }

    private function _wrapValueDefault( $value ) {
        return '<span class="value" style="color: #DD0000">' . $value . '</span>';
    }

    private function _wrapValueRed( $value ) {
        return '<span class="value" style="color: red">' . $value . '</span>';
    }

    private function _addComma( $input = false)
    {
        return $input . ",";
    }

    private function _addBr( $input = false )
    {
        return $input . '<br>';
    }

    private function _addTab( $input = false )
    {
        return $input . '&nbsp;&nbsp;';
    }
}
