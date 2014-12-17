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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('create_ticket')) {
    return;
}

$args = filter_input_array(INPUT_POST);

$title = strip_tags(mysql_real_escape_string($args['title']));
$wip = strip_tags(mysql_real_escape_string($args['wip']));
$resp = strip_tags(mysql_real_escape_string($args['resp']));
$prio = strip_tags(mysql_real_escape_string($args['prio']));
$ref = strip_tags(mysql_real_escape_string($args['reference_id']));
$comp = strip_tags(mysql_real_escape_string($args['comp']));
$parent = strip_tags(mysql_real_escape_string($args['parent']));
$phase = strip_tags(mysql_real_escape_string($args['phase']));
$info = preg_replace('#[a-zA-Z]+://\S+#m', '<a href="$0" rel="nofollow" target="_blank">$0</a>', mysql_real_escape_string($args['info']));

$query = "INSERT INTO ticket (board_id, data, info, responsible, wip, cycle, phase, ";
$query .= "priority, reference_id, active, deleted, component, created, last_change) VALUES (";
$query .= "'" . $GLOBALS['board']->getBoardId() . "', '" . $title . "', '" . $info . "', '" . $resp . "', '" . $wip . "'";

if (isset($_SESSION['current_cycle']) && $_SESSION['current_cycle'] != 0) {
    $cycle_id = $_SESSION['current_cycle'];
} else {
    $cycle_id = $GLOBALS['db']->get_var("SELECT id FROM cycle WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' AND active = '1' ORDER BY start DESC LIMIT 1");
}

$query .= ", '" . $cycle_id . "', '" . $phase . "', '" . $prio . "', '" . $ref . "', '1', '0', '" . $comp . "', UTC_TIMESTAMP(), UTC_TIMESTAMP())";

$GLOBALS['db']->query($query);

$new_ticket_id = $GLOBALS['db']->insert_id;

if ($parent != 0) {
    /* Link these tickets */
    $GLOBALS['db']->query("INSERT INTO ticket_link (parent, child) VALUES ('" . $parent . "', '" . $new_ticket_id . "')");
    $history = $_SESSION['username'] . " created new child ticket: " . $title;
    $GLOBALS['db']->query("INSERT INTO ticket_history (ticket_id, user_id, data) VALUES ('" . $parent . "', '" . $_SESSION['userid'] . "', '" . $history . "')");
}

$history = 'Ticket created';
$GLOBALS['db']->query("INSERT INTO ticket_history (ticket_id, user_id, data, created) VALUES ('" . $new_ticket_id . "', '" . $_SESSION['userid'] . "', '" . $history . "', UTC_TIMESTAMP())");

$log = $_SESSION['username'] . ' created new ticket: ' . $title;
$GLOBALS['logger']->log($log);

$responsible = new User($resp);
$GLOBALS['email']->setRecipient($responsible);
$GLOBALS['email']->setSubject("You have a new ticket!");
$message = $_SESSION['username'] . ' created new ticket and you are responsible!<br><br>';
$message .= "Ticket title: " . $title . "<br><br>";
$message .= "Check the ticket at board: " . $GLOBALS['board']->getBoardURL() . "?ticket_id=" . $new_ticket_id . "<br>";

$GLOBALS['email']->setMessage($message);
$GLOBALS['email']->send();

if ($resp != $_SESSION['userid']) {
    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $resp . "', 'You have a new ticket " . $title . "', 'page', 'page.board.php&ticket_id=" . $new_ticket_id . "', 'unread', UTC_TIMESTAMP())");
}

$p = new Phase($phase);
$ticket = new Ticket($new_ticket_id);
$GLOBALS['email']->setSubject("New ticket!");
$message = $_SESSION['username'] . ' created new ticket "' . $title . '" on phase ' . $p->getName() . '<br><br>';
$GLOBALS['email']->setMessage($message);
$GLOBALS['email']->generateTicketFooter($ticket);

$subscribers = $GLOBALS['db']->get_results("SELECT * FROM phase_subscription ps LEFT JOIN user u ON ps.user_id = u.id WHERE phase_id = '" . $phase . "'");
if ($GLOBALS['db']->num_rows > 0) {
    foreach ($subscribers as $subscriber) {
        if ($subscriber->user_id != $_SESSION['userid']) {
            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $subscriber->user_id . "', '" . $_SESSION['username'] . " created new ticket to phase " . $p->getName() . "', 'page', 'page.board.php&ticket_id=" . $new_ticket_id . "', 'unread', UTC_TIMESTAMP())");
        }

        $GLOBALS['email']->setAddress($subscriber->email);
        $GLOBALS['email']->send();
    }
}

$u_subscribers = $GLOBALS['db']->get_results("SELECT * FROM phase_email_notification ps LEFT JOIN user_group_link ug ON ug.group_id = ps.group_id LEFT JOIN user u ON ug.user_id = u.id WHERE phase_id = '" . $phase . "'");
if ($GLOBALS['db']->num_rows > 0) {
    foreach ($u_subscribers as $subscriber) {
        if ($subscriber->user_id != $_SESSION['userid']) {
            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $subscriber->user_id . "', '" . $_SESSION['username'] . " created new ticket to phase " . $p->getName() . "', 'page', 'page.board.php&ticket_id=" . $new_ticket_id . "', 'unread', UTC_TIMESTAMP())");
        }

        $GLOBALS['email']->setAddress($subscriber->email);
        $GLOBALS['email']->send();
    }
}

$GLOBALS['board']->updateClients();
?>
