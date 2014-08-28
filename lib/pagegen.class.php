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

require_once './lib/board.class.php';

class PageGen {

    private $board = NULL;
    private $current_page = "page.board.php";
    private $login_error = "";

    public function __construct() {
        session_start();

        /* Log out from other boards */
        require_once './config/board.config.php';
        require_once './config/global.config.php';
        require_once './lib/board.class.php';
        require_once './lib/user.class.php';
        require_once './lib/log.class.php';
        require_once './lib/util.php';
        require_once './lib/benchmark.class.php';
        require_once './config/database.config.php';
        require_once './3rdparty/ezSQL/shared/ez_sql_core.php';
        require_once './3rdparty/ezSQL/mysql/ez_sql_mysql.php';

        if (BENCHMARK) {
            require_once './tools/php-profiler/profiler.php';
        }

        $GLOBALS['benchmark'] = new Benchmark();
        /* Connect to database */
        $GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

        $this->board = new Board($GLOBALS['board_name']);

        $c_board = filter_input(INPUT_COOKIE, 'board', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!isset($c_board) || !$c_board || $c_board == '') {
            setcookie('board', $GLOBALS['board_name'], time() + 86400);
        } else if ($c_board != $GLOBALS['board_name']) {
            setcookie('board', $GLOBALS['board_name'], time() + 86400);
            setcookie('p_id', 'all', time() + 86400);
            setcookie('c_id', 'all', time() + 86400);
        }

        $GLOBALS['board'] = $this->board;
        $GLOBALS['logger'] = new Log();

        if (!isset($_SESSION['board_name']) || $GLOBALS['board']->getBoardName() != $_SESSION['board_name']) {
            setcookie("current_page", "", time() - 3600);
        }

        $GLOBALS['mem'] = NULL;
        $func = filter_input(INPUT_POST, 'func', FILTER_SANITIZE_SPECIAL_CHARS);
        if (isset($func) && $func == 'login') {
            $this->handleLogin();
        }

        $timezone = $GLOBALS['board']->getBoardTimezone();
        if (isset($_SESSION['userid'])) {
            $GLOBALS['cur_user'] = new User($_SESSION['userid']);
            $usertz = $GLOBALS['cur_user']->getTimezone();
            if ($usertz != '' && $usertz != null) {
                $timezone = $usertz;
            }
        }

        date_default_timezone_set($timezone);

        if (isset($_SESSION['userid'])) {
            if ($_SESSION['userid'] == 1 && $GLOBALS['board']->getBoardName() != $_SESSION['board_name']) {
                session_unset();
                session_destroy();
                $_SESSION = array();
                return;
            }

            $lid = $GLOBALS['db']->get_var("SELECT id FROM user WHERE id = '" . $_SESSION['userid'] . "'");
            if (!$lid) {
                session_unset();
                session_destroy();
                $_SESSION = array();
            }
        } else {
            session_unset();
            session_destroy();
            $_SESSION = array();
        }
    }

    private function handleLogin() {
        $name = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!isset($name) || isset($_SESSION['username'])) {
            return;
        }

        $password = "";
        if (is_string($_POST['password'])) {
            $password = $_POST['password'];
        }

        if ($password == "") {
            error_log("HAGGARD ERROR [2.0]: Password was not good for login " . $name);
            return;
        }

        if (strtolower($name) == "guest") {
            if ($password == $GLOBALS['board']->getSettingValue("GUEST_PASSWORD") &&
                    $GLOBALS['board']->getSettingValue("GUEST_PASSWORD") != "") {
                $_SESSION['logged_user'] = new User(1);
                $_SESSION['username'] = "Guest";
                $_SESSION['userid'] = 1;
                $_SESSION['board_name'] = $GLOBALS['board']->getBoardName();
                $GLOBALS['logger']->login($_SESSION['logged_user']);
                return;
            }
        }

        if ($GLOBALS['use_ldap']) {

            $conn = ldap_connect($GLOBALS['ldap_domain_controllers'][0]);
            if (!$conn || !ldap_bind($conn, $GLOBALS['ldap_admin'], $GLOBALS['ldap_password'])) {
                $this->login_error = "Could not connect to LDAP";
                error_log("HAGGARD ERROR [2.0] Problem connecting to LDAP server");
                return;
            }

            $res = ldap_search($conn, 'o=Nokia', '(uid=' . $name . ')', array("*"));
            $info = ldap_get_entries($conn, $res);
            if ($info['count'] == 0) {
                $this->login_error = "Could not find user " . $name;
                error_log("HAGGARD ERROR [2.0]: Cannot find LDAP user " . $name);
                return;
            }

            $dn = $info[0]['dn'];
            $realname = $info[0]['cn'][0];
            $email = $info[0]['mail'][0];
            $site = $info[0]['nokiasite'][0];

            $country = substr($site, 0, 2);
            $timezone = getTimezoneByCountry($country);

            if (!$dn) {
                $this->login_error = "Could not find user " . $name;
                error_log("HAGGARD ERROR: Could not find user " . $name);
                return;
            }

            if (ldap_bind($conn, $dn, $password)) {
                $name = $GLOBALS['db']->escape($name);

                $uid = $GLOBALS['db']->get_var("SELECT id FROM user WHERE email = '" . $email . "' OR email = '" . $name . "@nokia.com' OR noe_account = '" . $name . "'");
                if (!$uid) {
                    $GLOBALS['db']->query("INSERT INTO user (name, email, noe_account, nokiasite, timezone, last_login) VALUES ('" . $realname . "', '" . $email . "', '" . $name . "', '" . $site . "', '" . $timezone . "', UTC_TIMESTAMP())");
                    $uid = $GLOBALS['db']->insert_id;
                } else {
                    $GLOBALS['db']->query("UPDATE user SET name = '" . $realname . "', noe_account = '" . $name . "', email = '" . $email . "', nokiasite = '" . $site . "', last_login = UTC_TIMESTAMP() WHERE id = '" . $uid . "'");
                }

                $_SESSION['logged_user'] = new User($uid);
                $_SESSION['username'] = $realname;
                $_SESSION['userid'] = $uid;
                $_SESSION['board_name'] = $GLOBALS['board']->getBoardName();
                $GLOBALS['logger']->login($_SESSION['logged_user']);

                header("Location: .");
                return;
            } else {
                $this->login_error = "Wrong password";
                error_log("HAGGARD ERROR: User authentication failed for user " . $name);
            }
        }
        else {

            $realname = '';
            $email = '';

            $name = $GLOBALS['db']->escape($name);
            $password = $GLOBALS['db']->escape($password);
            $password .= $GLOBALS['password_salt'];
            $password = sha1($password);
            // Security can be enhanced even furhter with this line
            //$password = substr($password, 25) . substr($password, 0, 25);
            $uid = $GLOBALS['db']->get_var("SELECT id FROM user WHERE name='$name' AND password='$password'");

            if ($uid) {
                $GLOBALS['db']->query("UPDATE user SET last_login=UTC_TIMESTAMP() WHERE id='$uid'");

                $_SESSION['logged_user'] = new User($uid);
                $_SESSION['username'] = $realname;
                $_SESSION['userid'] = $uid;
                $_SESSION['board_name'] = $GLOBALS['board']->getBoardName();
                $GLOBALS['logger']->login($_SESSION['logged_user']);
            }
            else {
                $this->login_error = "Could not find user " . $name;
                error_log("HAGGARD ERROR: Could not find user " . $name);
                //$this->login_error = "Wrong password";
                //error_log("HAGGARD ERROR: User authentication failed for user " . $name);
                return;
            }

            header("Location: .");
        }
    }

    /* Used to get page content from jQuery navigation */

    public function getPageContent($page) {
        $this->current_page = $page;
        return $this->genContent() . $this->loadJS();
    }

    /* Used to print out the first page */

    public function printPage() {
        $this->genHeader();
        echo '</head>' . PHP_EOL;
        flush();
        echo '<body>' . PHP_EOL;
        $this->genContent();
        $this->genFooter();
        $this->loadJS();
        echo '</body>' . PHP_EOL;
        echo '</html>' . PHP_EOL;
    }

    /* Generate header for the main page */

    private function genHeader() {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;

        echo '<html xmlns="http://www.w3.org/1999/xhtml">' . PHP_EOL;

        echo '<head>' . PHP_EOL;

        echo '<title>' . $this->board->getBoardName() . '</title>' . PHP_EOL;
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>' . PHP_EOL;
        echo '<meta http-equiv="X-UA-Compatible" content="IE=9" />' . PHP_EOL;

        /* Favicon */
        echo '<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />';

        /* Common stylesheets for all pages */
        echo '<link rel="stylesheet" href=' . $GLOBALS['JQUERY_UI_CSS'] . ' />' . PHP_EOL;
        echo '<link rel="stylesheet" href=' . $GLOBALS['JQUERY_QTIP_CSS'] . ' />' . PHP_EOL;

        echo '<link rel="stylesheet" href="./css/main.css" type="text/css">' . PHP_EOL;
        //echo '<link rel="stylesheet" href="./3rdparty/jquery-farbtastic/farbtastic.min.css" type="text/css">' . PHP_EOL;
        echo '<link rel="stylesheet" href="./3rdparty/jquery.jqplot.css" type="text/css">' . PHP_EOL;
        //echo '<link rel="stylesheet" href="./3rdparty/jquery-tablesorter/jquery.tablesorter.min.css" type="text/css">' . PHP_EOL;
    }

    /* Generates content from views */

    private function genContent() {
        require_once './lib/views/menu.php';

        echo '<div id="login_dialog" style="display:none;">';
        echo '<div id="pop_up">';
        echo '<p>Log in with NOE/NEE credentials</p>';
        echo '<form id="login_form" action="" method="post">';
        echo '<table border="0" width="100%" cellspacing="3">';
        echo '<tr><td style="text-align:right;" width="30%">Username:</td><td style="text-align:left;"><input name="username" type="text" size="20" maxlength="60"></td></tr>';
        echo '<tr><td style="text-align:right;" width="30%">Password:</td><td style="text-align:left;"><input name="password" type="password" size="20" maxlength="40"></td></tr>';
        echo '</table>';

        echo '<input type="hidden" name="func" value="login">';
        echo '<p style="text-align: center;"><input type="image" id="loginImg" src="./img/login.png" value="Log in"></p>';
        echo '</form>';
        echo '</div>';
        echo '</div>';

        echo '<div id="dialog" title="Basic dialog"></div>';
        echo '<div id="comment_dialog" title="Basic dialog"></div>';
        if ($this->login_error != "") {
            echo '<div style="position: fixed; bottom: 0; left: 0; z-index: 2000; background-color: white; padding: 10px;">';
            echo '<p style="color:red">ERROR: ' . $this->login_error . '</p>';
            echo '</div>';
            $this->login_error = "";
        }

        echo '<div id="content" style="display:none;">';

        echo '</div>';
    }

    private function genFooter() {
        // require_once './lib/views/footer.php';
    }

    /* Loads appropriate JS for each page */

    private function loadJS() {
        /* Common javascripts for all pages */

        // Load external js libraries
        echo '<script src='.$GLOBALS['JQUERY_JS'].'></script>';
        echo '<script src='.$GLOBALS['JQUERY_UI_JS'].'></script>';
        echo '<script src='.$GLOBALS['JQUERY_QTIP_JS'].'></script>';
        echo '<script src='.$GLOBALS['JQUERY_MIGRATE_JS'].'></script>';

        $this->loadJSFile("./3rdparty/jquery.cookie.js");
        $this->loadJSFile("./3rdparty/jquery.dragsort-0.5.2.js");
        $this->loadJSFile("./3rdparty/jquery.livequery.js");
        $this->loadJSFile("./3rdparty/jquery.placeholder.js");
        $this->loadJSFile("./3rdparty/farbtastic.js");
        $this->loadJSFile("./3rdparty/jquery.tablesorter.js");

        $this->loadJSFile("./js/navigation.js");
        $this->loadJSFile("./js/login_handler.js");
        $this->loadJSFile("./js/ticket_handler.js");
        $this->loadJSFile("./js/search.js");
        $this->loadJSFile("./js/ticket_table.js");
        $this->loadJSFile("./js/ticket_sort.js");
        $this->loadJSFile("./js/phase_settings.js");
        $this->loadJSFile("./js/auto_update.js");
        $this->loadJSFile("./js/page_scroll.js");

        if (isset($_SESSION['username'])) {
            $user = new User($_SESSION['userid']);
            if ($user->getPermission('move_ticket')) {
                echo '<script async type="text/javascript" src="./js/ticket_move.js" ></script>';
            }
        }
    }

    private function loadJSFile($file) {
        if (file_exists($file)) {
            echo '<script src="' . $file . '" type="text/javascript" charset="UTF-8"></script>';
        } else {
            error_log("[ERROR] Could not find Javascript file " . $file);
        }
    }

}
