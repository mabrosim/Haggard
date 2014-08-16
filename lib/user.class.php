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

class User {

    private $id = 0;
    private $name = "";
    private $email = "";
    private $group_id = -1;
    private $type = 'USER';
    private $board = NULL;
    private $timezone = "Europe/Helsinki";
    private $nokiasite = "FIOUL08";
    private $last_login = NULL;
    private $alias = NULL;
    private $permissions = array();
    private $default_color = "#102c65";
    private $default_hover_color = "#124191";

    public function __construct($id, $board = NULL) {
        if (!isset($board)) {
            if (isset($GLOBALS['board'])) {
                $this->board = $GLOBALS['board'];
            } else {
                error_log("Haggard v2.0 : Failed to create user, no board defined");
                return;
            }
        }

        if (!isset($id)) {
            return;
        }

        $this->id = $id;

        $usr = $GLOBALS['db']->get_row("SELECT type, name, email, noe_account, timezone, nokiasite, alias, last_login FROM user WHERE id = '" . $id . "'");

        if (!$usr) {
            return;
        }

        $this->name = $usr->name;
        $this->email = $usr->email;
        $this->type = $usr->type;
        $this->timezone = $usr->timezone;
        $this->nokiasite = $usr->nokiasite;
        $this->alias = $usr->alias;
        $this->last_login = $usr->last_login;

        $group_id = $GLOBALS['db']->get_var("SELECT group_id FROM user_group_link WHERE user_id = '" . $this->id . "' LIMIT 1");
        if ($group_id) {
            $this->group_id = $group_id;
        }
    }

    public function hasAccessToBoard() {
        if ($this->type == "SYSTEM_ADMIN") {
            return true;
        }

        if ($this->board->getSettingValue("PRIVATE_BOARD") == 1) {
            if (in_array($this->id, $this->board->getUserIds())) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    public function getUserBoards() {
        $boards = $GLOBALS['db']->get_results("SELECT * FROM board b LEFT JOIN user_board ub ON b.id = ub.board_id WHERE ub.user_id = '" . $this->id . "' ORDER BY b.name ASC");

        $ret = array();
        if ($boards) {
            foreach ($boards as $b) {
                $ret[] = new Board($b->name);
            }
        }

        return $ret;
    }

    public function getRealName() {
        return $this->name;
    }

    public function getName() {
        if ($this->alias != null && $this->alias != '') {
            return $this->alias;
        }

        if ($this->board->getSettingValue('USE_FIRSTNAME')) {
            $parts = explode(" ", $this->name);
            if (count($parts) >= 2) {
                return $parts[1];
            }
        }

        return $this->name;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($alias) {
        $this->alias = $alias;
        $GLOBALS['db']->query("UPDATE user SET alias = '" . $alias . "' WHERE id = '" . $this->id . "' LIMIT 1");
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTimezone() {
        return $this->timezone;
    }

    public function getLastLogin() {
        return $this->last_login;
    }

    public function getNokiaSite() {
        return $this->nokiasite;
    }

    public function getType() {
        return $this->type;
    }

    public function setTimezone($timezone) {
        if ($timezone == $this->timezone) {
            return;
        }

        $GLOBALS['db']->query("UPDATE user SET timezone = '" . $timezone . "' WHERE id = '" . $this->id . "' LIMIT 1");
    }

    public function setSetting($key, $value) {
        $usr_setting = $GLOBALS['db']->get_row("SELECT * FROM personal_setting WHERE user_id = '" . $this->id . "' AND setting = '" . $key . "' LIMIT 1");
        if ($GLOBALS['db']->num_rows > 0) {
            $GLOBALS['db']->query("UPDATE personal_setting SET value = '" . $value . "' WHERE user_id = '" . $this->id . "' AND setting = '" . $key . "' LIMIT 1");
        } else {
            $GLOBALS['db']->query("INSERT INTO personal_setting (user_id, setting, value) VALUES ('" . $this->id . "', '" . $key . "', '" . $value . "')");
        }
    }

    public function getSetting($key) {
        $usr_setting = $GLOBALS['db']->get_row("SELECT value FROM personal_setting WHERE user_id = '" . $this->id . "' AND setting = '" . $key . "' LIMIT 1");
        if ($usr_setting) {
            return $usr_setting->value;
        } else {
            $value = 1;
            if ($key == "color") {
                $value = $this->default_color;
            } else if ($key == "hover_color") {
                $value = $this->default_hover_color;
            } else if ($key == "show_ticket_created" || $key == "show_ticket_changed" || $key == "hide_extra_info") {
                $value = 0;
            }

            $GLOBALS['db']->escape($key);
            $GLOBALS['db']->query("INSERT INTO personal_setting (user_id, setting, value) VALUES ('" . $this->id . "', '" . $key . "', '" . $value . "')");

            return $value;
        }
    }

    public function getNumTickets() {
        $tickets = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE responsible = '" . $this->id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' AND active = '1' AND deleted = '0'");
        return $tickets;
    }

    public function getPermission($key, $board_id = NULL) {
        if ($this->type == "SYSTEM_ADMIN") {
            return true;
        }

        if ($board_id == NULL) {
            $board_id = $this->board->getBoardId();
        }

        if ($key == null || $key == '') {
            return false;
        }

        $retval = false;

        if (array_key_exists($key, $this->permissions)) {
            return true;
        }

        /* User permissions overwrite the group permissions */
        $key = $GLOBALS['db']->escape($key);
        $permission_id = $GLOBALS['db']->get_var("SELECT id FROM permission WHERE data = '" . $key . "' LIMIT 1");
        if (!$permission_id) {
            // No such permission given yet
            $GLOBALS['db']->query("INSERT INTO permission (data) VALUES ('" . $key . "')");
            $permission_id = $GLOBALS['db']->insert_id;
        }

        $all_permission_id = $GLOBALS['db']->get_var("SELECT id FROM permission WHERE data = 'all' LIMIT 1");
        $query = "SELECT id FROM user_permission WHERE user_id = '" . $this->id . "' AND (permission_id = '" . $permission_id . "'";

        if ($all_permission_id) {
            $query .= " OR permission_id = '" . $all_permission_id . "'";
        }

        $query .= ") AND board_id = '" . $board_id . "'";

        $sel = $GLOBALS['db']->get_var($query);
        if ($sel) {
            // User has this permission
            $this->permissions[$key] = true;
            return true;
        }

        if ($this->group_id > 0) {
            $query = "SELECT gp.* FROM group_permission gp LEFT JOIN user_group g ON g.id = gp.group_id WHERE gp.group_id = '" . $this->group_id . "' AND (gp.permission_id = '" . $permission_id . "'";

            if ($all_permission_id) {
                $query .= " OR gp.permission_id = '" . $all_permission_id . "'";
            }

            $query .= ") AND g.board_id = '" . $board_id . "'";

            $sel = $GLOBALS['db']->get_row($query);
            if ($sel) {
                // Group that the user is has this permission
                $this->permissions[$key] = true;
                $retval = true;
            }
        }

        return $retval;
    }

    public function setPermission($key) {
        $key = $GLOBALS['db']->escape($key);
        if ($key == '') {
            return;
        }

        $permission_id = $GLOBALS['db']->get_var("SELECT id FROM permission WHERE data = '" . $key . "' LIMIT 1");
        if (!$permission_id) {
            $GLOBALS['db']->query("INSERT INTO permission (data) VALUES ('" . $key . "')");
            $permission_id = $GLOBALS['db']->insert_id;
        }

        $GLOBALS['db']->query("INSERT INTO user_permission (board_id, user_id, permission_id) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $this->id . "', '" . $permission_id . "')");
    }

    public function clearPermissions() {
        $GLOBALS['db']->query("DELETE FROM user_permission WHERE user_id = '" . $this->id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    }

    public function getNumPosts() {
        return $GLOBALS['db']->get_var("SELECT COUNT(id) FROM message WHERE user_id = '" . $this->id . "'");
    }

}

?>
