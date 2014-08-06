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

if (!isset($_SESSION['userid'])) {
    return;
}

echo '<script type="text/javascript" src="./3rdparty/jquery-tablesorter/jquery.tablesorter.min.js" charset="UTF-8"></script>';
require_once './ticket.class.php';
require_once './phase.class.php';
require_once './user.class.php';
require_once './reference.class.php';
require_once './component.class.php';

echo '<h1>My tickets</h1>';
$q = $GLOBALS['db']->get_results("SELECT * FROM ticket WHERE responsible = '" . $_SESSION['userid'] . "' AND deleted = '0' ORDER BY data");

if ($GLOBALS['db']->num_rows == 0) {
    echo '<p>You do not have any tickets</p>';
    return;
}

echo '<div id="my_ticket_holder">';
echo '<div style="margin-bottom: 10px;">';
echo '<a href="#" class="export_my_tickets">Export all my tickets to excel</a></div>';
echo '<table width="100%" class="stat_table tablesorter" id="my_tickets">';
echo '<thead>';
echo '<tr><th>Title</th><th>Board</th><th>Component</th><th>Phase</th><th>Reference</th><th>Parent</th><th>Last modified</th><th>Status</th><th>Actions</th>';
echo '</thead><tbody>';
foreach ($q as $ticket_id) {
    $ticket = new Ticket($ticket_id->id);
    $component = $ticket->getComponent();
    $phase = $ticket->getPhase();
    $references = $ticket->getReferences();
    $t_board = $ticket->getBoard();

    echo '<tr>';
    echo '<td><a href="' . $ticket->getURL() . '">' . $ticket->getTitle() . '</a></td>';
    echo '<td><a href="' . $t_board->getBoardURL() . '">' . $t_board->getBoardName() . '</a></td>';
    echo '<td>' . $component->getName() . '</td>';
    echo '<td>' . $phase->getName() . '</td>';
    echo '<td>';
    if ($references) {
        foreach ($references as $reference) {
            echo '<a target="_blank" href="' . $reference->getURL() . '">' . $reference->getRef() . '</a>';
        }
    }
    echo '</td>';
    echo '<td>';
    $parent = $ticket->getParent();
    if ($parent->getId() != 0) {
        echo '<a href="' . $parent->getURL() . '">' . $parent->getTitle() . '</a>';
    }
    echo '</td>';
    echo '<td>' . $ticket->getLastChange() . '</td>';
    echo '<td>' . $ticket->getStatus() . '</td>';

    echo '<td>';

    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('edit_ticket', $t_board->getBoardId())) {
        echo '<a class="edit_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
    }

    echo '<a class="comments_ticket" data-id="' . $ticket->getId() . '" href=""></a><span style="font-size:10px;">(' . $ticket->getNumComments() . ')</span>';
    echo '</td>';

    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '<script type="text/javascript">';
echo '$(function() {';
echo 'var $table = $("table").tablesorter({
    widthFixed : true,
    widgets: ["zebra", "filter"],
    widgetOptions : {
      filter_cssFilter   : \'\',
      filter_ignoreCase  : true,
      filter_hideFilters : false,
      filter_searchDelay : 300,
      filter_anyMatch : true,
      filter_columnFilters: true,
      filter_liveSearch : true,
      filter_saveFilters : true
    }});';
echo '$(".export_my_tickets").click(function(e)
        {
            window.open("./lib/xls_export.php?type=my_tickets", "_newtab");
        });';
echo '});';
echo '</script>';
?>
