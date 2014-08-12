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
require_once '/var/www/haggard/lib/util.php';

/* Connect to database */
$GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

$users = $GLOBALS['db']->get_results("SELECT * FROM user");

$conn = ldap_connect($GLOBALS['ldap_domain_controllers'][0]);
if ($conn) {
    if (!ldap_bind($conn, $GLOBALS['ldap_admin'], $GLOBALS['ldap_password'])) {
        $conn = null;
    }
}
foreach ($users as $user) {
    if ($conn) {
        $res = ldap_search($conn, 'o=Nokia', '(mail=' . $user->email . ')', array("*"));
        if ($res) {
            $info = ldap_get_entries($conn, $res);
            if ($info['count'] == 0) {
                echo "Could not find user with email " . $user->name . "\n";
                continue;
            }

            $dn = $info[0]['dn'];
            $name = $info[0]['cn'][0];
            $site = $info[0]['nokiasite'][0];

            $country = substr($site, 0, 2);
            $timezone = getTimezoneByCountry($country);
            echo $user->name . " -> " . $country . " -> " . $timezone . "\n";
            $GLOBALS['db']->query("UPDATE user SET timezone = '" . $timezone . "' WHERE id = '" . $user->id . "'");
        } else {
            echo "Could not find LDAP username with email " . $user->email;
        }
    }
}

// FROM TIMESTAMP TO DATETIME
$GLOBALS['db']->query("ALTER TABLE board CHANGE created created DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE cycle CHANGE start start DATETIME NULL DEFAULT NULL, CHANGE stop stop DATETIME NULL DEFAULT NULL");
$GLOBALS['db']->query("ALTER TABLE cycle_stat CHANGE created created DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE log CHANGE date date DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE message CHANGE time time DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE message_topic CHANGE created created DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE ticket CHANGE last_change last_change DATETIME NULL DEFAULT NULL, CHANGE created created DATETIME NULL DEFAULT NULL");
$GLOBALS['db']->query("ALTER TABLE ticket_comment CHANGE created created DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE ticket_history CHANGE created created DATETIME NULL");
$GLOBALS['db']->query("ALTER TABLE ticket_stat CHANGE created created DATETIME NULL");

// FROM UTC+2 -> UTC
$GLOBALS['db']->query("UPDATE board SET created = DATE_SUB(created, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE cycle SET start = DATE_SUB(start, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE cycle SET stop = DATE_SUB(stop, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE cycle_stat SET created = DATE_SUB(created, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE log SET date = DATE_SUB(date, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE message SET time = UTC_TIMESTAMP()");
$GLOBALS['db']->query("UPDATE message_topic SET time = UTC_TIMESTAMP()");
$GLOBALS['db']->query("UPDATE ticket SET last_change = DATE_SUB(last_change, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE ticket SET created = DATE_SUB(created, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE ticket_comment SET created = DATE_SUB(created, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE ticket_history SET created = DATE_SUB(created, INTERVAL 2 HOUR)");
$GLOBALS['db']->query("UPDATE ticket_stat SET created = DATE_SUB(created, INTERVAL 2 HOUR)");
?>
