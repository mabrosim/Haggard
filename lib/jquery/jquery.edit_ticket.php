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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('edit_ticket')) {
    return;
}

require_once './ticket.class.php';
require_once './cycle.class.php';
require_once './user.class.php';
require_once './priority.class.php';
require_once './component.class.php';

$args = filter_input_array(INPUT_POST);

$title = strip_tags($GLOBALS['db']->escape($args['title']));
$id = $GLOBALS['db']->escape($args['id']);
$wip = strip_tags($GLOBALS['db']->escape($args['wip']));
$resp = $GLOBALS['db']->escape($args['resp']);
$prio = $GLOBALS['db']->escape($args['prio']);
$ref = strip_tags($GLOBALS['db']->escape($args['reference_id']));
$comp = strip_tags($GLOBALS['db']->escape($args['comp']));
$parent = $GLOBALS['db']->escape($args['parent']);
$info = strip_tags($GLOBALS['db']->escape($args['info']));
$phase = $GLOBALS['db']->escape($args['phase']);

$old_ticket = new Ticket($id);
$cycle = $old_ticket->getCycle();
$new_cycle_id = $cycle->getId();

if ($GLOBALS['board']->getSettingValue("USE_CYCLES")) {
    $new_cycle_id = $GLOBALS['db']->escape($args['cycle']);
}

$old_user = $old_ticket->getResponsible();
$new_user = new User($resp);

$log = $_SESSION['username'] . ' updated ticket ' . $old_ticket->getTitle();
if ($old_ticket->getBoard() != null && $old_ticket->getBoard()->getSettingValue("USE_CYCLES")) {
    $log .= ' (' . $cycle->getName() . ')';
}

$log .= ' :';

if ($title !== $old_ticket->getTitle()) {
    $log .= 'Title: ' . $old_ticket->getTitle() . ' updated to ' . $title . '. ';
}

if ($info !== $old_ticket->getInfo()) {
    $log .= 'Additional info: ' . $old_ticket->getInfo() . ' updated to ' . $info . '. ';
}

if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
    if ($wip !== $old_ticket->getWIP()) {
        $log .= 'WIP: ' . $old_ticket->getWIP() . ' updated to ' . $wip . '. ';
    }
}

if ($phase !== $old_ticket->getPhaseId()) {
    $new_phase = new Phase($phase);
    $log .= 'Phase: ' . $old_ticket->getPhase()->getName() . ' updated to ' . $new_phase->getName() . '. ';
    $comment = 'Ticket moved from ' . $old_ticket->getPhase()->getName() . ' to ' . $new_phase->getName();
    $old_ticket->addHistory($_SESSION['userid'], $comment);
} else {
    $phase = $old_ticket->getPhaseId();
}

if ($resp !== $old_ticket->getResponsibleId()) {
    $log .= 'Responsible: from <a href="mailto:' . $old_user->getEmail() . '">' . $old_user->getName() . '</a> to <a href="mailto:' . $new_user->getEmail() . '">' . $new_user->getName() . '</a>. ';

    $GLOBALS['email']->setRecipient($old_user);
    $GLOBALS['email']->setSubject("You are no longer responsible of ticket: " . $title);
    $GLOBALS['email']->setMessage("You are no longer responsible of ticket: " . $title . ". New responsible is " . $new_user->getName() . '<br><br>');
    $GLOBALS['email']->generateTicketFooter($old_ticket);
    $GLOBALS['email']->send();

    $GLOBALS['email']->setRecipient($new_user);
    $GLOBALS['email']->setSubject("You are new responsible of ticket: " . $title);
    $GLOBALS['email']->setMessage("You are new responsible of ticket: " . $title . '<br><br>');
    $GLOBALS['email']->generateTicketFooter($old_ticket);
    $GLOBALS['email']->send();
} else {
    $GLOBALS['email']->setRecipient($new_user);
    $GLOBALS['email']->setSubject("Your ticket was modified");
    $GLOBALS['email']->setMessage("Ticket " . $title . " was modified by " . $_SESSION['username'] . '.<br><br>');
    $GLOBALS['email']->generateTicketFooter($old_ticket);
    $GLOBALS['email']->send();
}

if ($prio !== $old_ticket->getPriorityId()) {
    $new_prio = new Priority($prio);
    $log .= 'Priority: ' . $old_ticket->getPriority()->getName() . ' updated to ' . $new_prio->getName() . '. ';
}

if ($ref !== $old_ticket->getReferenceString()) {
    $log .= 'Reference: ' . $old_ticket->getReferenceString() . ' updated to ' . $ref . '. ';
}

if ($comp !== $old_ticket->getComponentId()) {
    $old_comp = $old_ticket->getComponent();
    $new_comp = new Component($comp);

    $log .= 'Component: ' . $old_comp->getName() . ' updated to ' . $new_comp->getName() . '. ';
}

$GLOBALS['logger']->log($log);

$GLOBALS['db']->query("UPDATE ticket SET data = '" . $title . "',
                          info = '" . $info . "',
                          responsible = '" . $resp . "',
                          wip = '" . $wip . "',
                          priority = '" . $prio . "',
                          reference_id = '" . $ref . "',
                          component = '" . $comp . "',
                          phase = '" . $phase . "',
                          cycle = '" . $new_cycle_id . "',
                          last_change = UTC_TIMESTAMP() WHERE id = '" . $id . "' LIMIT 1");

$subscribers = $GLOBALS['db']->get_results("SELECT user_id FROM ticket_subscription WHERE ticket_id = '" . $id . "'");

if (!empty($resp) && $resp != $_SESSION['userid']) {
    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $old_ticket->getBoard()->getBoardId() . "', '" . $resp . "', '" . $_SESSION['username'] . " modified your ticket " . $title . "', 'page', 'page.board.php&ticket_id=" . $id . "', 'unread', UTC_TIMESTAMP())");
}

if ($subscribers) {
    $GLOBALS['email']->setSubject("Ticket was updated!");
    foreach ($subscribers as $subscriber) {
        if ($subscriber->user_id != $_SESSION['userid']) {
            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $old_ticket->getBoard()->getBoardId() . "', '" . $subscriber->user_id . "', '" . $_SESSION['username'] . " updated ticket " . $title . "', 'page', 'page.board.php&ticket_id=" . $id . "', 'unread', UTC_TIMESTAMP())");
        }

        $sub = new User($subscriber->user_id);
        $GLOBALS['email']->setRecipient($log);
        $GLOBALS['email']->send();
    }
}

$ticket_subscribers = $GLOBALS['db']->get_results("SELECT email FROM ticket_email_subscription WHERE ticket_id = '" . $id . "'");
if ($ticket_subscribers) {
    $GLOBALS['email']->setSubject("Ticket was updated!");
    foreach ($ticket_subscribers as $subscriber) {
        $GLOBALS['email']->setAddress($subscriber->email);
        $GLOBALS['email']->send();
    }
}

if (isset($parent)) {
    if ($parent != 0) {
        $res = $GLOBALS['db']->get_row("SELECT * FROM ticket_link WHERE child = '" . $id . "' LIMIT 1");
        if ($GLOBALS['db']->num_rows > 0) {
            $GLOBALS['db']->query("UPDATE ticket_link SET parent = '" . $parent . "' WHERE child = '" . $id . "' LIMIT 1");
        } else {
            $GLOBALS['db']->query("INSERT INTO ticket_link (parent, child) VALUES ('" . $parent . "', '" . $id . "')");
        }
    } else {
        $GLOBALS['db']->query("DELETE FROM ticket_link WHERE child = '" . $id . "' LIMIT 1");
    }
}

$old_ticket->getBoard()->updateClients();
?>
