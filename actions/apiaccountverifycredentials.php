<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Test if supplied user credentials are valid.
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  API
 * @package   StatusNet
 * @author    Zach Copley <zach@status.net>
 * @copyright 2009 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/apiauth.php';

/**
 * Check a user's credentials. Returns an HTTP 200 OK response code and a
 * representation of the requesting user if authentication was successful;
 * returns a 401 status code and an error message if not.
 *
 * @category API
 * @package  StatusNet
 * @author   Zach Copley <zach@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link     http://status.net/
 */

class ApiAccountVerifyCredentialsAction extends ApiAuthAction
{

    /**
     * Handle the request
     *
     * Check whether the credentials are valid and output the result
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        switch ($this->format) {
        case 'xml':
        case 'json':
            $args['id'] = $this->auth_user->id;
            $action_obj = new ApiUserShowAction();
            if ($action_obj->prepare($args)) {
                $action_obj->handle($args);
            }
            break;
        default:
            header('Content-Type: text/html; charset=utf-8');
            print 'Authorized';
        }

    }

}
