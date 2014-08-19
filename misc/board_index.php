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

header('Content-type: text/html; charset=utf-8');
require_once './lib/pagegen.class.php';
require_once './config/global.config.php';

if ($GLOBALS['maintenance_mode'] == true) {
    include_once './maintenance.php';
    exit;
}

$post_args = filter_input_array(INPUT_POST);
/* Installation */
if (isset($post_args['name'])) {
    if (isset($post_args['name']) && $post_args['name'] != "" &&
            isset($post_args['email']) &&
            isset($post_args['url']) &&
            isset($post_args['timezone'])) {

        require_once './config/database.config.php';
        require_once './3rdparty/ezSQL/shared/ez_sql_core.php';
        require_once './3rdparty/ezSQL/mysql/ez_sql_mysql.php';

        $GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

        $board_name = $GLOBALS['db']->escape($post_args['name']);
        $existing = $GLOBALS['db']->get_var("SELECT id FROM board WHERE name = '" . $board_name . "'");
        if ($existing) {
            $GLOBALS['install_error'] = "Board with same name exists!";
            return;
        }

        $fp = fopen('./config/board.config.php', 'w');
        if ($fp) {
            $config = '<?php
                        /* General board settings */
                        $GLOBALS[\'board_name\'] = "' . $post_args['name'] . '";
                        $GLOBALS[\'board_email\'] = "' . $post_args['email'] . '";
                        $GLOBALS[\'board_address\'] = "' . $post_args['url'] . '";
                        /* Timezone */
                        $GLOBALS[\'timezone\'] = "' . $post_args['timezone'] . '";
                        ';
            $config = preg_replace('/^\s*/m', '', $config);
            $config = preg_replace('/\s*$/m', '', $config);

            fwrite($fp, $config);
            fclose($fp);

            header('Location: .');
        } else {
            $GLOBALS['install_error'] = "Cannot write to config folder, please check settings!";
        }
    } else {
        $GLOBALS['install_error'] = "Fill in all forms!";
    }
}

if (file_exists('./config/board.config.php')) {
    include_once './config/board.config.php';
}

if (!isset($GLOBALS['board_name'])) {
    require_once './lib/views/page.board_install.php';
    return;
}

$page = new PageGen();
$page->printPage();
