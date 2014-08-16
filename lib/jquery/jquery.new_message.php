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

if (!isset($_SESSION['username'])) {
    return;
}
require_once './message_topic.class.php';
$args = filter_input_array(INPUT_POST);
$message = mysql_real_escape_string($args['message']);
$topic = mysql_real_escape_string($args['topic']);
if (!isset($message) || $message == "" || !isset($topic) || $topic == "") {
    echo "No message given";
    return;
}

$t = new MessageTopic($topic);
$log = $_SESSION['username'] . ' posted new message to : ' . $t->getName();
$GLOBALS['logger']->log($log);

$strip_message = strip_tags($message);

$GLOBALS['db']->query("INSERT INTO message (topic_id, user_id, message, time) VALUES ('" . $topic . "', '" . $_SESSION['userid'] . "', '" . $strip_message . "', UTC_TIMESTAMP())");

$users = $GLOBALS['db']->get_results("SELECT u.id AS id FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '" . $GLOBALS['board']->getBoardId() . "'");
foreach ($users as $user) {
    if ($user->id == $_SESSION['userid']) {
        continue;
    }
    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $user->id . "', '" . $_SESSION['username'] . " posted new message', 'message', '" . $topic . "', 'unread', UTC_TIMESTAMP())");
}
?>
