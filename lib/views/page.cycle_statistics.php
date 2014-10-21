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

echo '<link rel="stylesheet" type="text/css" href="./3rdparty/jqplot/jquery.jqplot.min.css" />';
echo '<script type="text/javascript" src="./3rdparty/jquery.tablesorter.js" charset="UTF-8"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/jquery.jqplot.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.json2.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.ciParser.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.barRenderer.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.highlighter.min.js"></script>';
echo '<script type="text/javascript" src="./3rdparty/jqplot/plugins/jqplot.cursor.min.js"></script>';
echo '<script type="text/javascript" src="./js/statistics.js"></script>';
require_once './ticket.class.php';
require_once './phase.class.php';
require_once './user.class.php';
require_once './reference.class.php';
require_once './component.class.php';

function getAverageHangTime($phase, $cycle)
{
    $hang_time = 0;
    $items = 0;
    $total = 0;

    $query = "SELECT * FROM ticket_stat WHERE new_phase = '" . $phase . "'";

    if ($cycle != 0) {
        $query .= " AND cycle_id = '" . $cycle . "'";
    }

    $res = $GLOBALS['db']->get_results($query);

    if ($res) {
        foreach ($res as $row) {
            $diff = 0;
            $moved = $GLOBALS['db']->get_row("SELECT * FROM ticket_stat WHERE old_phase = '" . $phase . "' AND ticket_id = '" . $row->ticket_id . "'");
            if ($GLOBALS['db']->num_rows > 0) {
                $diff = strtotime($row->created) - strtotime($moved->created);
            } else {
                $diff = time() - strtotime($row->created);
            }

            $hang_time += $diff;
            $items++;
        }
    }

    if ($items > 0) {
        $total = ($hang_time / $items);
    }

    $minutes = round($total / 60);
    $d = floor($minutes / 1440);
    $h = floor(($minutes - $d * 1440) / 60);
    $m = $minutes - ($d * 1440) - ($h * 60);

    if ($total > 0) {
        return $d . " days, " . $h . " hours, " . $m . " minutes";
    } else {
        return "";
    }
}

function getDeletedWIP($cycle)
{
    $total_wip = $GLOBALS['db']->get_var("SELECT SUM(wip) FROM ticket WHERE cycle = '" . $cycle . "' AND deleted = '1' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    return $total_wip;
}

function getArchivedWIP($cycle)
{
    $total_wip = $GLOBALS['db']->get_var("SELECT SUM(wip) FROM ticket WHERE cycle = '" . $cycle . "' AND active = '0' AND deleted != '1' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    return $total_wip;
}

function getAverageWIP($cycle)
{
    $query = "SELECT SUM(wip) FROM ticket";
    $total_wip = 0;
    $num = 0;

    if ($cycle != 0) {
        $query .= " WHERE cycle = '" . $cycle . "'";
    }

    $total_wip = $GLOBALS['db']->get_var($query);
    $num = $GLOBALS['db']->num_rows;

    return $total_wip / $num;
}

function printCycleTickets($cycle, $phase)
{
    $query = "SELECT * FROM ticket WHERE cycle = '" . $cycle . "' AND phase = '" . $phase . "'";
    $res = $GLOBALS['db']->get_results($query);
    if ($GLOBALS['db']->num_rows > 0) {
        $phase = new Phase($phase);

        echo '<tr><td colspan="6" style="background-color:#aac7d9;">Tickets in ' . $phase->getName() . '</td></tr>';

        foreach ($res as $row) {
            echo '<tr>';
            echo '<td>' . $row->data . '</td>';

            $user = new User($row->responsible);

            echo '<td>' . $user->getName() . '</td>';

            $comp = new Component($row->component);

            echo '<td>' . $comp->getName() . '</td>';

            if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
                echo '<td>' . $row->wip . '</td>';
            }

            $reference = new Reference($row->reference_id);

            echo '<td>';
            echo '<a target="_blank" href="' . $reference->getURL() . '">' . $reference->getRef() . '</a>';
            echo '</td>';

            $GLOBALS['db']->get_results("SELECT id FROM ticket_comment WHERE ticket_id = '" . $row->id . "'");
            $num_comments = $GLOBALS['db']->num_rows;

            echo '<td>';

            if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('edit_ticket')) {
                echo '<a class="edit_ticket" data-id="' . $row->id . '" href=""></a>';
            }

            echo '<a class="comments_ticket" data-id="' . $row->id . '" href=""></a><span style="font-size:10px;">(' . $num_comments . ')</span>';
            echo '</td>';
            echo '</tr>';
        }
    }
}

class Tab
{

    public $id;
    public $label;
    public $contents;

    public function __construct($id, $label)
    {
        $this->id = $id;
        $this->label = $label;
        $this->contents = ob_get_contents();
        ob_clean();
    }

}

$tabs = array();
$id = 0;

echo '<h1>General statistics</h1>';

ob_start();

$get_id = filter_input(INPUT_GET, 'id');
if (isset($get_id)) {
    $id = mysql_real_escape_string($get_id);
}

if ($id != 0) {
    $cycle = new Cycle($id);

    echo '<h1>' . $cycle->getName() . '</h1>';
    echo '<h3>Time: ' . date("d.m.Y", strtotime($cycle->getStart() . ' UTC')) . ' - ' . date("d.m.Y", strtotime($cycle->getStop() . ' UTC')) . '</h3>';
    if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
        echo '<h3>WIP points for this cycle: ' . $cycle->getWIPLimit() . '</h3>';
    }

    echo '<h3>Tickets in this cycle</h3>';
    echo '<table class="stat_table">';
    echo '<thead>';
    echo '<tr><th>Title</th><th>Responsible</th><th>Component</th>';
    if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
        echo '<th>WIP</th>';
    }
    echo '<th>Reference</th><th>Actions</th></tr></thead><tbody>';

    for ($i = 0; $i <= 8; $i++) {
        printCycleTickets($id, $i);
    }

    echo '</tbody></table>';

    $tabs[] = new Tab('cycle-info', 'Cycle: ' . htmlspecialchars($cycle->getName()));
}

if ($id == 0 || !isset($id)) {
    echo '<div id="board_stat_holder">';
    echo '<table class="stat_table" style="float:left;">';
    echo '<tr><th>Stat</th><th>Value</th></tr>';
    echo '<tr><td>Board created</td><td>' . $GLOBALS['board']->getCreated() . '</td></tr>';
    echo '<tr><td>Amount of tickets</td><td>' . $GLOBALS['board']->getNumTickets() . '</td></tr>';
    echo '<tr><td>Amount of archived tickets</td><td>' . $GLOBALS['board']->getNumTickets(0) . '</td></tr>';
    echo '<tr><td>Amount of deleted tickets</td><td>' . $GLOBALS['board']->getNumTickets(1, 1) . '</td></tr>';
    echo '<tr><td>Amount of users</td><td>' . $GLOBALS['board']->getNumUsers() . '</td></tr>';
    $day_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' AND DATE(date) = UTC_DATE()");
    $week_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' AND DATE(date) BETWEEN DATE_SUB(UTC_DATE(), INTERVAL 1 WEEK) AND UTC_DATE()");
    $month_stat = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM log WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' AND DATE(date) BETWEEN DATE_SUB(UTC_DATE(), INTERVAL 1 MONTH) AND UTC_DATE()");

    echo '<tr><td>Activity today</td><td>' . $day_stat . '</td></tr>';
    echo '<tr><td>Activity past week</td><td>' . $week_stat . '</td></tr>';
    echo '<tr><td>Activity past month</td><td>' . $month_stat . '</td></tr>';

    $message_topics = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM message_topic WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    echo '<tr><td>Message board topics</td><td>' . $message_topics . '</td></tr>';

    $messages = $GLOBALS['db']->get_var("SELECT COUNT(m.id) FROM message_topic t LEFT JOIN message m ON t.id = m.topic_id WHERE t.board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    echo '<tr><td>Message board messages</td><td>' . $messages . '</td></tr>';

    echo '</table>';
    echo '</div>';

    echo '<div class="stat_table" style="float:left; display: block; margin-left: 20px; width: 1050px; height: 630px; padding: 10px;">';
    echo '<div id="board_activity_plot" style="float:left; display: block; margin-left: 20px; width: 1000px; height: 600px; padding: 10px;">';
    echo '</div>';
    echo '<p style="text-align: right; margin-top: 20px; font-size: 14px;">Double click to reset zooming</p>';
    echo '</div>';

    echo '<div style="clear:both;"></div>';

    $tabs[] = new Tab('board', 'Board statistics');
}

if ($GLOBALS['board']->getSettingValue("USE_CYCLES") == "0") {
    echo '<div id="phase_stat_holder">';

    echo '<table class="stat_table" style="float:left;">';
    echo '<tr><th>Phase</th><th>Amount of tickets</th></tr>';
    $phases = $GLOBALS['board']->getPhases();

    foreach ($phases as $phase) {
        echo '<tr><td>' . $phase->getName() . '</td><td>' . $phase->getNumTickets() . '</td></tr>';
    }
    echo '</table>';
    echo '</div>';

    echo '<div class="stat_table" style="float:left; display: block; margin-left: 20px; width: 1050px; height: 630px; padding: 10px;">';
    echo '<div id="phase_history_plot" style="float:left; display: block; margin-left: 20px; width: 1000px; height: 600px; padding: 10px;">';
    echo '</div>';
    echo '<p style="text-align: right; margin-top: 20px; font-size: 14px;">Double click to reset zooming</p>';
    echo '</div>';

    echo '<div style="clear:both;"></div>';
    $tabs[] = new Tab('phase', 'Phase statistics');

    echo '<div id="user_stat_holder">';
    echo '<table class="stat_table" style="float:left;">';
    echo '<tr><th>User</th><th>Amount of tickets</th></tr>';

    $users = $GLOBALS['board']->getUsers();
    foreach ($users as $user) {
        echo '<tr><td>' . $user->getName() . '</td><td>' . $user->getNumTickets() . '</td></tr>';
    }
    echo '</table>';
    echo '</div>';

    echo '<div class="stat_table" style="float:left; display: block; margin-left: 20px; width: 1050px; height: 630px; padding: 10px;">';
    echo '<div id="user_history_plot" style="float:left; display: block; margin-left: 20px; width: 1000px; height: 600px; padding: 10px;">';
    echo '</div>';
    echo '<p style="text-align: right; margin-top: 20px; font-size: 14px;">Double click to reset zooming</p>';
    echo '</div>';

    echo '<div style="clear:both;"></div>';

    $tabs[] = new Tab('users', 'User statistics');
}

if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
    echo '<table class="stat_table" style="float:left;">';
    echo '<tr><th>Stat</th><th>Value</th></tr>';
    echo '<tr><td>Average WIP of ticket</td><td>' . round(getAverageWIP($id)) . '</td></tr>';
    echo '<tr><td>Archived tickets WIP</td><td>' . getArchivedWIP($id) . '</td></tr>';
    echo '<tr><td>Deleted tickets WIP</td><td>' . getDeletedWIP($id) . '</td></tr>';
    echo '</table><div style="clear:both;"></div>';

    $tabs[] = new Tab('wip', 'WIP');
}

if ($GLOBALS['board']->getSettingValue("USE_CYCLES") == "0") {
    $q = $GLOBALS['db']->get_results("SELECT * FROM ticket WHERE active = '1' AND deleted = '0' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    if ($GLOBALS['db']->num_rows > 0) {
        echo '<div id="current_ticket_holder">';
        echo '<div style="margin-bottom: 10px;">';
        echo '<a href="" class="export_current" target="_blank">Export to excel</a></div>';
        echo '<table width="100%" class="stat_table" id="current_tickets">';
        echo '<thead>';
        echo '<tr><th>Title</th><th>Component</th><th>Responsible</th><th>Phase</th><th>Reference</th>';
        $use_linking = false;
        if ($GLOBALS['board']->getSettingValue("USE_LINKING")) {
            echo '<th>Parent</th>';
            $use_linking = true;
        }
        echo '<th>Last update</th><th>Actions</th>';
        echo '</thead><tbody>';
        foreach ($q as $ticket_id) {
            $ticket = new Ticket($ticket_id->id);
            $component = $ticket->getComponent();
            $phase = $ticket->getPhase();
            $user = $ticket->getResponsible();
            $references = $ticket->getReferences();

            echo '<tr>';
            echo '<td>' . $ticket->getTitle() . '</td>';
            echo '<td>' . $component->getName() . '</td>';
            echo '<td>' . $user->getName() . '</td>';
            echo '<td>' . $phase->getName() . '</td>';
            echo '<td>';
            if ($references) {
                foreach ($references as $reference) {
                    echo '<a target="_blank" href="' . $reference->getURL() . '">' . $reference->getRef() . '</a>';
                }
            }
            echo '</td>';
            if ($use_linking) {
                echo '<td>';
                $parent = $ticket->getParent();
                if ($parent->getId() != 0) {
                    echo $parent->getTitle();
                }
                echo '</td>';
            }
            echo '<td>' . $ticket->getLastChange() . '</td>';

            echo '<td>';

            if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('edit_ticket')) {
                echo '<a class="edit_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
            }

            echo '<a class="comments_ticket" data-id="' . $ticket->getId() . '" href=""></a><span style="font-size:10px;">(' . $ticket->getNumComments() . ')</span>';
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        $tabs[] = new Tab('current-tickets', 'Current tickets');
    }

    $q = $GLOBALS['db']->get_results("SELECT * FROM ticket WHERE active = '0' AND deleted = '0' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    if ($GLOBALS['db']->num_rows > 0) {
        echo '<div id="archived_ticket_holder">';
        echo '<div style="margin-bottom: 10px;">';
        echo '<a href="" class="export_archived" target="_blank">Export to excel</a></div>';
        echo '<table width="100%" class="stat_table" id="archived_tickets">';
        echo '<thead>';
        echo '<tr><th>Title</th><th>Component</th><th>Responsible</th><th>Phase</th><th>Reference</th>';
        $use_linking = false;
        if ($GLOBALS['board']->getSettingValue("USE_LINKING")) {
            echo '<th>Parent</th>';
            $use_linking = true;
        }

        echo '<th>Archive date</th><th>Actions</th>';
        echo '</thead><tbody>';
        foreach ($q as $ticket_id) {
            $ticket = new Ticket($ticket_id->id);
            $component = $ticket->getComponent();
            $phase = $ticket->getPhase();
            $user = $ticket->getResponsible();
            $references = $ticket->getReferences();

            echo '<tr>';
            echo '<td>' . $ticket->getTitle() . '</td>';
            echo '<td>' . $component->getName() . '</td>';
            echo '<td>' . $user->getName() . '</td>';
            echo '<td>' . $phase->getName() . '</td>';
            echo '<td>';
            if ($references) {
                foreach ($references as $reference) {
                    echo '<a target="_blank" href="' . $reference->getURL() . '">' . $reference->getRef() . '</a>';
                }
            }
            echo '</td>';
            if ($use_linking) {
                echo '<td>';
                $parent = $ticket->getParent();
                if ($parent->getId() != 0) {
                    echo $parent->getTitle();
                }
                echo '</td>';
            }
            echo '<td>' . $ticket->getLastChange() . '</td>';

            echo '<td>';

            if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('edit_ticket')) {
                echo '<a class="edit_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
            }

            echo '<a class="comments_ticket" data-id="' . $ticket->getId() . '" href=""></a><span style="font-size:10px;">(' . $ticket->getNumComments() . ')</span>';
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        $tabs[] = new Tab('archived-tickets', 'Archived tickets');
    }

    $q = $GLOBALS['db']->get_results("SELECT * FROM ticket WHERE deleted = '1' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    if ($GLOBALS['db']->num_rows > 0) {
        echo '<div id="deleted_ticket_holder">';
        echo '<div style="margin-bottom: 10px;">';
        echo '<a href="" class="export_deleted" target="_blank">Export to excel</a></div>';
        echo '<table width="100%" class="stat_table" id="deleted_tickets">';
        echo '<thead>';
        echo '<tr><th>Title</th><th>Component</th><th>Responsible</th><th>Phase</th><th>Reference</th>';
        $use_linking = false;
        if ($GLOBALS['board']->getSettingValue("USE_LINKING")) {
            echo '<th>Parent</th>';
            $use_linking = true;
        }

        echo '<th>Delete date</th><th>Actions</th>';
        echo '</thead><tbody>';
        foreach ($q as $ticket_id) {
            $ticket = new Ticket($ticket_id->id);
            $component = $ticket->getComponent();
            $phase = $ticket->getPhase();
            $user = $ticket->getResponsible();
            $references = $ticket->getReferences();

            echo '<tr>';
            echo '<td>' . $ticket->getTitle() . '</td>';
            echo '<td>' . $component->getName() . '</td>';
            echo '<td>' . $user->getName() . '</td>';
            echo '<td>' . $phase->getName() . '</td>';
            echo '<td>';
            if ($references) {
                foreach ($references as $reference) {
                    echo '<a target="_blank" href="' . $reference->getURL() . '">' . $reference->getRef() . '</a>';
                }
            }
            echo '</td>';
            if ($use_linking) {
                echo '<td>';
                $parent = $ticket->getParent();
                if ($parent->getId() != 0) {
                    echo $parent->getTitle();
                }
                echo '</td>';
            }
            echo '<td>' . $ticket->getLastChange() . '</td>';

            echo '<td>';

            if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('edit_ticket')) {
                echo '<a class="edit_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
            }

            echo '<a class="comments_ticket" data-id="' . $ticket->getId() . '" href=""></a><span style="font-size:10px;">(' . $ticket->getNumComments() . ')</span>';
            echo '</td>';

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        $tabs[] = new Tab('deleted-tickets', 'Deleted tickets');
    }
}

$first_date = $GLOBALS['db']->get_row("SELECT created FROM ticket_stat ORDER BY created ASC LIMIT 1");
if ($first_date) {
    echo '<div id="ticket_history_holder">';
    $last_date = date("d.m.Y");
    $first_date = date("d.m.Y", time() - (60 * 60 * 24 * 7));
    echo '<div class="date_select">';
    echo '<span>Date limit: </span>';
    echo '<label for="from">from</label>';
    echo '<input type="text" id="from" name="from" value="' . $first_date . '"/>';
    echo '<label for="to">to</label>';
    echo '<input type="text" id="to" name="to" value="' . $last_date . '"/>';
    //echo ' <a href="" class="export_all" target="_blank">Export to excel</a>';
    echo '</div>';

    echo '<div id="ticket_changes" style="margin-top: 10px">';
    echo '<table width="100%" class="stat_table" id="tickets">';
    echo '<thead>';
    echo '<tr><th id="title_header">Title</th><th id="component_header">Component</th><th id="from_header">From phase</th>';
    echo '<th id="to_header">To phase</th><th id="date_header">Date</th></thead><tbody>';
    $q = $GLOBALS['db']->get_results("SELECT ticket_stat.* FROM ticket_stat LEFT JOIN ticket ON ticket_stat.ticket_id = ticket.id WHERE ticket_stat.created >= DATE_SUB(UTC_DATE(), INTERVAL 7 DAY) AND ticket.board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY ticket_stat.created DESC");
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

    $tabs[] = new Tab('ticket-history', 'Ticket history');
}

$res = $GLOBALS['db']->get_results("SELECT * FROM phase WHERE active = '1' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
if (!empty($res)) {
    echo '<div id="hang_time_holder">';
    echo '<table class="stat_table" style="float:left;">';
    echo '<tr><th>Phase</th><th>Hang time</th></tr>';

    foreach ($res as $row) {
        $hangtime = getAverageHangTime($row->id, $id);
        if ($hangtime != "") {
            echo '<tr><td>' . $row->name . '</td><td>' . $hangtime . '</td></tr>';
        }
    }
    echo '</table>';
    echo '</div><div style="clear:both;"></div>';

    $tabs[] = new Tab('hang-times', 'Hang times');
}

ob_end_clean();
?>

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

if (!empty($tabs)): ?>
    <div id="stat-tabs">
        <ul>
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

            foreach ($tabs as $tab): ?>
                <li><a href="#<?= $tab->id ?>"><?= $tab->label ?></a></li>
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

            endforeach; ?>
        </ul>
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

        foreach ($tabs as $tab): ?>
            <div id="<?= $tab->id ?>">
                <?= $tab->contents ?>
            </div>
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

        endforeach; ?>
    </div>
    <script>
        $(function () {
            $('#stat-tabs').tabs();
        });
    </script>
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

endif; ?>
