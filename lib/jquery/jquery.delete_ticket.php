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

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('delete_ticket')) {
    return;
}

require_once './ticket.class.php';
$args = filter_input_array(INPUT_POST);

if ($args['func'] == "delete_ticket") {
    if (!isset($args['id'])) {
        return;
    }

    $id = $GLOBALS['db']->escape($args['id']);
    $reason = "";
    $delete_children = "0";

    if (isset($args['delete_children'])) {
        $delete_children = $GLOBALS['db']->escape($args['delete_children']);
    }

    if (isset($args['reason'])) {
        $reason = $GLOBALS['db']->escape($args['reason']);
    }

    $ticket = new Ticket($id);
    $parent = $ticket->getParent();

    if ($parent->getId() != 0) {
        $log = $_SESSION['username'] . ' deleted child ticket ' . $ticket->getTitle() . '. Reason: ' . $reason;
        $parent->addHistory($_SESSION['userid'], $log);

        if ($parent->getResponsibleId() != $_SESSION['userid']) {
            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $parent->getResponsibleId() . "', '" . $log . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
        }


        $parent_resp = $parent->getResponsible();
        $GLOBALS['email']->setRecipient($parent_resp);
        $GLOBALS['email']->setSubject("Child ticket deleted!");
        $GLOBALS['email']->setMessage($log);
        $GLOBALS['email']->send();
    }

    $log = $_SESSION['username'] . ' deleted ticket ' . $ticket->getTitle() . '. Reason: ' . $reason;

    if ($ticket->getResponsibleId() != $_SESSION['userid']) {
        $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $ticket->getResponsibleId() . "', '" . $_SESSION['username'] . " deleted ticket " . $ticket->getTitle() . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
    }

    $GLOBALS['email']->setRecipient($ticket->getResponsible());
    $GLOBALS['email']->setMessage($log);
    $GLOBALS['email']->setSubject("Your ticket was deleted!");
    $GLOBALS['email']->send();

    $GLOBALS['logger']->log($log);
    $GLOBALS['db']->query("UPDATE ticket SET deleted = '1' WHERE id = '" . $ticket->getId() . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");

    $subscribers = $GLOBALS['db']->get_results("SELECT user_id FROM ticket_subscription WHERE ticket_id = '" . $ticket->getId() . "'");

    if ($subscribers) {
        $GLOBALS['email']->setSubject("Ticket was deleted!");
        foreach ($subscribers as $subscriber) {
            $sub = new User($subscriber->user_id);
            $GLOBALS['email']->setRecipient($log);
            $GLOBALS['email']->send();
        }
    }

    if ($delete_children == "1") {
        $children = $ticket->getChildren();

        if ($children) {
            foreach ($children as $child) {
                $GLOBALS['db']->query("UPDATE ticket SET deleted = '1' WHERE id = '" . $child->getId() . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");
                $log = $_SESSION['username'] . ' deleted ticket ' . $child->getTitle() . '. Reason ' . $reason;
                $GLOBALS['logger']->log($log);
                if ($child->getResponsibleId() != $_SESSION['userid']) {
                    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $child->getResponsibleId() . "', '" . $_SESSION['username'] . " deleted ticket " . $child->getTitle() . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
                }

                $GLOBALS['email']->setRecipient($child->getResponsible());
                $GLOBALS['email']->setSubject("Parent ticket deleted!");
                $GLOBALS['email']->setMessage($log);
                $GLOBALS['email']->send();

                $subscribers = $GLOBALS['db']->get_results("SELECT user_id FROM ticket_subscription WHERE ticket_id = '" . $children->getId() . "'");

                if ($subscribers) {
                    $GLOBALS['email']->setSubject("Ticket was deleted!");
                    foreach ($subscribers as $subscriber) {
                        if ($subscriber->user_id != $_SESSION['userid']) {
                            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $subscriber->user_id . "', '" . $_SESSION['username'] . " deleted ticket " . $ticket->getTitle() . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
                        }

                        $sub = new User($subscriber->user_id);
                        $GLOBALS['email']->setRecipient($sub);
                        $GLOBALS['email']->send();
                    }
                }
            }
        }
    }

    $GLOBALS['board']->updateClients();
} else if ($args['func'] == "child_tickets") {
    $id = $GLOBALS['db']->escape($args['id']);
    $ticket = new Ticket($id);
    echo $ticket->getNumChildren();
}
?>
