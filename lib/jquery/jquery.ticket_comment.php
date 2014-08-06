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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('comment_ticket')) {
    return;
}

$args = filter_input_array(INPUT_POST);

if (isset($args['func']) && $args['func'] == "new_comment") {
    $ticket_id = $GLOBALS['db']->escape($args['id']);
    $c = $GLOBALS['db']->escape($args['comment']);
    $linked_comment = preg_replace('#[a-zA-Z]+://\S+#m', '<a href="$0" rel="nofollow" target="_blank">$0</a>', $c);
    $comment = str_replace("\n", "[br]", $linked_comment);


    if ($comment == "") {
        return;
    }

    $ticket = new Ticket($ticket_id);

    $GLOBALS['db']->query("UPDATE ticket SET last_change = UTC_TIMESTAMP() WHERE id = '" . $ticket_id . "'");

    $log = $_SESSION['username'] . ' commented to <a class="ticket_comment_log" data-id="' . $ticket_id . '" href="#">' . $ticket->getTitle() . '</a>: ' . $comment;
    if ($ticket->getResponsibleId() != $_SESSION['userid']) {
        $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $ticket->getResponsibleId() . "', '" . $_SESSION['username'] . " commented ticket " . $ticket->getTitle() . "', 'comment', '" . $ticket_id . "', 'unread', UTC_TIMESTAMP())");
    }

    $GLOBALS['logger']->log($log);
    $resp = $ticket->getResponsible();

    $GLOBALS['email']->setReceipient($resp);
    $GLOBALS['email']->setSubject("New comment on your ticket");

    $mail = $_SESSION['username'] . ' commented ticket "' . $ticket->getTitle() . '"<br><br>';
    $mail .= 'Comment: ' . $comment . '<br>';
    $GLOBALS['email']->setMessage($mail);
    $GLOBALS['email']->send();

    $subscribers = $GLOBALS['db']->get_results("SELECT user_id FROM ticket_subscription WHERE ticket_id = '" . $ticket_id . "'");
    if ($subscribers) {
        $GLOBALS['email']->setSubject("New comment on ticket you are subscribed");
        foreach ($subscribers as $subscriber) {
            if ($subscriber->user_id != $_SESSION['userid']) {
                $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $subscriber->user_id . "', '" . $_SESSION['username'] . " commented on ticket " . $ticket->getTitle() . "', 'comment', '" . $ticket_id . "', 'unread', UTC_TIMESTAMP())");
            }


            $sub = new User($subscriber->user_id);
            $GLOBALS['email']->setReceipient($sub);
            $GLOBALS['email']->send();
        }
    }

    $u_subscribers = $GLOBALS['db']->get_results("SELECT email FROM ticket_email_subscription WHERE ticket_id = '" . $ticket_id . "'");
    if ($subscribers) {
        $GLOBALS['email']->setSubject("New comment on ticket you are subscribed");
        foreach ($u_subscribers as $subscriber) {
            $GLOBALS['email']->setAddress($subscriber->email);
            $GLOBALS['email']->send();
        }
    }

    $GLOBALS['db']->query("INSERT INTO ticket_comment (ticket_id, user_id, data, created) VALUES ('" . $ticket_id . "', '" . $_SESSION['userid'] . "', '" . $comment . "', UTC_TIMESTAMP())");
} else if (isset($args['func']) && $args['func'] == "delete_comment") {
    $comment_id = $GLOBALS['db']->escape($args['id']);
    $comment = $GLOBALS['db']->get_row("SELECT * FROM ticket_comment WHERE id = '" . $comment_id . "' LIMIT 1");
    if (!$comment) {
        return;
    }
    $ticket = new Ticket($comment->ticket_id);
    $log = $_SESSION['username'] . ' deleted comment "' . $comment->data . '" from <a class="ticket_comment_log" data-id="' . $ticket->getId() . '" href="">' . $ticket->getTitle() . '</a>';
    $GLOBALS['logger']->log($log);
    $resp = $ticket->getResponsible();
    $GLOBALS['email']->setReceipient($resp);
    $GLOBALS['email']->setSubject("Comment deleted on your ticket");
    $mail = $_SESSION['username'] . ' deleted comment from your ticket ' . $ticket->getTitle() . '<br><br>';
    $mail .= 'Comment: ' . str_replace('[br]', '<br', $comment->data) . '<br>';
    $GLOBALS['email']->setMessage($mail);
    $GLOBALS['email']->send();

    $subscribers = $GLOBALS['db']->get_results("SELECT user_id FROM ticket_subscription WHERE ticket_id = '" . $comment->ticket_id . "'");
    if ($subscribers) {
        $GLOBALS['email']->setSubject("Comment deleted on ticket you are subscribed");
        foreach ($subscribers as $subscriber) {
            $sub = new User($subscriber->user_id);
            $GLOBALS['email']->setReceipient($sub);
            $GLOBALS['email']->send();
        }
    }

    $GLOBALS['db']->query("DELETE FROM ticket_comment WHERE id = '" . $comment_id . "' LIMIT 1");
}
?>
