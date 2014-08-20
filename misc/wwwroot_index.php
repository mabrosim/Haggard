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
require_once './haggard/config/database.config.php';
require_once './haggard/3rdparty/ezSQL/shared/ez_sql_core.php';
require_once './haggard/3rdparty/ezSQL/mysql/ez_sql_mysql.php';

$GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

$dirs = array_filter(glob('*'), 'is_dir');
echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<title>Haggard - Digital boards</title>';
echo '<link rel="shortcut icon" type="image/x-icon" href="./haggard/favicon.ico" />';
echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>';
//echo '<script type="text/javascript" src="./jwplayer.js"></script>' . PHP_EOL;
?>

<script>
$(document).ready(function() {
    $('.stat').click(function(e)
    {
        e.preventDefault();
        $(this).closest('li').find('.stats').slideToggle();
    });

    $('#show_video').click(function(e)
    {
        e.preventDefault();
        $('#video_container').slideToggle();
    });

});
</script>

<style>
/*
@font-face
{
    font-family: YourFontFamilyName;
    src: url('./haggard/font/YourFont.ttf');
}
*/
body
{
    font-family: Arial;
    font-size:16px;
    padding:0px;
    margin: auto;
    color: black;
    background-color: rgb(18,65,145);
    background-image: url('bg2.png');
    background-repeat: no-repeat;
    background-position: center top;
}

a:visited, a, a:active
{
    color: black;
}

a:hover
{
    color: rgb(18, 65, 145);
}

h1
{
    font-size: 22px;
    color: rgb(18, 65, 100);
}

img { border: 0 }

#content
{
    background-color: rgb(255, 255, 255);
    padding: 20px;
    width: 800px;
    margin-top: 340px;
    margin-left: 88px;
    border: solid #ccc 1px;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow: 0 6px 6px #333;
    -moz-box-shadow: 0 6px 6px #333;
    box-shadow: 0 6px 6px #333;
    margin-bottom: 50px;
    margin-left: auto;
    margin-right: auto;
}

</style>
<?php
echo '</head>';
echo '<body>';

echo '<div id="content">';
echo '<h1 style="font-size: 36px">Welcome to Haggard!</h1>';
echo '<p>Haggard is digital board to follow your team\'s tasks and activities. To try out the board, please navigate to <a href="./demo">Demo Board</a> and log in with Guest!</p>';
echo '<p>More information can be found from Haggard wiki</a>. Also check out the demo video below <a href="./haggard.mpg">(Download video).</a></p>';
echo '<div id="video_container">';
echo '<div id="video" style="display:none";>';
echo '</div>';
echo '</div>';

$all_day_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE DATE(date) = CURDATE()");
$all_week_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE DATE(date) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND CURDATE()");
$all_month_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE DATE(date) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()");

echo '<a name="support"></a>';
echo '<h1>Made in Oulu with Passion!</h1>';
echo '<p>Haggard was developed as hobby project by Heikki Hellgren & Maxim Abrosimov.<br>';
echo ' your thanks will generate good karma for the tool creators wherever they are. </p>';

$boards = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM board");
$private_boards = 0;
echo '<h1>Currently active public boards ('.$boards.')</h1>';
echo '<ul>';
if(count($dirs) > 0)
{
    foreach($dirs as $dir)
    {
        if($dir == "haggard") continue;
        $board = array('name' => $dir,
                       'id' => 0);
        if(file_exists('./'.$dir.'/config/board.config.php'))
        {
            require_once './'.$dir.'/config/board.config.php';
            $board = $GLOBALS['db']->get_row("SELECT * FROM board WHERE name = '".$GLOBALS['board_name']."' LIMIT 1");

            $private = $GLOBALS['db']->get_var("SELECT value FROM board_setting WHERE board_id = '".$board->id."' AND data = 'PRIVATE_BOARD'");
            if($private != null && $private == "1")
            {
                $private_boards++;
                continue;
            }
        }
        else
        {
            continue;
        }
        echo '<li><p><a href="./'.$dir.'/">'.$board->name.'</a> <a class="stat" href="" style="display:inline;">[Statistics]</a>';
        echo '<div class="stats" style="display:none;">';
            echo '<ul>';
                echo '<li>Board created: ' . $board->created . '</li>';
                $tickets = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE board_id = '".$board->id."'");
                echo '<li>Tickets: '.$tickets.'</li>';
                $users = $GLOBALS['db']->get_var("SELECT COUNT(u.id) FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '".$board->id."'");
                echo '<li>Users: '.$users.'</li>';
                $day_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '".$board->id."' AND DATE(date) = UTC_DATE()");
                echo '<li>Activity today: '.$day_stat.' ('.round($day_stat/$all_day_stat*100,2).'%)</li>';
                $week_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '".$board->id."' AND DATE(date) BETWEEN DATE_SUB(UTC_DATE(), INTERVAL 1 WEEK) AND UTC_DATE()");
                echo '<li>Activity past week: '.$week_stat.' ('.round($week_stat/$all_week_stat*100,2).'%)</li>';
                $month_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '".$board->id."' AND DATE(date) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()");
                echo '<li>Activity past month: '.$month_stat.' ('.round($month_stat/$all_month_stat*100,2).'%)</li>';
        echo '</ul>';
        echo '</div>';
        echo '</li>';
        unset($GLOBALS['board_name']);
    }
}
echo '</ul>';

echo '<h1>Global statistics</h1>';
if($private_boards > 0)
{
    echo '<p>Private boards: ' . $private_boards . '</p>';
}

$users = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM user");
echo '<p>Users: ' . $users . ', by site:</p>';
$sites = $GLOBALS['db']->get_results("SELECT SUBSTR(nokiasite, 1, 5) as site, COUNT(id) as count FROM user GROUP BY SUBSTR(nokiasite, 1, 5) ORDER BY count DESC");
echo '<ul>';
foreach($sites as $site)
{
    if($site->site == '') $site->site = "Unknown";
    echo '<li>' . $site->site . ': ' . $site->count . '</li>';
}
echo '</ul>';
$all = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket");
$avg = $all / $users;
$archived = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE active = '0'");
$deleted = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE deleted = '1'");
$active = $all - ($archived + $deleted);
echo '<p>Tickets: ' . $all . ' (Active: '.$active.', archived: '.$archived.', deleted: '.$deleted.', avg / user: '.round($avg, 2).')</p>';
$comments = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket_comment");
$history = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket_history");
$avg = $comments / $all;
$h_avg = $history / $all;
echo '<p>Ticket comments: ' . $comments . ' (Avg / ticket: '.round($avg, 2).')</p>';
echo '<p>Ticket activity: ' . $history . ' (Avg / ticket: '.round($h_avg, 2).')</p>';
echo '<p>Activity today: '.$all_day_stat.' (Avg / board: '.round($all_day_stat/$boards,2).')</p>';
echo '<p>Activity past week: '.$all_week_stat.' (Avg / board: '.round($all_week_stat/$boards, 2).', per day: '.round($all_week_stat/7, 2).')</p>';
echo '<p>Activity past month: '.$all_month_stat.' (Avg / board: '.round($all_month_stat/$boards, 2).', per day: '.round($all_month_stat/30, 2).')</p>';

echo '</div>';
?>
<script type="text/javascript">
jwplayer('video').setup({
    file: './haggard.mp4',
    width: '800',
    height: '450',
});
</script>

<?php
echo '</body></html>';
?>
