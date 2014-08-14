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

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_board_settings') || !isset($_SESSION['username'])) {
    return;
}

$func = filter_input(INPUT_POST, 'func');

if (!isset($func)) {
    return;
}

$args = filter_input_array(INPUT_POST);

if ($func == "board_settings") {
    $wip = $GLOBALS['db']->escape($args['use_wip']);
    $cycles = $GLOBALS['db']->escape($args['use_cycles']);
    $linking = $GLOBALS['db']->escape($args['use_linking']);
    $statistics = $GLOBALS['db']->escape($args['use_statistics']);
    $logging = $GLOBALS['db']->escape($args['use_logging']);
    $priorities = $GLOBALS['db']->escape($args['use_priorities']);
    $ticket_help = $GLOBALS['db']->escape($args['show_ticket_help']);
    $firstname = $GLOBALS['db']->escape($args['use_firstname']);
    $private_b = $GLOBALS['db']->escape($args['private_board']);
    $send_email = $GLOBALS['db']->escape($args['send_email']);

    $color1 = $GLOBALS['db']->escape($args['color1']);
    $color2 = $GLOBALS['db']->escape($args['color2']);
    $color3 = $GLOBALS['db']->escape($args['color3']);
    $color4 = $GLOBALS['db']->escape($args['color4']);

    if (!$color1) {
        $color1 = "#9e9cd3";
    }
    if (!$color2) {
        $color2 = "ffdc00";
    }
    if (!$color3) {
        $color3 = "ff9800";
    }
    if (!$color4) {
        $color4 = "7eec0e";
    }

    $ticket_type1 = $GLOBALS['db']->escape($args['ticket_type1']);
    $ticket_type2 = $GLOBALS['db']->escape($args['ticket_type2']);
    $ticket_type3 = $GLOBALS['db']->escape($args['ticket_type3']);
    $ticket_type4 = $GLOBALS['db']->escape($args['ticket_type4']);

    if (!$ticket_type1) {
        $ticket_type1 = "Backlog";
    }
    if (!$ticket_type2) {
        $ticket_type2 = "Developent";
    }
    if (!$ticket_type3) {
        $ticket_type3 = "Test";
    }
    if (!$ticket_type4) {
        $ticket_type4 = "Study";
    }

    $use_wip = 0;
    $use_cycles = 0;
    $use_linking = 0;
    $use_statistics = 0;
    $use_logging = 0;
    $use_priorities = 0;
    $show_ticket_help = 0;
    $use_firstname = 0;
    $setting_private_board = 0;
    $setting_send_email = 0;

    if (isset($wip) && $wip != "") {
        $use_wip = 1;
    }
    if (isset($cycles) && $cycles != "") {
        $use_cycles = 1;
    }
    if (isset($linking) && $linking != "") {
        $use_linking = 1;
    }
    if (isset($statistics) && $statistics != "") {
        $use_statistics = 1;
    }
    if (isset($logging) && $logging != "") {
        $use_logging = 1;
    }
    if (isset($priorities) && $priorities != "") {
        $use_priorities = 1;
    }
    if (isset($ticket_help) && $ticket_help != "") {
        $show_ticket_help = 1;
    }
    if (isset($firstname) && $firstname != "") {
        $use_firstname = 1;
    }
    if (isset($private_b) && $private_b != "") {
        $setting_private_board = 1;
    }
    if (isset($send_email) && $send_email != "") {
        $setting_send_email = 1;
    }

    $GLOBALS['board']->setSettingValue('BOARD_TEAM', $args['board_team']);
    $GLOBALS['board']->setSettingValue('BOARD_TEAM_EMAIL', $args['board_team_email']);
    $GLOBALS['board']->setSettingValue('PRIVATE_BOARD', $setting_private_board);
    $GLOBALS['board']->setSettingValue('SEND_EMAIL', $setting_send_email);
    $GLOBALS['board']->setSettingValue('USE_WIP', $use_wip);
    $GLOBALS['board']->setSettingValue('USE_CYCLES', $use_cycles);
    $GLOBALS['board']->setSettingValue('USE_LINKING', $use_linking);
    $GLOBALS['board']->setSettingValue('USE_STATISTICS', $use_statistics);
    $GLOBALS['board']->setSettingValue('USE_LOGGING', $use_logging);
    $GLOBALS['board']->setSettingValue('USE_PRIORITIES', $use_priorities);
    $GLOBALS['board']->setSettingValue('SHOW_TICKET_HELP', $show_ticket_help);
    $GLOBALS['board']->setSettingValue('USE_FIRSTNAME', $use_firstname);
    $GLOBALS['board']->setSettingValue('TICKET_COLOR1', $color1);
    $GLOBALS['board']->setSettingValue('TICKET_COLOR2', $color2);
    $GLOBALS['board']->setSettingValue('TICKET_COLOR3', $color3);
    $GLOBALS['board']->setSettingValue('TICKET_COLOR4', $color4);
    $GLOBALS['board']->setSettingValue('TICKET_TYPE1', $ticket_type1);
    $GLOBALS['board']->setSettingValue('TICKET_TYPE2', $ticket_type2);
    $GLOBALS['board']->setSettingValue('TICKET_TYPE3', $ticket_type3);
    $GLOBALS['board']->setSettingValue('TICKET_TYPE4', $ticket_type4);

    if ($use_cycles == 0) {
        $timenow = date("Y-d-m H:i:s");
        $query = "SELECT * FROM cycle WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "'";
        $res = $GLOBALS['db']->get_results($query);

        if ($GLOBALS['db']->num_rows == 0) {
            /* No cycles so create one */
            $GLOBALS['db']->query("INSERT INTO cycle (board_id, data, start, stop, wip_limit, active) VALUES ('" . $GLOBALS['board']->getBoardId() . "',  'foreverAlone', '" . $timenow . "', '2022-01-01 00:00:00', '0', '1')");
        } else {
            $timenow = time();
            foreach ($res as $cycle) {
                $cycle_start = strtotime($cycle->start);
                $cycle_stop = strtotime($cycle->stop);

                /* Do not delete current cycle */
                if ($cycle_start <= $timenow && $cycle_stop >= $timenow) {
                    $GLOBALS['db']->query("UPDATE cycle SET stop = '2022-01-01 00:00:00' WHERE id = '" . $cycle->id . "'");
                    continue;
                }

                /* Delete all other cycles, tickets and ticket comments
                 */
                $cycle_id = $cycle->id;
                $ticket_q = $GLOBALS['db']->get_results("SELECT id FROM ticket WHERE cycle = '" . $cycle_id . "'");

                foreach ($ticket_q as $ticket) {
                    $GLOBALS['db']->query("DELETE FROM ticket_comment WHERE ticket_id = '" . $ticket->id . "'");
                    $GLOBALS['db']->query("DELETE FROM ticket_link WHERE parent = '" . $ticket->id . "' OR child = '" . $ticket->id . "'");
                    $GLOBALS['db']->query("DELETE FROM ticket_subscription WHERE ticket_id = '" . $ticket_id . "'");
                }

                $GLOBALS['db']->query("DELETE FROM ticket_stat WHERE cycle_id = '" . $cycle_id . "'");
                $GLOBALS['db']->query("DELETE FROM ticket WHERE cycle = '" . $cycle_id . "'");
                $GLOBALS['db']->query("DELETE FROM cycle WHERE id = '" . $cycle_id . "'");
            }
        }
    }
} else if ($args['func'] == "auto_archive") {
    $enabled = $GLOBALS['db']->escape($args['enabled']);
    $threshold = $GLOBALS['db']->escape($args['threshold']);
    $phases = $GLOBALS['db']->escape($args['phases']);

    if ($enabled == 0 || $enabled == "0") {
        $GLOBALS['board']->setSettingValue("AUTO_ARCHIVE", "0");
    } else {
        $GLOBALS['board']->setSettingValue("AUTO_ARCHIVE", "1");
        $GLOBALS['board']->setSettingValue("AUTO_ARCHIVE_THRESHOLD", $threshold);
        $GLOBALS['board']->setSettingValue("AUTO_ARCHIVE_PHASES", $phases);
    }
} else if ($args['func'] == "mass_archive") {
    $limit = $GLOBALS['db']->escape($args['older']);
    $phase = $GLOBALS['db']->escape($args['phase']);

    if (!isset($limit) || !isset($phase)) {
        return;
    }

    $time = strtotime($limit);

    if (!$time) {
        return;
    }

    $older = date('Y-m-d H:i:s', $time);

    $GLOBALS['db']->query("UPDATE ticket SET active = '0' WHERE phase = '" . $phase . "' AND last_change <= '" . $older . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
} else if ($args['func'] == "import_data") {
    $username = $GLOBALS['db']->escape($args['username']);
    $password = $GLOBALS['db']->escape($args['password']);
    $database = $GLOBALS['db']->escape($args['name']);
    $host = $GLOBALS['db']->escape($args['host']);

    $old = new ezSQL_mysql($username, $password, $database, $host);

    if (!$old) {
        echo 'Could not connect to database!';
        return;
    }

    $board_id = $GLOBALS['board']->getBoardId();

    $board_settings = $old->get_results("SELECT * FROM board_settings");
    if ($board_settings) {
        foreach ($board_settings as $setting) {
            $GLOBALS['board']->setSettingValue($setting->data, $setting->value);
        }
    }

    $components = $old->get_results("SELECT * FROM components");

    if ($components) {
        foreach ($components as $component) {
            $data = $GLOBALS['db']->escape($component->name);
            $GLOBALS['db']->query("INSERT INTO component (board_id, name) VALUES ('" . $board_id . "', '" . $data . "')");
        }
    }

    $tickets = $GLOBALS['db']->get_results("SELECT id FROM ticket WHERE board_id = '" . $board_id . "'");

    $GLOBALS['db']->query("DELETE FROM cycle WHERE board_id = '" . $board_id . "'");
    $cycles = $old->get_results("SELECT * FROM cycles");

    if ($cycles) {
        $timenow = time();
        foreach ($cycles as $cycle) {

            $data = $GLOBALS['db']->escape($cycle->data);
            $GLOBALS['db']->query("INSERT INTO cycle (board_id, data, start, stop, wip_limit, active) VALUES ('" . $board_id . "', '" . $data . "', '" . $cycle->start . "', '" . $cycle->stop . "', '" . $cycle->wip_limit . "', '" . $cycle->active . "')");

            $cycle_start = strtotime($cycle->start);
            $cycle_stop = strtotime($cycle->stop);

            if ($cycle_start <= $timenow && $cycle_stop >= $timenow) {
                $new_cycle = $GLOBALS['db']->insert_id;
                foreach ($tickets as $ticket) {
                    $GLOBALS['db']->query("UPDATE ticket SET cycle = '" . $new_cycle . "' WHERE id = '" . $ticket->id . "' LIMIT 1");
                }
            }
        }
    }

    $logs = $old->get_results("SELECT * FROM log");

    if ($logs) {
        foreach ($logs as $log) {
            $data = $GLOBALS['db']->escape($log->data);
            $GLOBALS['db']->query("INSERT INTO log (board_id, data, date) VALUES ('" . $board_id . "', '" . $data . "', '" . $log->date . "')");
        }
    }

    $users = $old->get_results("SELECT * FROM persons");

    if ($users) {
        foreach ($users as $user) {
            $name = $GLOBALS['db']->escape($user->name);
            $email = $GLOBALS['db']->escape($user->email);
            $exists = $GLOBALS['db']->get_var("SELECT id FROM user WHERE name = '" . $name . "' AND email = '" . $email . "' AND board_id = '" . $board_id . "'");
            if (!$exists) {
                $GLOBALS['db']->query("INSERT INTO user (name, email, type) VALUES ('" . $name . "', '" . $email . ", 'USER')");
                $uid = $GLOBALS['db']->insert_id;
                $GLOBALS['db']->query("INSERT INTO user_board (user_id, board_id) VALUES ('" . $uid . "', '" . $board_id . "')");
            }
        }
    }

    $phases = $old->get_results("SELECT * FROM phases");

    if ($phases) {
        $phase_num = 0;
        foreach ($phases as $phase) {
            $name = $GLOBALS['db']->escape($phase->name);
            $help = $GLOBALS['db']->escape($phase->help);

            $cur_phase = $GLOBALS['db']->get_var("SELECT id FROM phase WHERE board_id = '" . $board_id . "' ORDER BY id ASC LIMIT " . $phase_num . ", 1");

            if ($cur_phase) {
                $GLOBALS['db']->query("UPDATE phase SET name = '" . $name . "', help = '" . $help . "', active = '" . $phase->active . "', force_comment = '" . $phase->force_comment . "', wip_limit = '" . $phase->wip_limit . "' WHERE id = '" . $cur_phase . "' LIMIT 1");
            } else {
                $GLOBALS['db']->query("INSERT INTO phase VALUES ('', '" . $board_id . "', '" . $name . "', '" . $phase->css . "', '" . $help . "', '" . $phase->active . "', '" . $phase->force_comment . "', '" . $phase->wip_limit . "')");
            }
            $phase_num++;
        }
    }

    $old_tickets = $old->get_results("SELECT * FROM tickets");

    if ($tickets) {
        foreach ($old_tickets as $ticket) {
            $responsible = $old->get_var("SELECT name FROM persons WHERE id = '" . $ticket->responsible . "'");
            $new_resp_id = $GLOBALS['db']->get_var("SELECT id FROM user WHERE name = '" . $responsible . "' AND board_id = '" . $board_id . "'");

            $ticket_cycle = 0;
            if ($ticket->cycle == 0) {
                $ticket_cycle = $old->get_var("SELECT data FROM cycles WHERE stop >= UTC_TIMESTAMP() AND start <= UTC_TIMESTAMP()");
            } else {
                $ticket_cycle = $old->get_var("SELECT data FROM cycles WHERE id = '" . $ticket->cycle . "'");
            }

            $cycle = $GLOBALS['db']->escape($ticket_cycle);
            $new_cycle_id = $GLOBALS['db']->get_var("SELECT id FROM cycle WHERE data = '" . $cycle . "' AND board_id = '" . $board_id . "'");

            $phase = $old->get_var("SELECT name FROM phases WHERE id = '" . $ticket->phase . "'");
            $new_phase_id = $GLOBALS['db']->get_var("SELECT id FROM phase WHERE name = '" . $phase . "' AND board_id = '" . $board_id . "'");

            $component = $old->get_var("SELECT name FROM components WHERE id = '" . $ticket->component . "'");
            $new_component_id = $GLOBALS['db']->get_var("SELECT id FROM component WHERE name = '" . $component . "' AND board_id = '" . $board_id . "'");

            $data = $GLOBALS['db']->escape($ticket->data);
            $ref = $GLOBALS['db']->escape($ticket->reference_id);

            $GLOBALS['db']->query("INSERT INTO ticket VALUES ('', '" . $board_id . "', '" . $data . "', '" . $new_resp_id . "', '" . $ticket->wip . "', '" . $new_cycle_id . "', '" . $new_phase_id . "', '" . $ticket->priority . "', '" . $ref . "', '" . $ticket->active . "', '0', '" . $new_component_id . "', '" . $ticket->last_change . "', UTC_TIMESTAMP())");

            $new_ticket_id = $GLOBALS['db']->insert_id;

            $ticket_comments = $old->get_results("SELECT * FROM ticket_comments WHERE ticket_id = '" . $ticket->id . "'");
            if ($ticket_comments) {
                foreach ($ticket_comments as $ticket_comment) {
                    $sender = $old->get_var("SELECT name FROM persons WHERE id = '" . $ticket_comment->sender . "'");
                    $new_sender_id = $GLOBALS['db']->get_var("SELECT id FROM user WHERE name = '" . $sender . "' AND board_id = '" . $board_id . "'");

                    $comment = $GLOBALS['db']->escape($ticket_comment->data);

                    $GLOBALS['db']->query("INSERT INTO ticket_comment VALUES ('', '" . $new_ticket_id . "', '" . $new_sender_id . "', '" . $comment . "', '" . $ticket_comment->date . "')");
                }
            }
        }
    }

    $ticket_links = $old->get_results("SELECT * FROM ticket_links");

    if ($ticket_links) {
        foreach ($ticket_links as $link) {
            $parent = $old->get_var("SELECT data FROM tickets WHERE id = '" . $link->parent . "'");
            $new_parent_id = $GLOBALS['db']->get_var("SELECT id FROM ticket WHERE data = '" . $parent . "' AND board_id = '" . $board_id . "'");

            $child = $old->get_var("SELECT data FROM tickets WHERE id = '" . $link->child . "'");
            $new_child_id = $GLOBALS['db']->get_var("SELECT id FROM ticket WHERE data = '" . $child . "' AND board_id = '" . $board_id . "'");

            $GLOBALS['db']->query("INSERT INTO ticket_link VALUES ('" . $new_parent_id . "', '" . $new_child_id . "')");
        }
    }

    $ticket_stats = $old->get_results("SELECT * FROM ticket_stats");

    if ($ticket_stats) {
        foreach ($ticket_stats as $stat) {
            $ticket = $old->get_var("SELECT data FROM tickets WHERE id = '" . $stat->ticket_id . "'");
            $new_ticket_id = $GLOBALS['db']->get_var("SELECT id FROM ticket WHERE data = '" . $ticket . "' AND board_id = '" . $board_id . "'");

            $cycle = $old->get_var("SELECT data FROM cycles WHERE id = '" . $stat->cycle_id . "'");
            $new_cycle_id = $GLOBALS['db']->get_var("SELECT id FROM cycle WHERE data = '" . $cycle . "' AND board_id = '" . $board_id . "'");

            $new_phase = $old->get_var("SELECT name FROM phases WHERE id = '" . $stat->new_phase . "'");
            $new_phase_id = $GLOBALS['db']->get_var("SELECT id FROM phase WHERE name = '" . $new_phase . "' AND board_id = '" . $board_id . "'");

            $old_phase = $old->get_var("SELECT name FROM phases WHERE id = '" . $stat->old_phase . "'");
            $old_phase_id = $GLOBALS['db']->get_var("SELECT id FROM phase WHERE name = '" . $old_phase . "' AND board_id = '" . $board_id . "'");

            $GLOBALS['db']->query("INSERT INTO ticket_stat VALUES ('" . $new_ticket_id . "', '" . $new_cycle_id . "', '" . $old_phase_id . "', '" . $new_phase_id . "', '" . $stat->date . "')");
        }
    }

    echo "Import succesful!";
} else if ($args['func'] == "delete_all_data") {
    if ($GLOBALS['cur_user']->getType() != "SYSTEM_ADMIN") {
        return;
    }

    $board_id = $GLOBALS['board']->getBoardId();

    $GLOBALS['db']->query("DELETE FROM board_activity_stat WHERE board_id = '" . $board_id . "'");
    $GLOBALS['db']->query("DELETE FROM board_setting WHERE board_id = '" . $board_id . "'");
    $GLOBALS['db']->query("DELETE FROM component WHERE board_id = '" . $board_id . "'");
    $GLOBALS['db']->query("DELETE FROM cycle WHERE board_id = '" . $board_id . "'");
    $GLOBALS['db']->query("DELETE FROM log WHERE board_id = '" . $board_id . "'");
    $GLOBALS['db']->query("DELETE FROM pagegen WHERE board_id = '" . $board_id . "'");

    $releases = $GLOBALS['db']->get_results("SELECT * FROM phase_release WHERE board_id = '" . $board_id . "'");
    if ($releases) {
        foreach ($releases as $release) {
            $GLOBALS['db']->query("DELETE FROM release_ticket WHERE release_id = '" . $release->id . "'");
            $GLOBALS['db']->query("DELETE FROM phase_release WHERE id = '" . $release->id . "'");
        }
    }

    $topics = $GLOBALS['db']->get_results("SELECT * FROM message_topic WHERE board_id = '" . $board_id . "'");

    if ($topics) {
        foreach ($topics as $topic) {
            $GLOBALS['db']->query("DELETE FROM message WHERE topic_id = '" . $topic->id . "'");
            $GLOBALS['db']->query("DELETE FROM message_topic WHERE id = '" . $topic->id . "' LIMIT 1");
        }
    }

    $tickets = $GLOBALS['db']->get_results("SELECT * FROM ticket WHERE board_id = '" . $board_id . "'");

    if ($tickets) {
        foreach ($tickets as $ticket) {
            $GLOBALS['db']->query("DELETE FROM ticket_link WHERE child = '" . $ticket->id . "' OR parent = '" . $ticket->id . "'");
            $GLOBALS['db']->query("DELETE FROM ticket_comment WHERE ticket_id = '" . $ticket->id . "'");
            $GLOBALS['db']->query("DELETE FROM ticket_history WHERE ticket_id = '" . $ticket->id . "'");
            $GLOBALS['db']->query("DELETE FROM ticket_stat WHERE ticket_id = '" . $ticket->id . "'");
            $GLOBALS['db']->query("DELETE FROM ticket_subscription WHERE ticket_id = '" . $ticket->id . "'");
            $GLOBALS['db']->query("DELETE FROM ticket WHERE id = '" . $ticket->id . "'");
        }
    }

    $phases = $GLOBALS['db']->get_results("SELECT * FROM phase WHERE board_id = '" . $board_id . "'");

    if ($phases) {
        foreach ($phases as $phase) {
            $GLOBALS['db']->query("DELETE FROM phase_email_notification WHERE phase_id = '" . $phase->id . "'");
            $GLOBALS['db']->query("DELETE FROM phase_subscription WHERE phase_id = '" . $phase->id . "'");
            $GLOBALS['db']->query("DELETE FROM phase WHERE id = '" . $phase->id . "'");
            $GLOBALS['db']->query("DELETE FROM phase_day_stat WHERE phase = '" . $phase->id . "' AND board_id = '" . $board_id . "'");
        }
    }

    $groups = $GLOBALS['db']->get_results("SELECT * FROM user_group WHERE board_id = '" . $board_id . "'");
    if ($groups) {
        foreach ($groups as $group) {
            $GLOBALS['db']->query("DELETE FROM group_permission WHERE group_id = '" . $group->id . "'");
            $GLOBALS['db']->query("DELETE FROM user_group WHERE id = '" . $group->id . "'");
        }
    }

    $users = $GLOBALS['db']->get_results("SELECT * FROM user WHERE board_id = '" . $board_id . "'");

    if ($users) {
        foreach ($users as $user) {
            $boards = $GLOBALS['db']->get_var("SELECT COUNT(1) FROM user_board WHERE user_id = '" . $user->id . "'");

            $GLOBALS['db']->query("DELETE FROM user_group_link WHERE user_id = '" . $user->id . "'");
            $GLOBALS['db']->query("DELETE FROM user_permission WHERE user_id = '" . $user->id . "' AND board_id = '" . $board_id . "'");
            $GLOBALS['db']->query("DELETE FROM user_board WHERE user_id = '" . $user->id . "' AND board_id = '" . $board_id . "'");
            $GLOBALS['db']->query("DELETE FROM user_day_stat WHERE user_id = '" . $user->id . "' AND board_id = '" . $board_id . "'");
            $GLOBALS['db']->query("DELETE FROM user_notification WHERE user_id = '" . $user->id . "' AND board_id = '" . $board_id . "'");
            // User only on this board
            if ($boards == 1) {
                $GLOBALS['db']->query("DELETE FROM personal_setting WHERE user_id = '" . $user->id . "'");
                $GLOBALS['db']->query("DELETE FROM message_topic WHERE user_id = '" . $user->id . "'");
                $GLOBALS['db']->query("DELETE FROM message WHERE user_id = '" . $user->id . "'");
                $GLOBALS['db']->query("DELETE FROM phase_subscription WHERE user_id = '" . $user->id . "'");
                $GLOBALS['db']->query("DELETE FROM ticket_email_subscription WHERE user_id = '" . $user->id . "'");
                $GLOBALS['db']->query("DELETE FROM user WHERE id = '" . $user->id . "' LIMIT 1");
            }
        }
    }

    $GLOBALS['db']->query("DELETE FROM board WHERE id = '" . $board_id . "' LIMIT 1");
    $fp = fopen('../../config/board.config.php', 'w');
    if ($fp) {
        fwrite($fp, '');
        fclose($fp);
    }
} else if ($args['func'] == "delete_archived_tickets") {
    $GLOBALS['db']->query("UPDATE ticket SET deleted = '1' WHERE active = '0' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
} else if ($args['func'] == "delete_all_tickets") {
    $GLOBALS['db']->query("UPDATE ticket SET deleted = '1' WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "'");
}
?>
