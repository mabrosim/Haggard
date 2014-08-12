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

require_once '/var/www/haggard/config/database.config.php';
require_once '/var/www/haggard/config/global.config.php';

require_once '/var/www/haggard/3rdparty/ezSQL/shared/ez_sql_core.php';
require_once '/var/www/haggard/3rdparty/ezSQL/mysql/ez_sql_mysql.php';

/* Connect to database */
$GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

$GLOBALS['db']->query("CREATE TABLE user_board (id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, board_id INT NOT NULL, PRIMARY KEY(id)) ENGINE MYISAM");

$GLOBALS['db']->query("ALTER TABLE user_permission ADD board_id INT NOT NULL AFTER id");
$GLOBALS['db']->query("ALTER TABLE user_notification ADD board_id INT NOT NULL AFTER id");

$users = $GLOBALS['db']->get_results("SELECT * FROM user");

foreach ($users as $user) {
    $uid = $GLOBALS['db']->get_var("SELECT id FROM user WHERE id = '" . $user->id . "'");
    if (!$uid) {
        continue;
    }

    $GLOBALS['db']->query("INSERT INTO user_board VALUES ('', '" . $user->id . "', '" . $user->board_id . "')");

    $GLOBALS['db']->query("UPDATE user_permission SET board_id = '" . $user->board_id . "' WHERE user_id = '" . $user->id . "'");

    $GLOBALS['db']->query("UPDATE user_notification SET board_id = '" . $user->board_id . "' WHERE user_id = '" . $user->id . "'");

    $same_users = $GLOBALS['db']->get_results("SELECT * FROM user WHERE email = '" . $user->email . "' AND id > '" . $user->id . "'");
    if ($GLOBALS['db']->num_rows > 0) {
        foreach ($same_users as $u) {
            $GLOBALS['db']->query("INSERT INTO user_board VALUES ('', '" . $user->id . "', '" . $u->board_id . "')");
            $GLOBALS['db']->query("UPDATE user_notification SET user_id = '" . $user->id . "', board_id = '" . $u->board_id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE user_group_link SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE user_permission SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE ticket_subscription SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE ticket_comment SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE ticket SET responsible = '" . $user->id . "' WHERE responsible = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE phase_subscription SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("DELETE FROM personal_setting WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE message_topic SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("UPDATE message SET user_id = '" . $user->id . "' WHERE user_id = '" . $u->id . "'");
            $GLOBALS['db']->query("DELETE FROM user WHERE id = '" . $u->id . "' LIMIT 1");
        }
    }
}

$GLOBALS['db']->query("DELETE FROM user_permission WHERE board_id = '0'");

$GLOBALS['db']->query("ALTER TABLE user ADD type ENUM('USER', 'SYSTEM_ADMIN') NOT NULL DEFAULT 'USER'");
$GLOBALS['db']->query("ALTER TABLE user DROP board_id, DROP active");
?>
