<?php
/**
 * Data class to mark a notice as a question
 *
 * PHP version 5
 *
 * @category QnA
 * @package  StatusNet
 * @author   Zach Copley <zach@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.     See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

/**
 * For storing a question
 *
 * @category QnA
 * @package  StatusNet
 * @author   Zach Copley <zach@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * @see      DB_DataObject
 */

class QnA_Question extends Managed_DataObject
{
    const OBJECT_TYPE = 'http://activityschema.org/object/question';

    public $__table = 'qna_question'; // table name
    public $id;          // char(36) primary key not null -> UUID
    public $uri;
    public $profile_id;  // int -> profile.id
    public $title;       // text
    public $description; // text
    public $closed;      // int (boolean) whether a question is closed
    public $created;     // datetime

    /**
     * Get an instance by key
     *
     * This is a utility method to get a single instance with a given key value.
     *
     * @param string $k Key to use to lookup
     * @param mixed  $v Value to lookup
     *
     * @return QnA_Question object found, or null for no hits
     *
     */
    function staticGet($k, $v=null)
    {
        return Memcached_DataObject::staticGet('QnA_Question', $k, $v);
    }

    /**
     * Get an instance by compound key
     *
     * This is a utility method to get a single instance with a given set of
     * key-value pairs. Usually used for the primary key for a compound key; thus
     * the name.
     *
     * @param array $kv array of key-value mappings
     *
     * @return Bookmark object found, or null for no hits
     *
     */
    function pkeyGet($kv)
    {
        return Memcached_DataObject::pkeyGet('QnA_Question', $kv);
    }

    /**
     * The One True Thingy that must be defined and declared.
     */
    public static function schemaDef()
    {
        return array(
            'description' => 'Per-notice question data for QNA plugin',
            'fields' => array(
                'id' => array(
                    'type'        => 'char',
                    'length'      => 36,
                    'not null'    => true,
                    'description' => 'UUID'
                ),
                'uri' => array(
                    'type'     => 'varchar',
                    'length'   => 255,
                    'not null' => true
                ),
                'profile_id'  => array('type' => 'int'),
                'title'       => array('type' => 'text'),
                'closed'      => array('type' => 'int', 'size' => 'tiny'),
                'description' => array('type' => 'text'),
                'created'     => array(
                    'type'     => 'datetime',
                    'not null' => true
                ),
            ),
            'primary key' => array('id'),
            'unique keys' => array(
                'question_uri_key' => array('uri'),
            ),
        );
    }

    /**
     * Get a question based on a notice
     *
     * @param Notice $notice Notice to check for
     *
     * @return Question found question or null
     */
    function getByNotice($notice)
    {
        return self::staticGet('uri', $notice->uri);
    }

    function getNotice()
    {
        return Notice::staticGet('uri', $this->uri);
    }

    function bestUrl()
    {
        return $this->getNotice()->bestUrl();
    }

    function getProfile()
    {
        $profile = Profile::staticGet('id', $this->profile_id);
        if (empty($profile)) {
            throw new Exception("No profile with ID {$this->profile_id}");
        }
        return $profile;
    }

    /**
     * Get the answer from a particular user to this question, if any.
     *
     * @param Profile $profile
     *
     * @return Answer object or null
     */
    function getAnswer(Profile $profile)
    {
        $a = new QnA_Answer();
        $a->question_id = $this->id;
        $a->profile_id = $profile->id;
        $a->find();
        if ($a->fetch()) {
            return $a;
        } else {
            return null;
        }
    }

    function getAnswers()
    {
        $a = new QnA_Answer();
        $a->question_id = $this->id;
        $cnt = $a->find();
        if (!empty($cnt)) {
            return $a;
        } else {
            return null;
        }
    }

    function countAnswers()
    {
        $a              = new QnA_Answer();
        $a->question_id = $this->id;
        return $a-count();
    }

    static function fromNotice($notice)
    {
        return QnA_Question::staticGet('uri', $notice->uri);
    }

    function asHTML()
    {
        return self::toHTML(
            $this->getProfile(),
            $this,
            $this->getAnswers()
        );
    }

    function asString()
    {
        return self::toString(
            $this->getProfile(),
            $this,
            $this->getAnswers()
        );
    }

    static function toHTML($profile, $question, $answer)
    {
        $notice = $question->getNotice();

        $fmt =  '<p class="qna_question">';
        $fmt .= '<span class="question_title"><a href="%1$s">%2$s</a></span>';
        $fmt .= '<span class="question_description">%3$s</span>';
        $fmt .= '<span class="question_author">asked by <a href="%4$s">%5$s</a></span>';
        $fmt .= '</p>';

        $q = sprintf(
            $fmt,
            htmlspecialchars($notice->bestUrl()),
            htmlspecialchars($question->title),
            htmlspecialchars($question->description),
            htmlspecialchars($profile->profileurl),
            htmlspecialchars($profile->getBestName())
        );

        $ans = array();

        $ans[] = '<div class="qna_answers">';

        while($answer->fetch()) {
            $ans[] = $answer->asHTML();
        }

        $ans[] .= '</div>';

        return $q . implode($ans);
    }

    static function toString($profile, $question, $answers)
    {
        $fmt = _m(
            '%1$s asked the question "%2$s": %3$s'
        );

        return sprintf(
            $fmt,
            htmlspecialchars($profile->getBestName()),
            htmlspecialchars($question->title),
            htmlspecialchars($question->description)
        );
    }

    /**
     * Save a new question notice
     *
     * @param Profile $profile
     * @param string  $question
     * @param string  $title
     * @param string  $description
     * @param array   $option // and whatnot
     *
     * @return Notice saved notice
     */
    static function saveNew($profile, $title, $description, $options = array())
    {
        $q = new QnA_Question();

        $q->id          = UUID::gen();
        $q->profile_id  = $profile->id;
        $q->title       = $title;
        $q->description = $description;

        if (array_key_exists('created', $options)) {
            $q->created = $options['created'];
        } else {
            $q->created = common_sql_now();
        }

        if (array_key_exists('uri', $options)) {
            $q->uri = $options['uri'];
        } else {
            $q->uri = common_local_url(
                'qnashowquestion',
                array('id' => $q->id)
            );
        }

        common_log(LOG_DEBUG, "Saving question: $q->id $q->uri");
        $q->insert();

        // TRANS: Notice content creating a question.
        // TRANS: %1$s is the title of the question, %2$s is a link to the question.
        $content  = sprintf(
            _m('question: %1$s %2$s'),
            $title,
            $q->uri
        );

        $link = '<a href="' . htmlspecialchars($q->uri) . '">' . htmlspecialchars($title) . '</a>';
        // TRANS: Rendered version of the notice content creating a question.
        // TRANS: %s a link to the question as link description.
        $rendered = sprintf(_m('Question: %s'), $link);

        $tags    = array('question');
        $replies = array();

        $options = array_merge(
            array(
                'urls'        => array(),
                'rendered'    => $rendered,
                'tags'        => $tags,
                'replies'     => $replies,
                'object_type' => self::OBJECT_TYPE
            ),
            $options
        );

        if (!array_key_exists('uri', $options)) {
            $options['uri'] = $q->uri;
        }

        $saved = Notice::saveNew(
            $profile->id,
            $content,
            array_key_exists('source', $options) ?
            $options['source'] : 'web',
            $options
        );

        return $saved;
    }
}
