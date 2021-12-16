<?php

declare(strict_types=1);

/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      PrivilegesTrait.php 1001 25/8/21, 8:12 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   PrivilegesTrait.php
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2021. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

namespace App\Http\Traits;

use App\Exceptions\NoServerConfigurationException;
use MongoDB\Driver\Exception\Exception;
use MongoDB;

trait PrivilegesTrait
{
    /**
     * @return bool
     */
    public function hasRootRole(): bool
    {
        return $this->userRoles['hasRoot'] ?? false;
    }

    /**
     * If user has Read role they should be able to load most collection fata
     * @param bool|null $any
     * @return bool
     */
    public function isDbAdmin(?bool $any): bool
    {
        $hasReadRole = false;
        $hasDbAdminRole = false;
        foreach ($this->getUserRoles()['roles'] as $role) {
            if ($any) {
                if (
                    strpos($role['role'], 'read') !== false &&
                    strpos($role['role'], 'any') !== false
                ) {
                    $hasReadRole = true;
                }
            }
            if (strpos($role['role'], 'read') !== false) {
                $hasReadRole = true;
            }
            if ($any) {
                if (
                    strpos($role['role'], 'dbAdmin') !== false &&
                    strpos($role['role'], 'any') !== false
                ) {
                    $hasDbAdminRole = true;
                }
            }
            if (strpos($role['role'], 'dbAdmin') !== false) {
                $hasDbAdminRole = true;
            }
        }
        return $hasDbAdminRole && !$hasReadRole;
    }

    /**
     * @param   string $db The database we are checking against
     * @return  bool
     */
    public function hasUserAdminRoleOnDatabase(string $db): bool
    {
        foreach ($this->getUserRoles()['roles'] as $role) {
            if ($role['role'] === 'userAdminAnyDatabase') {
                return true;
            }
            if ($role['db'] == $db && in_array($role['role'], $this->userAdminRoles, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param   string $db The database we are checking against
     * @return  bool
     */
    public function hasReadRoleOnDatabase(string $db): bool
    {
        foreach ($this->getUserRoles()['roles'] as $role) {
            if ($role['role'] === 'readAnyDatabase') {
                return true;
            }
            if ($role['db'] == $db && in_array($role['role'], $this->readRoles, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param   string $db The database we are checking against
     * @return  bool
     */
    public function hasWriteRoleOnDatabase(string $db): bool
    {
        foreach ($this->getUserRoles()['roles'] as $role) {
            if ($role['role'] === 'readWriteAnyDatabase') {
                return true;
            }
            if ($role['db'] == $db && in_array($role['role'], $this->writeRoles, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param   string $db The database we are checking against
     * @return  bool
     */
    public function hasAdminRoleOnDatabase(string $db): bool
    {
        foreach ($this->getUserRoles()['roles'] as $role) {
            if ($role['role'] === 'dbAdminAnyDatabase') {
                return true;
            }
            if ($role['db'] == $db && in_array($role['role'], $this->dbAdminRoles, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param   string $collection The collection we are checking against
     * @return  bool
     */
    public function hasFindPrivilegeOnCollection(string $collection): bool
    {
        foreach ($this->getUserRoles()['roles'] as $userRole) {
            if (in_array($userRole['role'], $this->dbAdminRoles, true) && $collection === 'system.profile') {
                return true; // allow DB admin on this collection
            }
            if ($collection === 'system.profile') {
                return false; // restrict all other roles from system.profile
            }
            if (in_array($userRole['role'], $this->dbAdminRoles, true)) {
                return false; // restrict DB admin on other collections
            }
            foreach ($this->findPrivilegeOnCollection as $arr) {
                // $arr = ('roleName' => '*') << typical content
                $role = array_keys($arr)[0];
                // ToDo: marginally pointless until evaluation array is correctly populated with all default mongo collection types
                $coll = str_replace('*', $collection, $arr[$role]); // this will replace our all (*) default value
                if ($role === $userRole['role'] && $coll === $collection) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @throws NoServerConfigurationException
     * @throws Exception
     */
    public function getUserInfo()
    {
        $this->connectManager();
        $manager = $this->getManager();
        $command = array(
            'usersInfo' => array(
                'user' => $this->getUserName() ?? '',
                'db' => 'admin'
            )
        );
        $cursor = $manager->executeCommand(
            'admin',
            new MongoDb\Driver\Command($command)
        );

        return $cursor->toArray()[0];
    }

    /**
     * @throws NoServerConfigurationException
     * @throws Exception
     */
    public function getRolesInfo()
    {
        $this->connectManager();
        $manager = $this->getManager();
        $command = array(
            'rolesInfo' => 1,
            'showPrivileges' => true
        );
        $cursor = $manager->executeCommand(
            'admin',
            new MongoDb\Driver\Command($command)
        );

        return $cursor->toArray()[0];
    }
}
