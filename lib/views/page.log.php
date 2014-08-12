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
$get_date = filter_input(INPUT_GET, 'date');
/* Get more log dynamically */
if (isset($func)) {
    if ($func == "getLastPosts") {
        $lastId = $func = filter_input(INPUT_GET, 'lastID');

        $query = "";
        if (isset($get_date) && $get_date !== "") {
            $date = date("Y-m-d H:i", strtotime($get_date));
            $next_d = date("Y-m-d H:i", strtotime($get_date) + 24 * 60 * 60);

            $query = "SELECT * FROM log WHERE date > '" . $date . "' AND date < '" . $next_d . "' AND id < '" . $lastId . "'";
        } else {
            $query = "SELECT * FROM log WHERE id < '" . $lastId . "'";
        }

        $query .= " AND board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY date DESC LIMIT 20";

        $result = $GLOBALS['db']->get_results($query);
        $old_day = "";
        $last_id = 0;

        echo '<div id="log_table">';
        echo '<table width="100%">';

        if ($result) {
            foreach ($result as $row) {
                $day = date("d.m.Y", strtotime($row->date . ' UTC'));

                if ($old_day !== $day) {
                    $old_day = $day;
                    echo '<tr><th width="200">' . $day . '</th><th></th></tr>';
                }

                echo '<tr><td>';
                echo '<p>' . $row->date . '</p>';
                echo '</td><td>';
                $data = str_replace('[br]', '<br/>', $row->data);
                echo '<p>' . $data . '</p>';
                echo '</td></tr>';
                $last_id = $row->id;
            }
        }

        echo '</table>';
        echo '<div class="wrdLatest" id=' . $last_id . '></div>';
        echo '</div>';

        return;
    }
}

$query = "";

if (isset($get_date) && $get_date !== "") {
    $date = date("Y-m-d H:i", strtotime($get_date));
    $next_d = date("Y-m-d H:i", strtotime($get_date) + 24 * 60 * 60);

    $query = "SELECT * FROM log WHERE date > '" . $date . "' AND date < '" . $next_d . "'";
    $query .= " AND board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY date DESC LIMIT 0,20";
} else {
    echo '<h1>Log</h1>';

    echo '<label for="date">Date</label>';
    echo '<input type="text" id="date" name="date"/>';
    echo '<br><br>';

    $query = "SELECT * FROM log";
    $query .= " WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY date DESC LIMIT 0,20";
}


$result = $GLOBALS['db']->get_results($query);

$old_day = "";
$last_id = 0;
echo '<div id="log_table">';
echo '<table width="100%">';

foreach ($result as $row) {
    $day = date("d.m.Y", strtotime($row->date . ' UTC'));

    if ($old_day !== $day) {
        $old_day = $day;
        echo '<tr><th width="200">' . $day . '</th><th></th></tr>';
    }

    echo '<tr><td>';
    echo '<p>' . $row->date . '</p>';
    echo '</td><td>';
    $data = str_replace('[br]', '<br/>', $row->data);
    echo '<p>' . $data . '</p>';
    echo '</td></tr>';
    $last_id = $row->id;
}

echo '</table>';

echo '<div class="wrdLatest" id=' . $last_id . '></div>';

echo '</div>';
?>

