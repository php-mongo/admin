<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ExportDocument.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   ExportDocument.php
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

use App\Helpers\MongoHelper;

/**
 * Class ExportDocument
 * @package App\Classes
 */
class ExportDocument extends ExportClass
{
    /**
     * Export the variable to PHP
     *
     * @return string
     */
    private function exportArray(): string
    {
        $var = $this->formatVar($this->getVar());

        $string = var_export($var, true);

        foreach ($this->getPhpParams() as $index => $value) {
            $this->setParam($index, $value);
        }
        return strtr($string, $this->getParams());
    }

    /**
     * @return string
     */
    private function exportJSON(): string
    {
        if (function_exists("json_encode")) {
            $service = "json_encode";
        } else {
            $json = new ServicesJSON();
            $service = array(&$json, 'encode');
        }
        // $var = $this->formatVarAsJSON($this->var, $service);
        // $string = call_user_func($service, $var);
        $string = call_user_func($service, $this->getVar());

        //Remove "\/" escape
        $string = str_replace('\/', "/", $string);

        $params = array();
        foreach ($this->getJsonParams() as $index => $value) {
            $params['"' . $this->param($index) . '"'] = $value;
        }
        return MongoHelper::jsonUnicodeToUtf8(MongoHelper::jsonFormat(strtr($string, $params)));
    }

    /**
     * ExportDocument constructor.
     */
    public function __construct()
    {
        // clear the properties
        $this->setVar(null);
        $this->setParams([]);
    }

    /**
     * Handle the export process
     *
     * @param   string  $type
     * @param   bool    $fieldLabel
     * @return  string
     */
    public function export(string $type = 'array', bool $fieldLabel = false): string
    {
        if ($fieldLabel) {
            $this->setVar($this->addLabelToArray($this->getVar()));
        }
        if ('array' == $type) {
            return $this->exportArray();
        }
        return $this->exportJSON();
    }
}
