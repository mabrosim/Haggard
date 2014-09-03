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

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_users')) {
    return;
}

$args = filter_input_array(INPUT_POST);

if ($args['func'] == "new_user_ldap") {

    $mail = $GLOBALS['db']->escape($args['mail']);
    $name = "";
    $dn = "";
    $timezone = "Europe/Helsinki";
    $site = "FIOUL08";

    $conn = ldap_connect($GLOBALS['ldap_domain_controllers'][0]);
    if ($conn && ldap_bind($conn, $GLOBALS['ldap_admin'], $GLOBALS['ldap_password'])) {
        $res = ldap_search($conn, 'o=Nokia', '(mail=' . $mail . ')', array("*"));
        if ($res) {
            $info = ldap_get_entries($conn, $res);
            if ($info['count'] == 0) {
                echo "Could not find user with email " . $name;
                return;
            }

            $dn = $info[0]['dn'];
            $name = $info[0]['cn'][0];
            $site = $info[0]['nokiasite'][0];

            $country = substr($site, 0, 2);
            $timezone = getTimezoneByCountry($country);
        } else {
            echo "Could not find LDAP username with email " . $name;
        }
    }

    $uid = $GLOBALS['db']->get_var("SELECT id FROM user WHERE email = '" . $mail . "'");
    if (!$uid) {
        $query = "INSERT INTO user (name, email, noe_account, type, nokiasite, timezone) VALUES ('" . $name . "', '" . $mail . "', '" . $dn . "', 'USER', '" . $site . "', '" . $timezone . "')";
        $GLOBALS['db']->query($query);

        $uid = $GLOBALS['db']->insert_id;
    }

    // Basic privileges for all users
    $user = new User($uid);
    $user->clearPermissions();
    $user->setPermission('move_ticket');
    $user->setPermission('create_ticket');
    $user->setPermission('edit_ticket');
    $user->setPermission('comment_ticket');

    $lid = $GLOBALS['db']->get_var("SELECT id FROM user_board WHERE user_id = '" . $uid . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    if (!$lid) {
        $GLOBALS['db']->query("INSERT INTO user_board (user_id, board_id) VALUES ('" . $uid . "', '" . $GLOBALS['board']->getBoardId() . "')");

        $log = $_SESSION['username'] . ' added new user: ' . $name . ' (' . $mail . ').';
        $GLOBALS['logger']->log($log);

        $GLOBALS['email']->setAddress($mail);
        $GLOBALS['email']->setSubject("Account created");
        $msg = $_SESSION['username'] . " has granted you access to Haggard!<br><br>";
        $msg .= 'To login please go to <a href="' . $GLOBALS['board']->getBoardURL() . '">' . $GLOBALS['board']->getBoardURL() . '</a><br><br>';
        $msg .= 'Username: ' . $name . '<br><br><br>';

        $GLOBALS['email']->setMessage($msg);
        $GLOBALS['email']->send();
        echo "true";
    } else {
        echo "User is already on this board";
    }

} else if ($args['func'] == "new_user") {
    $mail = $GLOBALS['db']->escape($args['email']);
    $name = $GLOBALS['db']->escape($args['name']);
    $pass = sha1($GLOBALS['db']->escape($args['pass']));

    $dn = "";
    $timezone = "Europe/Helsinki";
    $site = "OULU";

    $uid = $GLOBALS['db']->get_var("SELECT id FROM user WHERE email = '" . $mail . "'");
    if (!$uid) {
        $query = "INSERT INTO user (name, email, password, type, nokiasite, timezone) VALUES ('" . $name . "', '" . $mail . "', '" . $pass . "', 'USER', '" . $site . "', '" . $timezone . "')";
        $GLOBALS['db']->query($query);

        $uid = $GLOBALS['db']->insert_id;
    }

    // Basic privileges for all users
    $user = new User($uid);
    $user->clearPermissions();
    $user->setPermission('move_ticket');
    $user->setPermission('create_ticket');
    $user->setPermission('edit_ticket');
    $user->setPermission('comment_ticket');

    $lid = $GLOBALS['db']->get_var("SELECT id FROM user_board WHERE user_id = '" . $uid . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    if (!$lid) {
        $GLOBALS['db']->query("INSERT INTO user_board (user_id, board_id) VALUES ('" . $uid . "', '" . $GLOBALS['board']->getBoardId() . "')");

        $log = $_SESSION['username'] . ' added new user: ' . $name . ' (' . $mail . ').';
        $GLOBALS['logger']->log($log);

        $GLOBALS['email']->setAddress($mail);
        $GLOBALS['email']->setSubject("Account created");
        $msg = $_SESSION['username'] . " has granted you access to Haggard!<br><br>";
        $msg .= 'To login please go to <a href="' . $GLOBALS['board']->getBoardURL() . '">' . $GLOBALS['board']->getBoardURL() . '</a><br><br>';
        $msg .= 'Username: ' . $name . '<br><br><br>';

        $GLOBALS['email']->setMessage($msg);
        $GLOBALS['email']->send();
        echo "true";
    } else {
        echo "User is already on this board";
    }
} else if ($args['func'] == "remove_user") {
    $id = $GLOBALS['db']->escape($args['id']);
    $user = new User($id);
    $GLOBALS['db']->query("DELETE FROM user_board WHERE user_id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    $GLOBALS['db']->query("DELETE FROM ticket_subscription s INNER JOIN ticket t ON s.ticket_id = t.id WHERE s.user_id = '" . $id . "' AND t.board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    $GLOBALS['db']->query("DELETE FROM user_group_link user_id = '" . $id . "'");
    $GLOBALS['db']->query("UPDATE ticket SET responsible = '0' WHERE responsible = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    $GLOBALS['db']->query("DELETE FROM user_permission WHERE user_id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    $log = $_SESSION['username'] . ' removed user: ' . $user->getName() . '.';
    $GLOBALS['logger']->log($log);
} else if ($args['func'] == "edit_user") {
    $name = $GLOBALS['db']->escape($args['name']);
    $email = $GLOBALS['db']->escape($args['email']);
    $id = $GLOBALS['db']->escape($args['id']);
    $pass = sha1($GLOBALS['db']->escape($args['pass']));

    $query = "UPDATE user SET name = '" . $name . "', email='" . $email . "', password='" . $pass . "' WHERE id = '" . $id . "'";
    $GLOBALS['db']->query($query);

    $log = $_SESSION['username'] . ' edited user: ' . $name . '(' . $email . ').';
    $GLOBALS['logger']->log($log);
} else if ($args['func'] == "add_to_group") {
    $id = $GLOBALS['db']->escape($args['id']);
    $group = $GLOBALS['db']->escape($args['group']);

    $GLOBALS['db']->query("DELETE FROM user_group_link WHERE user_id = '" . $id . "' LIMIT 1");
    $GLOBALS['db']->query("INSERT INTO user_group_link (user_id, group_id) VALUES ('" . $id . "', '" . $group . "')");
} else if ($args['func'] == "user_permissions") {
    $permission_str = $GLOBALS['db']->escape($args['permissions']);
    $id = $GLOBALS['db']->escape($args['id']);
    $user = new User($id);

    $user->clearPermissions();

    $permissions = explode(",", $permission_str);
    foreach ($permissions as $permission) {
        $user->setPermission($permission);
    }
}
?>
