<?php
/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      ServicesJSONError.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   ServicesJSONError.php
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

if (class_exists('PEAR_Error')) {

    /**
     * Class ServicesJSONError
     * @package App\Http\Classes
     */
    class ServicesJSONError extends PEAR_Error
    {
        /**
         * @param string $message
         * @param null $code
         * @param null $mode
         * @param null $options
         * @param null $userinfo
         */
        function ServicesJSONError($message = 'unknown error', $code = null, $mode = null, $options = null, $userinfo = null)
        {
            parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
        }
    }

} else {

    /**
     * Class ServicesJSONError
     * @package App\Http\Classes
     */
    class ServicesJSONError
    {
        /**
         * @param string $message
         * @param null $code
         * @param null $mode
         * @param null $options
         * @param null $userinfo
         */
        function ServicesJSONError($message = 'unknown error', $code = null, $mode = null, $options = null, $userinfo = null)
        {

        }
    }
}
