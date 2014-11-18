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

/* this script should be run as daily cron job */

require_once './base/config/global.config.php';
require_once './base/config/database.config.php';
require_once './base/3rdparty/ezSQL/shared/ez_sql_core.php';
require_once './base/3rdparty/ezSQL/mysql/ez_sql_mysql.php';

/* Connect to database */
$GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

$boards = $GLOBALS['db']->get_results("SELECT * FROM board");

/* Set SMTP conf */
ini_set("smtp", $GLOBALS['smtp_server']);
ini_set("smtp_port", $GLOBALS['smtp_port']);

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
$headers .= "From: Haggard boards <no-reply@haggard>\r\n";

if (function_exists('apc_clear_cache')) {
    // Clear all from user cache
    apc_clear_cache('user');
}

foreach ($boards as $board) {
    /* Phase statistics */
    $phase_stats = $GLOBALS['db']->get_results("SELECT * FROM phase_day_stat WHERE date = UTC_DATE() AND board_id = '" . $board->id . "'");
    if ($GLOBALS['db']->num_rows == 0) {
        $phs = $GLOBALS['db']->get_results("SELECT * FROM phase WHERE board_id = '" . $board->id . "' AND active = '1'");

        if ($GLOBALS['db']->num_rows > 0) {
            foreach ($phs as $phase) {
                $count = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE phase = '" . $phase->id . "' AND board_id = '" . $board->id . "' AND active = '1' AND deleted = '0'");
                $GLOBALS['db']->query("INSERT INTO phase_day_stat VALUES ('', '" . $board->id . "', '" . $phase->id . "', '" . $count . "', UTC_DATE())");
            }
        }
    }

    /* User statistics */
    $user_stats = $GLOBALS['db']->get_results("SELECT * FROM user_day_stat WHERE date = UTC_DATE() AND board_id = '" . $board->id . "'");
    if ($GLOBALS['db']->num_rows == 0) {
        $usrs = $GLOBALS['db']->get_results("SELECT u.* FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '" . $board->id . "'");

        if ($GLOBALS['db']->num_rows > 0) {
            foreach ($usrs as $user) {
                $count = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE responsible = '" . $user->id . "' AND board_id = '" . $board->id . "' AND active = '1' AND deleted = '0'");
                $GLOBALS['db']->query("INSERT INTO user_day_stat VALUES ('', '" . $board->id . "', '" . $user->id . "', '" . $count . "', UTC_DATE())");
            }
        }
    }

    /* Activity statistics */
    $activity_stats = $GLOBALS['db']->get_results("SELECT * FROM board_activity_stat WHERE date = UTC_DATE() AND board_id = '" . $board->id . "'");
    if ($GLOBALS['db']->num_rows == 0) {
        $count = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '" . $board->id . "' AND DATE(date) = CURDATE()");
        $GLOBALS['db']->query("INSERT INTO board_activity_stat VALUES ('', '" . $board->id . "', '" . $count . "', UTC_DATE())");
    }

    /* Auto archiving */
    $auto_archive = $GLOBALS['db']->get_var("SELECT value FROM board_setting WHERE data = 'AUTO_ARCHIVE' AND board_id = '" . $board->id . "'");
    if (isset($auto_archive)) {
        if ($auto_archive == 1) {
            $phases = $GLOBALS['db']->get_var("SELECT value FROM board_setting WHERE data = 'AUTO_ARCHIVE_PHASES' AND board_id = '" . $board->id . "'");
            if (isset($phases)) {
                $threshold = $GLOBALS['db']->get_var("SELECT value FROM board_setting WHERE data = 'AUTO_ARCHIVE_THRESHOLD' AND board_id = '" . $board->id . "'");

                if (isset($threshold)) {
                    $phases = explode(',', $phases);
                    if (count($phases) > 0) {
                        foreach ($phases as $phase) {
                            $GLOBALS['db']->query("UPDATE ticket SET active = '0' WHERE phase = '" . $phase . "' AND board_id = '" . $board->id . "' AND DATE(last_change) < DATE_SUB(UTC_TIMESTAMP(), INTERVAL " . $threshold . " WEEK)");
                        }
                    }
                }
            }
        }
    }

    /* Send automatic emails for board users with no activity */
    $activity = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '" . $board->id . "' AND DATE(date) BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 WEEK) AND CURDATE()");
    if ($activity == 0) {
        $users = $GLOBALS['db']->get_results("SELECT * FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '" . $board->id . "'");
        if ($GLOBALS['db']->num_rows > 0) {
            foreach ($users as $user) {
                $subject = "No board activity";
                $message = "You are registered to board " . $board->name . " and there has not been activity during last two weeks. If there is no activity during next week the board will be deleted by Haggard admins.";
                $message .= '<br><br>If you want to delete the board now or you want to prevent deletion please contact <a href="mailto:haggard_admins">Haggard admins</a> ';

                $message .= "or login to " . $board->url . " and login with your username " . $user->noe_account . ".";
                $message .= '<br><br>Please do not reply to this message';
//              mail($user->email, $subject, $message, $headers);
            }

        }
    }
}

$GLOBALS['db']->query("UPDATE ticket SET deleted = '1' WHERE active = '0' AND DATE(last_change) < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 9 MONTH)");
?>
