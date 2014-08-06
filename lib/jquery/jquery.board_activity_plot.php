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

if (apc_exists('board_activity_plot' . $GLOBALS['board']->getBoardId())) {
    $ret = apc_fetch('board_activity_plot' . $GLOBALS['board']->getBoardId(), $status);
    if ($status) {
        echo $ret;
        return;
    }
}

$ret = array();
$arr = array();
$ret['labels'] = array();

array_push($ret['labels'], 'This board');

$res = $GLOBALS['db']->get_results("SELECT * FROM (SELECT * FROM board_activity_stat WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY date DESC LIMIT 20) tmp ORDER BY tmp.date ASC");

$new_data = false;
$board_array = array();

if ($GLOBALS['db']->num_rows > 0) {
    foreach ($res as $s) {
        if (isset($s->date) && isset($s->num)) {
            $tmp = array($s->date, (int) $s->num);
            array_push($board_array, $tmp);
            $new_data = true;
        }
    }

    if ($new_data) {
        $arr[] = $board_array;
    }
}

array_push($ret['labels'], "All boards average");

$avg_res = $GLOBALS['db']->get_results("SELECT * FROM (SELECT date, AVG(num) AS avg FROM board_activity_stat GROUP BY date ORDER BY date DESC LIMIT 20) tmp ORDER BY tmp.date ASC");

$avg_new_data = false;
$avg_arr = array();
if ($GLOBALS['db']->num_rows > 0) {
    foreach ($avg_res as $s) {
        if (isset($s->date) && isset($s->avg)) {
            $tmp = array($s->date, (float) $s->avg);
            array_push($avg_arr, $tmp);
            $avg_new_data = true;
        }
    }

    if ($avg_new_data) {
        $arr[] = $avg_arr;
    }
}

$ret['data'] = $arr;
$retval = json_encode($ret);

apc_store('board_activity_plot' . $GLOBALS['board']->getBoardId(), $retval);

echo $retval;
?>
