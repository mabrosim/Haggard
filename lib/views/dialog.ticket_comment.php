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

$func = filter_input(INPUT_GET, 'func');
if (isset($func)) {
    echo '<style type="text/css">';
    echo 'td { padding: 5px; }';
    echo 'tr.even td { background-color: #f7f7f7; font-size: 12px; }';
    echo 'tr.odd td { background-color: #f3f3f3; font-size: 12px; }';
    echo 'body { font-size: 13px; font-family: arial; }';
    echo '</style>';

    $get_id = filter_input(INPUT_GET, 'id');
    if (!isset($get_id)) {
        echo 'No comments';
        return;
    }

    $id = $GLOBALS['db']->escape($get_id);
    if (!is_numeric($id)) {
        echo "not so fast hackerz";
        return;
    }

    echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';

    $query = "SELECT * FROM ticket_comment WHERE ticket_id = '" . $id . "' ORDER BY id DESC";
    if ($func == "history") {
        $query = "SELECT * FROM ticket_history WHERE ticket_id = '" . $id . "' ORDER BY id DESC";
    }
    $comments = $GLOBALS['db']->get_results($query);
    $can_delete = false;

    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('comment_ticket')) {
        $can_delete = true;
    }

    $i = 0;
    $tr_class = "odd";

    if ($comments) {
        foreach ($comments as $comment) {
            $user = new User($comment->user_id);

            if ($comment->user_id != $_SESSION['userid']) {
                $can_delete = false;
            }
            $tr_class = (($i % 2) == 0) ? "even" : "odd";

            echo '<tr class="' . $tr_class . '" id="comment_container">';
            if ($can_delete == true && $func != "history") {
                echo '<td valign="top" width="15px"><a href="" class="delete_comment" data-id="' . $comment->id . '" style="width:15px; height:15px;';
                echo ' background-image: url(\'../img/icons/12.png\'); display: block;"></a></td>';
            }
            echo '<td width="20%" style="vertical-align: top;">' . date("d.m.Y H:i", strtotime($comment->created . ' UTC')) . '</td>';
            echo '<td width="20%" style="vertical-align: top;">' . $user->getName() . '</td>';
            $com = str_replace('[br]', '<br/>', $comment->data);
            echo '<td style="vertical-align: top;">' . $com . '</td></tr>';
            $i++;
        }
        echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>';
        echo '<script src="../3rdparty/jquery.livequery.js" type="text/javascript"></script>';
        echo '<script src="../js/ticket_handler.js" type="text/javascript"></script>';
    } else {
        if ($func == "history") {
            echo '<p>No history</p>';
        } else {
            echo '<p>No comments</p>';
        }
    }

    echo '</table>';
} else {
    $get_id = filter_input(INPUT_GET, 'ticket_id');

    if (!isset($get_id)) {
        return;
    }

    $ticket_id = mysql_real_escape_string($get_id);
    $ticket = new Ticket($ticket_id);

    echo '<div id="pop_up">';
    echo '<h2>Title: ' . $ticket->getTitle() . '</h2>';
    echo '<div id="comment_tabs">';
    echo '<ul>';
    echo '<li><a href="#comment_frame_holder">Comments</a></li>';
    echo '<li><a href="#history_frame_holder">History</a></li>';
    echo '</ul>';
    echo '<div id="comment_frame_holder" style="padding:0 !important; width: 100%; height: 30em;">';
    echo '<iframe id="comment_frame" style="border: 0; height: 100%; width: 100%;" src="./lib/dyn_content.php?page=dialog.ticket_comment.php&func=comments&id=' . $ticket_id . '" width="100%" scrolling="auto"></iframe>';
    echo '</div>';
    echo '<div id="history_frame_holder" style="padding: 0 !important; width: 100%; height: 30em;">';
    echo '<iframe id="history_frame" style="border: 0; height: 100%; width: 100%;" src="./lib/dyn_content.php?page=dialog.ticket_comment.php&func=history&id=' . $ticket_id . '" width="100%" scrolling="auto"></iframe>';
    echo '</div>';
    echo '</div>';
    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('comment_ticket', $ticket->getBoard()->getBoardId())) {
        echo '<form id="new_comment" style="margin-top: 5px; margin-bottom: 0em;"><table width="100%" border="0" cellspacing="2" cellpadding="2">';
        echo '<tr><td valign="top">';
        echo $_SESSION['username'] . ':</td><td><textarea name="comment" id="comment_line" rows="6" cols="75" style="width:100%;" placeholder="Comment text"></textarea></td><td valign="top">';
        echo '<input type="submit" class="form_button" value="Comment"></td></tr>';
        echo '</table></form>';
    }
    echo '</div>';
}
?>
