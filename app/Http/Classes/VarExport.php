<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      VarExport.php 1001 29/8/20, 3:34 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   VarExport.php
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

/**
 * My name for your space
 */
namespace App\Http\Classes;

use App\Helpers\MongoHelper;
use MongoDB\Database;

/**
 * Class VarExport
 * ToDo: !! this class need serious attention !!
 * We need to implement more accurate export data type detections
 *
 * @package App\Http\Classes
 */
class VarExport extends ExportClass
{
    /**
     * @return string
     */
    private function exportPHP(): string
    {
        $var    = $this->formatVar($this->getVar());
        $string = var_export($var, true);
        $params = array();
        foreach ($this->getPhpParams() as $index => $value) {
            $params["'" . $this->param($index) . "'"] = $value;
        }
        return strtr($string, $params);
    }

    /**
     * @return string
     */
    private function exportJSON(): string
    {
        $service = "json_encode";
        $var     = $this->formatVarAsJSON($this->getVar(), $service);
        $string  = call_user_func($service, $var);

        // Remove "\/" escape
        $string = str_replace('\/', "/", $string);

        $params = array();
        foreach ($this->getJsonParams() as $index => $value) {
            $params['"' . $this->param($index) . '"'] = $value;
        }
        return MongoHelper::jsonUnicodeToUtf8(MongoHelper::jsonFormat(strtr($string, $params)));
    }

    /**
     * @return string
     */
    private function exportDocument(): string
    {
        $service = "json_encode";
        $vars    = $this->getVar()->getArrayCopy();
        $output  = '';

        foreach ($vars as $key => $row) {
            $var     = $this->formatVarAsJSON(array($key => $row), $service);

            $string  = call_user_func($service, $var);
            // Remove "\/" escape
            $string = str_replace('\/', "/", $string);

            $params = array();
            foreach ($this->getJsonParams() as $index => $value) {
                $params['"' . $this->param($index) . '"'] = $value;
            }

            $output .= MongoHelper::jsonUnicodeToUtf8(MongoHelper::jsonFormat(strtr($string, $params)));
        }
        return $output;
    }

    /**
     * VarExport constructor.
     * @param   Database $database
     * @param   $var
     */
    public function __construct(Database $database, $var)
    {
        $this->setDatabase($database);
        $this->setVar($var);
    }

    /**
     * @param   string $type
     * @param   bool $fieldLabel
     * @return  string
     */
    public function export(string $type = 'array', bool $fieldLabel = false): string
    {
        if ($fieldLabel) {
            $this->setVar($this->addLabelToArray($this->getVar()));
        }
        if ('array' == $type) {
            return $this->exportPHP();
        }
        if ('document' == $type) {
            return $this->exportDocument();
        }
        return $this->exportJSON();
    }

    /**
     * Used in our export methods
     *
     * @param   $document
     * @return  array
     */
    public function setObjectId($document): array
    {
        $arr = [];
        foreach ($document as $key => $value) {
            if ('id' == $key) {
                // we need to pass this back for exports instead of ObjectId('r42ry6y54t43r5y6y54t45t54y54ya')
                $arr[ $key ] = array("oid" => $value);
            } else {
                $arr[ $key ] = $value;
            }
        }
        return $arr;
    }
}
