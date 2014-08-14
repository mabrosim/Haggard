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
echo '<script type="text/javascript" src="./js/message_board.js"></script>' . PHP_EOL;
echo '<div id="left_menu">';
echo '<ul>';
echo '<li><a href="" class="new_message">New message</a></li>';
echo '<li><a href="" class="back_to_topics">Back to topics</a></li>';
echo '</ul>';
echo '</div>';
echo '<div id="sub_content">';

$get_id = filter_input(INPUT_GET, 'id');
if (!isset($get_id)) {
    echo "Could not find topic";
    return;
}

$topic = new MessageTopic($get_id);


echo '<div id="topic_id" data-id="' . $topic->getId() . '" style="display:hidden"></div>';
echo '<h2>Topic: ' . $topic->getName() . '</h2>';

$messages = $topic->getMessages();
if (count($messages) > 0) {
    echo "<h2>Messages (" . $topic->numPosts() . ")</h2>";
    foreach ($messages as $message) {
        echo '<table id="message">';

        echo '<tr>';
        echo '<td valign="top"><p>' . $message->getMessage() . '</p></td>';
        $user = $message->getUser();
        echo '<td valign="top" width="15%"><p><a href="mailto:' . $user->getEmail() . '">' . $user->getName() . '</a></p>';
        echo '<p class="info">' . date("d.m.Y H:i", strtotime($message->getTime() . ' UTC')) . '</p>';
        echo '<p class="info">Posts: ' . $user->getNumPosts() . '</p>';
        echo '</td>';
        echo '</tr>';

        echo '</table>';
    }
}
echo '</div>';
?>
