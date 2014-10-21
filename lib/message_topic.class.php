<?php

/*
Copyright (c) 2013-2014, Microsoft Mobile
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name of the {organization} nor the names of its
  contributors may be used to endorse or promote products derived from
  this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

require_once 'user.class.php';
require_once 'message.class.php';

class MessageTopic
{

    private $id;
    private $name;
    private $user_id;
    private $created;

    public function __construct($id)
    {
        $id = $GLOBALS['db']->escape($id);
        $res = $GLOBALS['db']->get_row("SELECT * FROM message_topic WHERE id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");
        if ($res) {
            $this->name = $res->name;
            $this->id = $id;
            $this->user_id = $res->user_id;
            $this->created = $res->created;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCreator()
    {
        return new User($this->user_id);
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getMessages()
    {
        $ret = array();
        $messages = $GLOBALS['db']->get_results("SELECT id FROM message WHERE topic_id = '" . $this->id . "' ORDER BY id DESC");
        if ($messages) {
            foreach ($messages as $message) {
                array_push($ret, new Message($message->id));
            }
        }

        return $ret;
    }

    public function numPosts()
    {
        return $GLOBALS['db']->get_var("SELECT COUNT(id) FROM message WHERE topic_id = '" . $this->id . "'");
    }

    public function getLastPost()
    {
        $id = $GLOBALS['db']->get_var("SELECT MAX(id) FROM message WHERE topic_id = '" . $this->id . "'");
        if ($id) {
            return new Message($id);
        }

        return NULL;
    }

}

?>
