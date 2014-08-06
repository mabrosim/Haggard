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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('archive_ticket')) {
    return;
}

require_once './ticket.class.php';
require_once './email.class.php';
require_once './user.class.php';

$email = $GLOBALS['email'];

$archive_children = "0";

$post_id = filter_input(INPUT_POST, 'id');
if (!isset($post_id)) {
    return;
}

$id = $GLOBALS['db']->escape($post_id);

$post_archive = filter_input(INPUT_POST, 'archive_children');
if (isset($post_archive)) {
    $archive_children = $GLOBALS['db']->escape($post_archive);
}

$ticket = new Ticket($id);

if ($ticket->isActive()) {
    $ticket_log = $_SESSION['username'] . ' archived ticket ' . $ticket->getTitle() . '. ';
    $GLOBALS['logger']->log($ticket_log);

    $GLOBALS['db']->query("UPDATE ticket SET active = '0' WHERE id = '" . $id . "' LIMIT 1");

    $parent = $ticket->getParent();

    if ($parent->getId() != 0) {
        $comment = $_SESSION['username'] . ' archived child ticket ' . $ticket->getTitle() . '. ';

        if ($parent->getResponsibleId() != $_SESSION['userid']) {
            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $parent->getResponsibleId() . "', '" . $comment . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
        }

        $parent->addHistory($_SESSION['userid'], $comment);
        $user = $parent->getResponsible();
        $email->setReceipient($user);
        $email->setSubject("Ticket " . $parent->getTitle() . " was archived");
        $email->setMessage("Parent ticket " . $parent->getTitle() . " was archived by " . $_SESSION['username']);
        $email->send();
    }

    if ($archive_children == "1") {
        $children = $ticket->getChildren();
        if ($children) {
            foreach ($children as $child) {
                $GLOBALS['db']->query("UPDATE ticket SET active = '0' WHERE id = '" . $child->getId() . "' LIMIT 1");
                $log = $_SESSION['username'] . ' archived ticket ' . $child->getTitle() . '.';
                $GLOBALS['logger']->log($log);

                if ($child->getResponsibleId() != $_SESSION['userid']) {
                    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $child->getResponsibleId() . "', '" . $log . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
                }

                $user = $child->getResponsible();
                $email->setReceipient($user);
                $email->setSubject("Ticket " . $child->getTitle() . " was archived");
                $email->setMessage("Child ticket " . $child->getTitle() . " was archived by " . $_SESSION['username']);
                $email->send();
            }
        }
    }

    $user = $ticket->getResponsible();

    if ($ticket->getResponsibleId() != $_SESSION['userid']) {
        $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $ticket->getResponsibleId() . "', '" . $ticket_log . "', 'page', 'page.statistics.php&id=0', 'unread', UTC_TIMESTAMP())");
    }

    $email->setReceipient($user);
    $email->setSubject("Ticket " . $ticket->getTitle() . " was archived");
    $email->setMessage("Ticket " . $ticket->getTitle() . " was archived by " . $_SESSION['username']);
    $email->send();
} else {
    $log = $_SESSION['username'] . ' unarchived ticket ' . $ticket->getTitle() . '. ';
    $GLOBALS['logger']->log($log);

    if ($ticket->getResponsibleId() != $_SESSION['userid']) {
        $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $ticket->getResponsibleId() . "', '" . $log . "', 'page', 'page.board.php&ticket_id=" . $ticket->getId() . "', 'unread', UTC_TIMESTAMP())");
    }

    $GLOBALS['db']->query("UPDATE ticket SET active = '1' WHERE id = '" . $id . "' LIMIT 1");

    $user = $ticket->getResponsible();
    $email->setReceipient($user);
    $email->setSubject("Ticket " . $ticket->getTitle() . " was unarchived");
    $email->setMessage("Ticket " . $ticket->getTitle() . " was unarchived by " . $_SESSION['username']);
    $email->generateTicketFooter($ticket);
    $email->send();
}

$GLOBALS['board']->updateClients();
?>
