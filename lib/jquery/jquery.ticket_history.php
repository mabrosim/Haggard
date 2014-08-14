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

$args = filter_input_array(INPUT_POST);
$start = $GLOBALS['db']->escape($args['start']);
$stop = $GLOBALS['db']->escape($args['stop']);

$start_time = date("y-m-d", strtotime($start)) . ' 00:00:00';
$stop_time = date("Y-m-d", strtotime($stop)) . ' 23:59:59';

echo '<table width="100%" class="stat_table" id="tickets">';
echo '<thead>';
echo '<tr><th id="title_header">Title</th><th id="component_header">Component</th><th id="from_header">From phase</th>';
echo '<th id="to_header">To phase</th><th id="date_header">Date</th></thead><tbody>';
$q = $GLOBALS['db']->get_results("SELECT ticket_stat.* FROM ticket_stat LEFT JOIN ticket ON ticket_stat.ticket_id = ticket.id WHERE ticket_stat.created >= '" . $start_time . "' AND ticket_stat.created <= '" . $stop_time . "' AND ticket.board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY ticket_stat.created DESC");
if ($q) {
    foreach ($q as $result) {
        $ticket = new Ticket($result->ticket_id);
        $component = $ticket->getComponent();
        $from_phase = new Phase($result->old_phase);
        $to_phase = new Phase($result->new_phase);

        echo '<tr>';

        echo '<td>' . $ticket->getTitle() . '</td><td>' . $component->getName() . '</td><td>' . $from_phase->getName() . '</td>';
        echo '<td>' . $to_phase->getName() . '</td><td>' . $result->created . '</td>';

        echo '</tr>';
    }
}
echo '</tbody></table>';
echo '</div>';

echo '</div>';
?>
