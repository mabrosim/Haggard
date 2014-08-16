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

require_once './message_topic.class.php';
require_once './message.class.php';

echo '<script type="text/javascript" src="./js/message_board.js"></script>' . PHP_EOL;

echo '<div class="left_menu">';
echo '<ul>';
echo '<li><a href="" class="new_message_topic">New topic</a></li>';
echo '</ul>';
echo '</div>';
echo '<div id="sub_content">';
echo '<h1>Message board</h1>';

echo '<div id="message_board_content">';

echo '<table class="settings_table" style="width:100%;">';

echo '<tr>';
echo '<th>Topics</th>';
echo '<th width="10%">Posts</th>';
echo '<th width="20%">Last post</th>';
echo '<th width="20%">Created</th>';
echo '</tr>';

$message_topics = $GLOBALS['db']->get_results("SELECT id FROM message_topic WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY id DESC");
if ($GLOBALS['db']->num_rows > 0) {
    foreach ($message_topics as $msg_topic) {
        echo '<tr>';
        $topic = new MessageTopic($msg_topic->id);
        echo '<td><a href="" class="topic_messages" data-topic="' . $topic->getId() . '">' . $topic->getName() . '</a></td>';
        echo '<td>' . $topic->numPosts() . '</td>';
        $last_post = $topic->getLastPost();
        if ($last_post) {
            $poster = $last_post->getUser();
            echo '<td>' . date('d.m.Y H:i', strtotime($last_post->getTime() . ' UTC')) . ' by <a href="mailto:' . $poster->getEmail() . '">' . $poster->getName() . '</a></td>';
        } else {
            echo '<td>No posts</td>';
        }
        $creator = $topic->getCreator();
        echo '<td>' . date("d.m.Y H:i", strtotime($topic->getCreated() . ' UTC')) . ' by <a href="mailto:' . $creator->getEmail() . '">' . $creator->getName() . '</a></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No topics yet</td></tr>';
}

echo '</table>';
echo '</div>';
echo '</div>';
?>
