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

require_once 'user.class.php';
require_once 'component.class.php';
require_once 'phase.class.php';
require_once 'cycle.class.php';

class Board {

    private $name = "";
    private $id = 0;
    private $url = "";
    private $created = 0;
    private $board_settings = array("DB_VERSION" => "1",
        "USE_WIP" => "0",
        "USE_CYCLES" => "1",
        "USE_LOGGING" => "1",
        "USE_LINKING" => "1",
        "USE_STATISTICS" => "1",
        "USE_PRIORITIES" => "1",
        "TICKET_COLOR1" => "#9e9cd3",
        "TICKET_COLOR2" => "#ffdc00",
        "TICKET_COLOR3" => "#ff9800",
        "TICKET_COLOR4" => "#7eec0e",
        "TICKET_TYPE1" => "Backlog",
        "TICKET_TYPE2" => "Development",
        "TICKET_TYPE3" => "Test",
        "TICKET_TYPE4" => "Study",
        "SHOW_TICKET_HELP" => "1",
        "AUTO_ARCHIVE" => "1",
        "AUTO_ARCHIVE_THRESHOLD" => "4",
        "AUTO_ARCHIVE_PHASES" => "",
        "PRIVATE_BOARD" => "0",
        "GUEST_PASSWORD" => "",
        "SEND_EMAIL" => "1",
        "BOARD_TEAM" => "",
        "BOARD_TEAM_EMAIL" => "");
    private $cached_settings = array();

    public function __construct($name) {
        $name = $GLOBALS['db']->escape($name);
        if ($name == "" || !isset($name)) {
            return;
        }

        $use_id = false;

        if (is_numeric($name)) {
            $use_id = true;
        }

        $query = "SELECT id, url, name, email, timezone, created FROM board WHERE ";
        if ($use_id == false) {
            $query .= "name = '" . $name . "'";
        } else {
            $query .= "id = '" . $name . "'";
        }
        $board = $GLOBALS['db']->get_row($query);

        if ($GLOBALS['db']->num_rows > 0) {
            $this->id = $board->id;
            $this->name = $board->name;
            $this->email = $board->email;
            $this->url = $board->url;
            if (substr($this->url, -1) != "/") {
                $this->url .= "/";
            }

            $this->timezone = $board->timezone;
            $this->created = $board->created;
            $this->checkBoardSettings();
            $this->checkBoardPhases();
        } else {
            $this->createNewBoard($name);
        }
    }

    public function getCreated() {
        return $this->created;
    }

    public function getSettingValue($key) {
        if (array_key_exists($key, $this->cached_settings)) {
            return $this->cached_settings[$key];
        }

        $query = "SELECT value FROM board_setting WHERE board_id = '" . $this->id . "' AND data = '" . $key . "'";
        $setting = $GLOBALS['db']->get_var($query);
        if ($setting && $setting != "") {
            $this->cached_settings[$key] = $setting;
            return $setting;
        }

        if (array_key_exists($key, $this->board_settings)) {
            return $this->board_settings[$key];
        }

        return false;
    }

    public function setSettingValue($key, $value) {
        $key = $GLOBALS['db']->escape($key);
        $value = $GLOBALS['db']->escape($value);

        $this->board_settings[$key] = $value;
        $this->cached_settings[$key] = $value;

        $setting = $GLOBALS['db']->get_var("SELECT board_id FROM board_setting WHERE board_id = '" . $this->id . "' AND data = '" . $key . "'");
        if (isset($setting)) {
            $GLOBALS['db']->query("UPDATE board_setting SET value = '" . $value . "' WHERE data = '" . $key . "' AND board_id = '" . $this->id . "' LIMIT 1");
        } else {
            $GLOBALS['db']->query("INSERT INTO board_setting VALUES ('" . $this->id . "', '" . $key . "', '" . $value . "')");
        }
    }

    public function getBoardId() {
        return $this->id;
    }

    public function getBoardName() {
        return $this->name;
    }

    public function getBoardURL() {
        return $this->url;
    }

    public function getBoardTimezone() {
        return $this->timezone;
    }

    public function getBoardEmail() {
        return $this->email;
    }

    public function getBoardCreatetime() {
        return $this->created;
    }

    public function getNumTickets($active = 1, $deleted = 0) {
        $query = "SELECT COUNT(id) FROM ticket WHERE board_id = '" . $this->id . "'";
        if ($active != 1) {
            $query .= " AND active = '0'";
        }

        if ($deleted != 0) {
            $query .= " AND deleted = '1'";
        }
        return $GLOBALS['db']->get_var($query);
    }

    public function getNumCycles() {
        $GLOBALS['db']->query("SELECT id FROM cycle WHERE board_id = '" . $this->id . "'");
        return $GLOBALS['db']->num_rows;
    }

    public function getNumUsers() {
        $GLOBALS['db']->query("SELECT u.id FROM user u LEFT JOIN user_board ub ON ub.user_id = u.id WHERE ub.board_id = '" . $this->id . "'");
        return $GLOBALS['db']->num_rows;
    }

    public function getNumComponents() {
        $GLOBALS['db']->query("SELECT id FROM component WHERE board_id = '" . $this->id . "'");
        return $GLOBALS['db']->num_rows;
    }

    public function getUsers($active = -1) {
        $users = array();
        $query = "SELECT u.id FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '" . $this->id . "'";
        $query .= " ORDER BY name ASC";

        $res = $GLOBALS['db']->get_results($query);
        if ($GLOBALS['db']->num_rows == 0) {
            return NULL;
        }

        foreach ($res as $user) {
            $users[] = new User($user->id);
        }

        return $users;
    }

    public function getUserIds($active = -1) {
        $users = array();
        $query = "SELECT u.id FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '" . $this->id . "'";
        $query .= " ORDER BY name ASC";

        $res = $GLOBALS['db']->get_results($query);
        if ($GLOBALS['db']->num_rows == 0) {
            return NULL;
        }

        foreach ($res as $user) {
            $users[] = $user->id;
        }

        return $users;
    }

    public function getComponents() {
        if (function_exists('apc_exists') && apc_exists('components' . $this->id)) {
            return apc_fetch('components' . $this->id);
        }

        $components = array();
        $res = $GLOBALS['db']->get_results("SELECT id FROM component WHERE board_id = '" . $this->id . "'");
        if ($res) {
            foreach ($res as $comp) {
                $components[] = new Component($comp->id);
            }

            if (function_exists('apc_store'))
                apc_store('components' . $this->id, $components);
        }
        return $components;
    }

    public function getPhases($active = -1) {
        $phases = array();
        $query = "SELECT id FROM phase WHERE board_id = '" . $this->id . "' ";

        if ($active == -1) {
            $query .= "AND active = '1' ";
        }
        $query .= "ORDER BY id ASC";
        $res = $GLOBALS['db']->get_results($query);
        if ($res) {
            foreach ($res as $phase) {
                $phases[] = new Phase($phase->id);
            }
        }

        return $phases;
    }

    public function getCycles($active = -1) {
        $cycles = array();
        $query = "SELECT id FROM cycle WHERE board_id = '" . $this->id . "'";
        if ($active != -1) {
            $query .= " AND active = '" . $active . "'";
        }

        $query .= " ORDER BY start ASC";
        $res = $GLOBALS['db']->get_results($query);
        if ($res) {
            foreach ($res as $cycle) {
                $cycles[] = new Cycle($cycle->id);
            }
        }

        return $cycles;
    }

    public function getTickets($archived = 0, $deleted = 0) {
        $tickets = array();
        $query = "SELECT id FROM ticket WHERE board_id = '" . $this->id . "' AND active = '" . !$archived . "' AND deleted = '" . $deleted . "' ORDER BY data";
        $res = $GLOBALS['db']->get_results($query);
        if ($res) {
            foreach ($res as $ticket) {
                $tickets[] = new Ticket($ticket->id);
            }
        }

        return $tickets;
    }

    public function getPageGen() {
        return $GLOBALS['db']->get_var("SELECT id FROM pagegen WHERE board_id = '" . $this->id . "' LIMIT 1");
    }

    public function updateClients() {
        $GLOBALS['db']->query("UPDATE pagegen SET id = id + 1 WHERE board_id = '" . $this->id . "' LIMIT 1");
        $pagegen = $GLOBALS['db']->get_var("SELECT id FROM pagegen WHERE board_id = '" . $this->id . "' LIMIT 1");
        if (function_exists('apc_store'))
            apc_store('pagegen' . $this->id, $pagegen);
    }

    public function getCurrentCycle() {
        $id = $GLOBALS['db']->get_var("SELECT id FROM cycle WHERE board_id = '" . $this->id . "' AND (start IS NULL OR start < UTC_TIMESTAMP()) AND stop > UTC_TIMESTAMP() LIMIT 1");
        if ($id) {
            return new Cycle($id);
        }

        $id = $GLOBALS['db']->get_var("SELECT id FROM cycle WHERE board_id = '" . $this->id . "' ORDER BY id DESC LIMIT 1");
        if ($id) {
            return new Cycle($id);
        }

        return null;
    }

    private function createNewBoard($name) {
        if (!isset($GLOBALS['board_name']) || !isset($GLOBALS['board_address']) ||
                !isset($GLOBALS['board_email']) || !isset($GLOBALS['timezone'])) {
            return;
        }

        /* Create new board */
        $GLOBALS['db']->query("INSERT INTO board (name, url, email, timezone, created) VALUES
                              ('" . $GLOBALS['board_name'] . "', '" . $GLOBALS['board_address'] . "',
                              '" . $GLOBALS['board_email'] . "', '" . $GLOBALS['timezone'] . "', UTC_TIMESTAMP())");

        $this->id = $GLOBALS['db']->insert_id;
        if ($this->id > 0) {
            $this->name = $GLOBALS['board_name'];
            $this->url = $GLOBALS['board_address'];
            $this->email = $GLOBALS['board_email'];
            $this->timezone = $GLOBALS['timezone'];

            $this->checkBoardSettings();
            $this->checkBoardPhases();
        } else {
            error_log("Could not create new board with name " . $GLOBALS['board_name']);
        }
    }

    private function checkBoardSettings() {
        $board_settings = $GLOBALS['db']->get_results("SELECT * FROM board_setting WHERE board_id = '" . $this->id . "'");

        if ($GLOBALS['db']->num_rows == 0) {
            while ($value = current($this->board_settings)) {
                $GLOBALS['db']->query("INSERT INTO board_setting (board_id, data, value) VALUES
                    ('" . $this->id . "', '" . key($this->board_settings) . "', '" . $value . "')");
                next($this->board_settings);
            }
        } else {
            /* Save the board settings to internal data array */
            foreach ($board_settings as $setting) {
                $this->board_settings[$setting->data] = $setting->value;
            }
        }
    }

    private function checkBoardPhases() {
        $phases = $GLOBALS['db']->get_results("SELECT * FROM phase WHERE board_id = '" . $this->id . "'");
        if ($GLOBALS['db']->num_rows == 0) {
            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Backlog', 'phase0', 'This is the backlog. All the new tickets will be added here.
			 Backlog is same for every cycle and tickets from backlog can be added to the cycle by dragging them to TO-DO list', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'TO-DO', 'phase1', 'This is the TODO list of the currently selected cycle
				 All tickets on the TO-DO list has been selected for the cycle and will be counted as part of the WIP points', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Blocked', 'phase2', 'This is the blocked list. It will contain all tickets that cannot be started for some reason for example waiting for confirmation from 3rd party or waiting for HW', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Dev on-going', 'phase3', 'This is the list where is all tickets that are currently under development
					When you are done with the ticket which is under development and you do not need to test it you can move it straight away to the Done list', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Dev done', 'phase4', 'This is the list where all done tickets are moved from development on-going
				 You do not need to use this list unless you have to move the testing to some other person or you need to wait for the testing to start
				 Consider this list as TO-DO list for the testers', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Test on-going', 'phase5', 'This is the list for all the tickets that are currently under testing
				 You do not need to use this list if you do not need testing for the ticket', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Test done', 'phase6', 'Testing done, waiting for confirmation to be totally done', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Done', 'phase7', 'This is list where all tickets that are completely done (development + testing) can be found.
				 Putting ticket on this list will automatically launch e-mail for team''s SM and PO', '1');";
            $GLOBALS['db']->query($query);

            $query = "INSERT INTO phase (id, board_id, name, css, help, active) VALUES ('', '" . $this->id . "', 'Released', 'phase8', 'This is list where all released stuff can be found. If you have for example PCP errors that you have routed away put them here. Also study items that are done should be moved on this list', '1');";
            $GLOBALS['db']->query($query);
        }
    }

}

?>
