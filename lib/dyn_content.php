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

session_start();

header('Content-type: text/html; charset=utf-8');

require_once '../config/database.config.php';
require_once '../config/board.config.php';
require_once '../config/global.config.php';
require_once '../3rdparty/ezSQL/shared/ez_sql_core.php';
require_once '../3rdparty/ezSQL/mysql/ez_sql_mysql.php';
require_once './board.class.php';
require_once './log.class.php';
require_once './email.class.php';
require_once './benchmark.class.php';
require_once './util.php';
if (BENCHMARK) {
    require_once '../tools/php-profiler/profiler.php';
}

$GLOBALS['benchmark'] = new Benchmark();
$GLOBALS['benchmark']->startBlock('dyn_content');
$GLOBALS['benchmark']->startBlock('initialization');

/* Connect to database */
$GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

$GLOBALS['board'] = new Board($GLOBALS['board_name']);
$GLOBALS['logger'] = new Log();
$GLOBALS['email'] = new Email();
$GLOBALS['mem'] = null;

/* if(class_exists('Memcached'))
  {
  $GLOBALS['mem'] = new Memcached();
  $GLOBALS['mem']->addServer('localhost', 11211);
  }
 */

$timezone = $GLOBALS['board']->getBoardTimezone();

if (isset($_SESSION['userid'])) {
    $GLOBALS['cur_user'] = new User($_SESSION['userid']);
    $usertz = $GLOBALS['cur_user']->getTimezone();
    if ($usertz != '' && $usertz != null) {
        $timezone = $usertz;
    }
}

date_default_timezone_set($timezone);
/* Dynmically load stuff */
$args = filter_input_array(INPUT_GET);

$GLOBALS['benchmark']->endBlock('initialization');

if (array_key_exists('jquery', $args) && file_exists('./jquery/' . $args['jquery'])) {
    ob_start();
    include('./jquery/' . $args['jquery']);
    echo ob_get_clean();
} else if (array_key_exists('page', $args) && file_exists('./views/' . $args['page'])) {
    if (!isset($GLOBALS['cur_user']) && $GLOBALS['board']->getSettingValue("PRIVATE_BOARD") == "1") {
        echo "<h2>Board is private</h2>";
        return;
    }

    ob_start();
    include('./views/' . $args['page']);
    echo ob_get_clean();
} else {
    /* Write PHP error log */
    $log = "Haggard - 404 error. Params: ";
    while ($param = current($args)) {
        $log .= key($args) . ': ' . $param . ' - ';
        next($args);
    }

    error_log($log);

    ob_start();
    include('./views/page.404.php');
    echo ob_get_clean();
}

$GLOBALS['benchmark']->endBlock('dyn_content');
$GLOBALS['benchmark']->render();
?>
