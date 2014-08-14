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

$args = filter_input_array(INPUT_POST);
if (!isset($args['func'])) {
    return;
}

if ($args['func'] == "number_new") {
    echo $GLOBALS['db']->get_var("SELECT COUNT(id) FROM user_notification WHERE user_id = '" . $_SESSION['userid'] . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' AND status = 'unread'");
} else if ($args['func'] == "get_notifications") {
    $notifications = array();
    $latest = $GLOBALS['db']->get_results("SELECT * FROM (SELECT * FROM user_notification WHERE user_id = '" . $_SESSION['userid'] . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY time DESC LIMIT 5) tmp ORDER BY tmp.time ASC");
    if ($GLOBALS['db']->num_rows > 0) {
        foreach ($latest as $notification) {
            $not = array();
            $not['title'] = $notification->title;
            $not['type'] = $notification->type;
            $not['link'] = $notification->link;
            $not['status'] = $notification->status;
            $not['time'] = $notification->time;
            array_push($notifications, $not);
        }

        echo json_encode($notifications);
    }
} else if ($args['func'] == "set_as_read") {
    $GLOBALS['db']->query("UPDATE user_notification SET status = 'read' WHERE user_id = '" . $_SESSION['userid'] . "'");
}
?>
