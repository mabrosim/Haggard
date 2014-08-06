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

if ($args['func'] == "check_permission") {
    if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('move_ticket')) {
        echo "";
        return;
    }

    echo "true";
    return;
}

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('move_ticket')) {
    return;
}

require_once './ticket.class.php';
require_once './cycle.class.php';
require_once './phase.class.php';

if ($args['func'] == "comment") {
    $item = $GLOBALS['db']->escape($args['item']);
    $reason = strip_tags($GLOBALS['db']->escape($args['reason']));

    $ticket = new Ticket($item);

    $comment = 'Force comment: ' . $reason;
    $ticket->addComment($_SESSION['userid'], $comment);

    $log = $_SESSION['username'] . 'force commented ticket ' . $ticket->getTitle() . ' : ' . $reason . '.';
    $GLOBALS['logger']->log($log);
} else if ($args['func'] == "updateData") {
    if (!isset($args['item']) || !isset($args['phase']) || !isset($args['cycle'])) {
        return;
    }

    $item = $GLOBALS['db']->escape($args['item']);
    $phase = $GLOBALS['db']->escape($args['phase']);
    $cycle_id = $GLOBALS['db']->escape($args['cycle']);

    $ticket = new Ticket($item);
    $cycle = new Cycle($cycle_id);
    $new_phase = new Phase($phase);
    $old_phase = $ticket->getPhase();

    if ($old_phase->getId() != $phase) {
        $notify = false;
        if ($old_phase->getNotifyEmpty() && $old_phase->getNumTickets() == 1) {
            $notify = true;
        }

        $log = $_SESSION['username'] . ' moved ticket "' . $ticket->getTitle() . '" from ' . $old_phase->getName() . ' to ' . $new_phase->getName();
        if ($GLOBALS['board']->getSettingValue("USE_CYCLES")) {
            $log .= ' (' . $cycle->getName() . ')';
        }

        $GLOBALS['logger']->log($log);

        if ($ticket->getResponsibleId() != $_SESSION['userid']) {
            $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES
                                                                     ('" . $GLOBALS['board']->getBoardId() . "',
                                                                     '" . $ticket->getResponsibleId() . "',
                                                                     '" . $_SESSION['username'] . " moved ticket " . $ticket->getTitle() . " from " . $old_phase->getName() . " to " . $new_phase->getName() . "',
                                                                     'page',
                                                                     'page.board.php&ticket_id=" . $ticket->getId() . "',
                                                                     'unread',
                                                                     UTC_TIMESTAMP())");
        }


        $log = 'Ticket moved from ' . $old_phase->getName() . ' to ' . $new_phase->getName();
        $ticket->addHistory($_SESSION['userid'], $log);

        $GLOBALS['db']->query("INSERT INTO ticket_stat (ticket_id, cycle_id, old_phase, new_phase, created) VALUES
                                  ('" . $item . "', '" . $cycle_id . "', '" . $old_phase->getId() . "',
                                  '" . $phase . "', UTC_TIMESTAMP())");
        $query = "UPDATE ticket SET phase = " . $phase . ", last_change = UTC_TIMESTAMP() WHERE id = '" . $item . "' LIMIT 1";
        $GLOBALS['db']->query($query);

        if ($new_phase->getId() == 0) {
            $query = "UPDATE ticket SET cycle = 0 WHERE id = '" . $item . "' LIMIT 1";
            $GLOBALS['db']->query($query);
        } else {
            $query = "UPDATE ticket SET cycle = '" . $cycle->getId() . "' WHERE id = '" . $item . "' LIMIT 1";
            $GLOBALS['db']->query($query);
        }

        $ticket->setPhaseId($phase);

        /* Send email */
        $GLOBALS['email']->setReceipient($ticket->getResponsible());
        $GLOBALS['email']->ticketMove($ticket);
        $GLOBALS['email']->send();

        $t_subscribers = $GLOBALS['db']->get_results("SELECT user_id FROM ticket_subscription WHERE ticket_id = '" . $ticket->getId() . "'");
        if ($t_subscribers) {
            foreach ($subscribers as $subscriber) {
                if ($subscriber->user_id != $_SESSION['userid']) {
                    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time)  VALUES
                            ('" . $GLOBALS['board']->getBoardId() . "',
                            '" . $subscriber->user_id . "',
                            '" . $_SESSION['username'] . " moved ticket " . $ticket->getTitle() . " from " . $old_phase->getName() . " to " . $new_phase->getName() . "',
                            'page',
                            'page.board.php&ticket_id=" . $ticket->getId() . "',
                            'unread',
                            UTC_TIMESTAMP())");

                    $sub = new User($subscriber->user_id);
                    $GLOBALS['email']->setReceipient($sub);
                    $GLOBALS['email']->send();
                }
            }
        }

        $te_subscribers = $GLOBALS['db']->get_results("SELECT email FROM ticket_email_subscription WHERE ticket_id = '" . $ticket->getId() . "'");
        if ($te_subscribers) {
            foreach ($te_subscribers as $subscriber) {
                if ($subscriber->user_id != $_SESSION['userid']) {
                    $GLOBALS['email']->setAddress($subscriber->email);
                    $GLOBALS['email']->send();
                }
            }
        }

        $p_subscribers = $GLOBALS['db']->get_results("SELECT * FROM phase_subscription ps LEFT JOIN user u ON ps.user_id = u.id WHERE phase_id = '" . $new_phase->getId() . "' OR phase_id = '" . $old_phase->getId() . "' GROUP BY u.id");
        if ($p_subscribers) {
            foreach ($p_subscribers as $subscriber) {
                if ($subscriber->user_id != $_SESSION['userid']) {
                    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES
                            ('" . $GLOBALS['board']->getBoardId() . "',
                                '" . $subscriber->user_id . "',
                                '" . $_SESSION['username'] . " moved ticket " . $ticket->getTitle() . " from " . $old_phase->getName() . " to " . $new_phase->getName() . "',
                                'page',
                                'page.board.php&ticket_id=" . $ticket->getId() . "',
                                'unread',
                                UTC_TIMESTAMP())");
                    $GLOBALS['email']->setAddress($subscriber->email);
                    $GLOBALS['email']->send();
                }
            }
        }

        $pe_subscribers = $GLOBALS['db']->get_results("SELECT * FROM phase_email_notification ps LEFT JOIN user_group_link ug ON ug.group_id = ps.group_id LEFT JOIN user u ON ug.user_id = u.id WHERE phase_id = '" . $new_phase->getId() . "' OR phase_id = '" . $old_phase->getId() . "' GROUP BY u.id");
        if ($pe_subscribers) {
            foreach ($pe_subscribers as $subscriber) {
                $GLOBALS['email']->setAddress($subscriber->email);
                $GLOBALS['email']->send();
            }
        }

        /* Check for this tickets parent and it's children */
        $parent = $ticket->getParent();
        if ($parent && $parent->getId() != 0) {
            $send_mail_to_parent = true;
            $children = $parent->getChildren();
            foreach ($children as $child) {
                if ($child->getId() == $ticket->getId()) {
                    continue;
                }
                if ($child->getPhaseId() != $phase) {
                    $send_mail_to_parent = false;
                    break;
                }
            }

            if ($send_mail_to_parent == true) {
                if ($parent->getResponsibleId() != $_SESSION['userid']) {
                    $GLOBALS['db']->query("INSERT INTO user_notification (board_id, user_id, title, type, link, status, time) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $parent->getResponsibleId() . "', 'All children of " . $parent->getTitle() . " are in " . $new_phase->getName() . "', 'page', 'page.board.php&ticket_id=" . $parent->getId() . "', 'unread', UTC_TIMESTAMP())");
                }

                $GLOBALS['email']->setReceipient($parent->getResponsible());
                $GLOBALS['email']->ticketchildrensamephase($parent, $new_phase);
                $GLOBALS['email']->send();
            }
        }

        if ($notify == true) {
            $notifyMsg = "The phase does not have any tickets!";
            $subscribers = $GLOBALS['db']->get_results("SELECT * FROM phase_email_notification ps LEFT JOIN user_group_link ug ON ug.group_id = ps.group_id LEFT JOIN user u ON ug.user_id = u.id WHERE phase_id = '" . $old_phase->getId() . "'");
            if ($GLOBALS['db']->num_rows > 0) {
                foreach ($subscribers as $subscriber) {
                    $GLOBALS['email']->setAddress($subscriber->email);
                    $GLOBALS['email']->setSubject("Phase " . $old_phase->getName() . " is now empty");
                    $GLOBALS['email']->setMessage($notifyMsg);
                    $GLOBALS['email']->send();
                }
            }
        }


        $GLOBALS['board']->updateClients();
    }
} else if ($args['func'] == "updateWIP") {
    if (!$GLOBALS['board']->getSettingValue("USE_WIP")) {
        echo '{ "current" : "0", "cycle" : "0", "left" : "0" }';
        return;
    }

    $p_id = $c_id = 0;
    $cycle = $GLOBALS['db']->escape($args['cycle']);

    if (isset($args['p_id'])) {
        $p_id = $GLOBALS['db']->escape($args['p_id']);
    }

    if (isset($args['c_id'])) {
        $c_id = $GLOBALS['db']->escape($args['c_id']);
    }

    $query = "SELECT SUM(wip) FROM ticket WHERE cycle = '" . $cycle . "' AND active = '1'";

    if (strstr($p_id, "p_")) {
        $p_id = substr($p_id, 2);
    }
    if (strstr($c_id, "p_")) {
        $c_id = substr($c_id, 2);
    }

    if ($p_id != 0 && $p_id != "0" && $p_id != "") {
        $query .= " AND responsible='" . $p_id . "'";
    }

    if ($c_id != 0 && $c_id != "0" && $c_id != "") {
        $query .= " AND component='" . $c_id . "'";
    }

    $current_wip = $GLOBALS['db']->get_var($query);
    if (!$current_wip) {
        $current_wip = 0;
    }

    $query .= " AND phase >= 1 AND phase <= 5";
    $wip_left = $GLOBALS['db']->get_var($query);
    if (!$wip_left) {
        $wip_left = 0;
    }

    $cycle_wip = $GLOBALS['db']->get_var("SELECT wip_limit FROM cycle WHERE id = '" . $cycle . "' LIMIT 1");
    if (!$cycle_wip) {
        $cycle_wip = 0;
    }

    echo '{ "current" : "' . $current_wip . '", "cycle" : "' . $cycle_wip . '", "left" : "' . $wip_left . '" }';
} else if ($args['func'] == "check_for_wip_limit") {
    if (!$GLOBALS['board']->getSettingValue("USE_WIP")) {
        echo '1';
        return;
    }

    $phase_id = $GLOBALS['db']->escape($args['phase']);

    $phase = new Phase($phase_id);

    if ($phase->getWIPLimit() == 0 && $phase->getTicketLimit() == 0) {
        echo '1';
        return;
    }

    $tickets = $phase->getNumTickets();
    if (($tickets + 1) > $phase->getTicketLimit()) {
        echo '0';
        return;
    }

    if (!$GLOBALS['board']->getSettingValue('USE_WIP')) {
        echo '1';
        return;
    }

    $item_id = $GLOBALS['db']->escape($args['item']);
    $ticket = new Ticket($item_id);

    $current_wip = $phase->getCurrentWIP();
    $current_wip += $ticket->getWIP();

    if ($current_wip > $phase->getWIPLimit()) {
        echo '0';
    } else {
        echo '1';
    }
}
?>
