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

if (!isset($_SESSION['userid'])) {
    return;
}
echo '<h1>Notifications</h1>';
echo '<div id="log_table">';

$notifications = $GLOBALS['db']->get_results("SELECT * FROM user_notification WHERE user_id = '" . $_SESSION['userid'] . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY time DESC LIMIT 200");
if ($GLOBALS['db']->num_rows > 0) {
    echo '<script type="text/javascript" src="./3rdparty/jquery.tablesorter.js" charset="UTF-8"></script>';
    echo '<table width="100%" class="stat_table" id="notification_table">';
    echo '<thead>';
    echo '<tr><th>Notification</th><th>Time</th></tr>';
    echo '</thead><tbody>';
    foreach ($notifications as $notification) {
        echo '<tr class="notification notification_all" data-type="' . $notification->type . '" data-link="' . $notification->link . '">';
        echo '<td>' . $notification->title . '</td>';
        echo '<td>' . $notification->time . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '<script>';
    echo '$(document).ready(function() { $("#notification_table").tablesorter(); });';
    echo '</script>';
} else {
    echo '<p>No notifications</p>';
}

echo '</div>';
?>
