<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * Form for entering an event
 * 
 * PHP version 5
 *
 * This program is free software: you can redistribute it and/or modify
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
 * @category  Event
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

/**
 * Form for adding an event
 *
 * @category  Event
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

class EventForm extends Form
{
    /**
     * ID of the form
     *
     * @return int ID of the form
     */

    function id()
    {
        return 'form_new_event';
    }

    /**
     * class of the form
     *
     * @return string class of the form
     */

    function formClass()
    {
        return 'form_settings ajax-notice';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
        return common_local_url('newevent');
    }

    /**
     * Data elements of the form
     *
     * @return void
     */

    function formData()
    {
        $this->out->elementStart('fieldset', array('id' => 'new_bookmark_data'));
        $this->out->elementStart('ul', 'form_data');

        $this->li();
        $this->out->input('title',
                          _m('LABEL','Title'),
                          null,
                          _m('Title of the event'));
        $this->unli();

        $this->li();
        $this->out->input('startdate',
                          _m('LABEL','Start date'),
                          null,
                          _m('Date the event starts'));
        $this->unli();

        $this->li();
        $this->out->input('starttime',
                          _m('LABEL','Start time'),
                          null,
                          _m('Time the event starts'));
        $this->unli();

        $this->li();
        $this->out->input('enddate',
                          _m('LABEL','End date'),
                          null,   
                          _m('Date the event ends'));
        $this->unli();

        $this->li();
        $this->out->input('endtime',
                          _m('LABEL','End time'),
                          null,
                          _m('Time the event ends'));
        $this->unli();

        $this->li();
        $this->out->input('location',
                          _m('LABEL','Location'),
                          null,
                          _m('Event location'));
        $this->unli();

        $this->li();
        $this->out->input('url',
                          _m('LABEL','URL'),
                          null,
                          _m('URL for more information'));
        $this->unli();

        $this->li();
        $this->out->input('description',
                          _m('LABEL','Description'),
                          null,
                          _m('Description of the event'));
        $this->unli();

        $this->out->elementEnd('ul');
        $this->out->elementEnd('fieldset');
    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
        $this->out->submit('submit', _m('BUTTON', 'Save'));
    }
}
