<?php

/**
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      Controller.php 1001 6/8/20, 8:53 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\App
 * @subpackage   Controller.php
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
 * Main controller namespace
 */
namespace App\Http\Controllers;

/**
 * @uses
 */

use App\Http\Classes\MongoConnection;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @var MongoConnection
     */
    private $mongo;

    /**
     * @var User
     */
    private $user;

    /**
     * @return array
     */
    private function getAttributes(): array
    {
        return $this->user->toArray();
    }

    /**
     * @param User|null $user
     * @param MongoConnection|null $mongo
     */
    public function __construct(?User $user, ?MongoConnection $mongo)
    {
        $this->mongo = $mongo;
        $this->user = $user;
    }

    /**
     * @return bool
     */
    protected function isControlUser(): bool
    {
        return $this->user->getAttribute('control_user') === "1";
    }

    /**
     * Control user, all admin users or user with correct role(s)
     *
     * @param string|null $database
     * @return  bool
     */
    protected function canAdministerUsers(?string $database = 'admin'): bool
    {
        $attr = $this->getAttributes();
        return ((int)$attr['control_user'] === 1 ||
            (int)$attr['admin_user'] === 1 ||
            $this->mongo->hasRootRole() ||
            $this->mongo->hasUserAdminRoleOnDatabase($database));
    }

    /**
     * Control user, all admin users or user with correct role(s)
     *
     * @param string|null $database
     * @return  bool
     */
    protected function canAdministerDatabases(?string $database = 'admin'): bool
    {
        $attr = $this->getAttributes();
        return (int)$attr['control_user'] === 1 ||
            (int)$attr['admin_user'] === 1 ||
            $this->mongo->hasRootRole() ||
            $this->mongo->hasAdminRoleOnDatabase($database);
    }

    /**
     *  Control user, all admin users or user with correct role(s)
     */
    protected function canReadDatabases(?string $database = 'admin'): bool
    {
        $attr = $this->getAttributes();
        return (int)$attr['control_user'] === 1 ||
            (int)$attr['admin_user'] === 1 ||
            $this->mongo->hasRootRole() ||
            $this->mongo->hasReadRoleOnDatabase($database);
    }

    /**
     *  Control user, all admin users or user with correct role(s)
     */
    protected function canLoadDatabases(?string $database = 'admin'): bool
    {
        $attr = $this->getAttributes();
        return (int)$attr['control_user'] === 1 ||
            (int)$attr['admin_user'] === 1 ||
            $this->mongo->hasRootRole() ||
            $this->mongo->hasReadRoleOnDatabase($database) ||
            $this->mongo->hasAdminRoleOnDatabase($database);
    }

    /**
     *  Control user or user with correct role(s)
     */
    protected function canWriteDatabases(?string $database = 'admin'): bool
    {
        $attr = $this->getAttributes();
        return (int)$attr['control_user'] === 1 ||
            $this->mongo->hasRootRole() ||
            $this->mongo->hasReadRoleOnDatabase($database);
    }
}
