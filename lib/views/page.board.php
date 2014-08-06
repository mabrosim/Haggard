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

require_once './ticket.class.php';
require_once './priority.class.php';
require_once './user.class.php';

$GLOBALS['content'] = "";
/* if(isset($_SESSION['username']))
  {
  $user = new User($_SESSION['userid']);
  if($user->getPermission('move_ticket'))
  $GLOBALS['content'] .= '<script type="text/javascript" src="./js/ticket_move.js"></script>';
  }

  $GLOBALS['content'] .= '<script type="text/javascript" src="./js/ticket_table.js"></script>';
  $GLOBALS['content'] .= '<script type="text/javascript" src="./js/ticket_handler.js"></script>';
 */

if (!$GLOBALS['board']->getSettingValue('USE_PRIORITIES')) {
    $GLOBALS['content'] .= '<style><!--';
    $GLOBALS['content'] .= '.low_priority { background-color: ' . $GLOBALS['board']->getSettingValue('TICKET_COLOR1') . ' !important; }';
    $GLOBALS['content'] .= '.medium_priority { background-color: ' . $GLOBALS['board']->getSettingValue('TICKET_COLOR2') . ' !important; }';
    $GLOBALS['content'] .= '.major_priority { background-color: ' . $GLOBALS['board']->getSettingValue('TICKET_COLOR3') . ' !important; }';
    $GLOBALS['content'] .= '.showstopper_priority { background-color: ' . $GLOBALS['board']->getSettingValue('TICKET_COLOR4') . ' !important; }';

    $GLOBALS['content'] .= '--></style>';
}

$p_id = 'all';
$c_id = 'all';
$ticket_id = 0;

$get_p_id = filter_input(INPUT_GET, 'p_id');
$get_c_id = filter_input(INPUT_GET, 'c_id');

if (isset($get_p_id)) {
    $p_id = $GLOBALS['db']->escape($get_p_id);
    if (strstr($p_id, "p_")) {
        $p_id = substr($p_id, 2);
    }
}
if (isset($get_c_id)) {
    $c_id = $GLOBALS['db']->escape($get_c_id);
    if (strstr($c_id, "p_")) {
        $c_id = substr($c_id, 2);
    }
}

$cookie_p_id = filter_input(INPUT_COOKIE, 'p_id');
$cookie_c_id = filter_input(INPUT_COOKIE, 'c_id');
if (isset($cookie_p_id)) {
    if (strstr($cookie_p_id, "p_")) {
        $p_id = $GLOBALS['db']->escape($cookie_p_id);
    } else {
        // Possible hack attempt
        $p_id = '';
    }
}

if (isset($cookie_c_id)) {
    if (strstr($cookie_c_id, "c_")) {
        $c_id = $GLOBALS['db']->escape($cookie_c_id);
    } else {
        // Possible hack attempt
        $c_id = '';
    }
}

if ($p_id == null || $p_id == '' || $p_id == 'null') {
    $p_id = 'all';
}
if ($c_id == null || $c_id == '' || $c_id == 'null') {
    $c_id = 'all';
}

$p_arr = explode(htmlentities(','), $p_id);
$c_arr = explode(htmlentities(','), $c_id);

$get_ticket_id = filter_input(INPUT_GET, 'ticket_id');
if (isset($get_ticket_id)) {
    $ticket_id = $GLOBALS['db']->escape($get_ticket_id);
    if (is_numeric($ticket_id)) {
        $c_id = 'all';
        $p_id = 'all';
    }
}

function printPhaseTickets($phase, $cycle, $p_arr, $c_arr, $ticket_id) {
    $logged_user = NULL;

    if (isset($_SESSION['userid'])) {
        $logged_user = $GLOBALS['cur_user'];
    }

    $query = "SELECT id FROM ticket WHERE phase='" . $phase . "'";

    if ($GLOBALS['board']->getSettingValue("USE_CYCLES") == "1") {
        if ($cycle !== 0) {
            $query .= " AND cycle = " . $cycle . "";
        }
    }

    $query .= " AND active = '1' AND deleted = '0'";

    if ($GLOBALS['board']->getSettingValue("USE_PRIORITIES")) {
        $query .= "ORDER BY priority DESC, last_change DESC";
    } else {
        $query .= "ORDER BY last_change DESC, id DESC";
    }

    $result = $GLOBALS['db']->get_results($query);

    if ($GLOBALS['db']->num_rows == 0) {
        return;
    }

    foreach ($result as $row) {
        $ticket = new Ticket($row->id);

        $hide_ticket = false;
        if (isset($ticket_id) && $ticket_id != 0) {
            if ($ticket->getId() != $ticket_id && !in_array($ticket->getId(), $ticket->getChildrenId()) && $ticket->getId() != $ticket->getParentId()) {
                $hide_ticket = true;
            }
        }

        if (count($p_arr) > 0 && $p_arr[0] != 'all') {
            if (!in_array($ticket->getResponsibleId(), $p_arr)) {
                $hide_ticket = true;
            }
        }

        if (count($c_arr) > 0 && $c_arr[0] != 'all') {
            if (!in_array($ticket->getComponentId(), $c_arr)) {
                $hide_ticket = true;
            }
        }

        $hidden_info = false;

        $GLOBALS['content'] .= '<li class="ticket_holder" data-comp="' . $ticket->getComponentId() . '"';
        $GLOBALS['content'] .= ' data-resp="' . $ticket->getResponsibleId() . '"';
        $GLOBALS['content'] .= ' data-itemid="' . $ticket->getId() . '"';
        $GLOBALS['content'] .= ' data-phase="' . $ticket->getPhaseId() . '"';
        $GLOBALS['content'] .= ' data-prio="' . $ticket->getPriorityId() . '"';
        $GLOBALS['content'] .= ' data-child="' . $ticket->getFirstChildId() . '"';
        $GLOBALS['content'] .= ' data-parent="' . $ticket->getParentId() . '"';
        $GLOBALS['content'] .= ' data-changed="' . strtotime($ticket->getLastChange()) . '"';
        $GLOBALS['content'] .= ' data-wip="' . $ticket->getWIP() . '"';
        $GLOBALS['content'] .= ' data-created="' . strtotime($ticket->getCreated()) . '"';

        $priority = $ticket->getPriority();
        $component = $ticket->getComponent();

        if ($hide_ticket == true) {
            $GLOBALS['content'] .= ' style="display:none;"';
        }

        $GLOBALS['content'] .= '>';

        $GLOBALS['content'] .= '<div class="' . $priority->getCSSClass() . '"><span id="ticket_title"><p>' . $ticket->getTitle() . '</p></span>';

        $GLOBALS['content'] .= '<span id="ticket_info">';

        if ($ticket->getInfo() != "") {
            if ($logged_user) {
                if ($logged_user->getSetting('show_ticket_info') || $logged_user->getId() == 1) {
                    $GLOBALS['content'] .= '<p style="font-size: 13px;">';
                } else {
                    $hidden_info = true;
                    $GLOBALS['content'] .= '<p class="info_hidden" style="display:none; font-size: 12px;">';
                }
            } else {
                $GLOBALS['content'] .= '<p style="font-size: 12px;">';
            }

            $info = preg_replace('#[a-zA-Z]+://\S+#m', '<a href="$0" rel="nofollow" target="_blank">$0</a>', $ticket->getInfo());
            $GLOBALS['content'] .= $info . '</p>';
        }

        $references = $ticket->getReferences();
        if (count($references) > 0) {
            foreach ($references as $reference) {
                if ($logged_user) {
                    if ($logged_user->getSetting('show_ticket_reference') || $logged_user->getId() == 1) {
                        $GLOBALS['content'] .= '<p>';
                    } else {
                        $hidden_info = true;
                        $GLOBALS['content'] .= '<p class="info_hidden" style="display:none">';
                    }
                } else {
                    $GLOBALS['content'] .= '<p>';
                }

                if ($reference->getType() == "URL" || $reference->getType() == "GERRIT") {
                    $GLOBALS['content'] .= 'REF: ';
                }

                $GLOBALS['content'] .= '<a href="' . $reference->getURL() . '" target="_blank">';
                if ($reference->getType() == "URL") {
                    $GLOBALS['content'] .= 'External URL';
                } else if ($reference->getType() == "GERRIT") {
                    $GLOBALS['content'] .= 'Gerrit change';
                } else {
                    $GLOBALS['content'] .= $reference->getType() . ': ' . $reference->getId();
                }
                $GLOBALS['content'] .= '</a></p>';
            }
        }

        if ($logged_user) {
            if ($logged_user->getSetting('show_ticket_responsible') || $logged_user->getId() == 1) {
                $GLOBALS['content'] .= '<p>';
            } else {
                $hidden_info = true;
                $GLOBALS['content'] .= '<p class="info_hidden" style="display:none">';
            }
        } else {
            $GLOBALS['content'] .= '<p>';
        }

        $GLOBALS['content'] .= $ticket->getResponsible()->getName() . '</p>';

        if ($logged_user && ($logged_user->getSetting('show_ticket_component') || $logged_user->getId() == 1)) {
            $GLOBALS['content'] .= '<p>';
        } else {
            $hidden_info = true;
            $GLOBALS['content'] .= '<p class="info_hidden" style="display:none">';
        }
        $GLOBALS['content'] .= $component->getName() . '</p>';

        if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
            if ($logged_user && ($logged_user->getSetting('show_ticket_wip') || $logged_user->getId() == 1)) {
                $GLOBALS['content'] .= '<p>';
            } else {
                $hidden_info = true;
                $GLOBALS['content'] .= '<p class="info_hidden" style="display:none">';
            }

            $GLOBALS['content'] .= 'WIP: ' . $ticket->getWIP() . '</p>';
        }

        if ($logged_user && ($logged_user->getSetting('show_ticket_created') || $logged_user->getId() == 1)) {
            $GLOBALS['content'] .= '<p style="font-size: 11px">';
        } else {
            $hidden_info = true;
            $GLOBALS['content'] .= '<p class="info_hidden" style="display:none; font-size: 11px;">';
        }
        $GLOBALS['content'] .= 'Created: ' . date('d.m.Y H:i:s', strtotime($ticket->getCreated() . ' UTC')) . '</p>';

        if ($logged_user && ($logged_user->getSetting('show_ticket_changed') || $logged_user->getId() == 1)) {
            $GLOBALS['content'] .= '<p style="font-size: 11px">';
        } else {
            $hidden_info = true;
            $GLOBALS['content'] .= '<p class="info_hidden" style="display:none; font-size: 11px;">';
        }
        $GLOBALS['content'] .= 'Changed: ' . date('d.m.Y H:i:s', strtotime($ticket->getLastChange() . ' UTC')) . '</p>';


        $GLOBALS['content'] .= '</span>';

        if (isset($_SESSION['username']) && $logged_user->getId() != 1) {
            if ($hidden_info && $logged_user->getSetting('hide_extra_info') == 0) {
                $GLOBALS['content'] .= '<a class="show_all_info" data-shown="0"></a>';
            }

            if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('edit_ticket')) {
                $GLOBALS['content'] .= '<a class="edit_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
            }

            $GLOBALS['content'] .= '<a class="comments_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
            if ($GLOBALS['board']->getSettingValue("USE_LINKING")) {
                if ($ticket->getParentId() || $ticket->getFirstChildId()) {
                    $GLOBALS['content'] .= '<a class="links_ticket" data-id="' . $ticket->getId() . '"';
                    if ($ticket_id == $ticket->getId()) {
                        $GLOBALS['content'] .= ' style="background-image: url(./img/icons/56.png)"';
                    }
                    $GLOBALS['content'] .= '></a>';
                }
                $GLOBALS['content'] .= '<a class="new_child_ticket" data-parent="' . $ticket->getId() . '" ';
                $GLOBALS['content'] .= 'data-component="' . $component->getId() . '" data-phase="' . $ticket->getPhaseId() . '" ';
                if ($GLOBALS['board']->getSettingValue("USE_PRIORITIES")) {
                    $GLOBALS['content'] .= 'data-priority="' . $ticket->getPriorityId() . '"></a>';
                } else {
                    $GLOBALS['content'] .= 'data-priority="1"></a>';
                }
            }

            $references = $ticket->getReferences();

            if ($references[0] && !$logged_user->getSetting('show_ticket_reference')) {
                $GLOBALS['content'] .= '<a class="external_reference" target="_blank" href="' . $references[0]->getURL() . '"></a>';
            }

            $subscription = $GLOBALS['db']->get_var("SELECT id FROM ticket_subscription WHERE ticket_id = '" . $ticket->getId() . "' AND user_id = '" . $_SESSION['userid'] . "' LIMIT 1");

            if (!$subscription) {
                $GLOBALS['content'] .= '<a class="ticket_subscribe" data-id="' . $ticket->getId() . '"></a>';
            } else {
                $GLOBALS['content'] .= '<a class="ticket_unsubscribe" data-id="' . $ticket->getId() . '"></a>';
            }
        } else if ($logged_user && $logged_user->getId() == 1) {
            $GLOBALS['content'] .= '<a class="comments_ticket" data-id="' . $ticket->getId() . '" href=""></a>';
        } else {
            $GLOBALS['content'] .= '<a class="ticket_subscribe_email" data-id="' . $ticket->getId() . '"></a>';
        }
        $GLOBALS['content'] .= '</div>';

        $GLOBALS['content'] .= '</div></li>';
    }
}

$result = $row = $cycle = null;
$cycle_id = 0;

$get_cycle_id = filter_input(INPUT_GET, 'id');
if (isset($get_cycle_id) && $get_cycle_id >= 0) {
    $cycle_id = $get_cycle_id;
} else if (isset($_SESSION['current_cycle'])) {
    $cycle_id = $_SESSION['current_cycle'];
}

$cycle = new Cycle($cycle_id);
if ($cycle->getId() < 0) {
    $cycle = new Cycle(0);
}

$_SESSION['current_cycle'] = $cycle_id;
$GLOBALS['content'] .= '<div id="ticket_table">';
$GLOBALS['content'] .= '<div id="ticket_table_header">';

if ($GLOBALS['board']->getSettingValue("SHOW_TICKET_HELP")) {
    $GLOBALS['content'] .= '<div id="ticket_explanations">';

    $type1 = new Priority(0);
    $type2 = new Priority(1);
    $type3 = new Priority(2);
    $type4 = new Priority(3);

    if ($GLOBALS['board']->getSettingValue("USE_PRIORITIES")) {
        $GLOBALS['content'] .= '<p><span class="low_priority_text">Green</span>: ' . $type1->getName() . ' - ';
        $GLOBALS['content'] .= '<span class="medium_priority_text">Yellow</span>: ' . $type2->getName() . ' - ';
        $GLOBALS['content'] .= '<span class="major_priority_text">Orange</span>: ' . $type3->getName() . ' - ';
        $GLOBALS['content'] .= '<span class="showstopper_priority_text">Red</span>: ' . $type4->getName() . '</p>';
    } else {
        $GLOBALS['content'] .= '<p><span class="low_priority" style="width: 10px; height: 10px; display: inline-block;"></span> ' . $type1->getName() . ' item - ';
        $GLOBALS['content'] .= '<span class="medium_priority" style="width: 10px; height: 10px; display: inline-block;"></span> ' . $type2->getName() . ' item - ';
        $GLOBALS['content'] .= '<span class="major_priority" style="width: 10px; height: 10px; display: inline-block;"></span> ' . $type3->getName() . ' item - ';
        $GLOBALS['content'] .= '<span class="showstopper_priority" style="width: 10px; height: 10px; display: inline-block;"></span> ' . $type4->getName() . ' item</p>';
    }
    $GLOBALS['content'] .= '</div>';
}

$GLOBALS['content'] .= '<h1>';

$boards = array();
if (isset($GLOBALS['cur_user'])) {
    $boards = $GLOBALS['cur_user']->getUserBoards();
} else {
    $boards[] = $GLOBALS['board'];
}

if (count($boards) == 1) {
    $GLOBALS['content'] .= $GLOBALS['board']->getBoardName();
} else {
    $GLOBALS['content'] .= '<select name="board_select" id="board_select">';
    $found = false;
    foreach ($boards as $b) {
        if ($b->getBoardName() == $GLOBALS['board']->getBoardName()) {
            $GLOBALS['content'] .= '<option selected data-url="' . $b->getBoardURL() . '">' . $b->getBoardName() . '</option>';
            $found = true;
        } else {
            $GLOBALS['content'] .= '<option data-url="' . $b->getBoardURL() . '">' . $b->getBoardName() . '</option>';
        }
    }

    if ($found == false) {
        $GLOBALS['content'] .= '<option selected data-url="' . $GLOBALS['board']->getBoardURL() . '">' . $GLOBALS['board']->getBoardName() . '</option>';
    }

    $GLOBALS['content'] .= '</select>';
}

$team = $GLOBALS['board']->getSettingValue("BOARD_TEAM");
$team_email = $GLOBALS['board']->getSettingValue("BOARD_TEAM_EMAIL");
if (!empty($team) && !empty($team_email)) {
    $GLOBALS['content'] .= ' (<a href="mailto:' . $team_email . '">' . $team . '</a>)';
} else if (!empty($team)) {
    $GLOBALS['content'] .= ' (' . $team . ')';
} else if (!empty($team_email)) {
    $GLOBALS['content'] .= ' (<a href="mailto:' . $team_email . '">' . $team_email . '</a>)';
}

$GLOBALS['content'] .= '</h1>';

if ($cycle->getId() >= 0) {
    if ($GLOBALS['board']->getSettingValue("USE_CYCLES")) {
        $GLOBALS['content'] .= '<h2 style="text-align: left">Cycle ';
        $GLOBALS['content'] .= '<select name="cycle_select" class="cycle_select">';
        $cycles = $GLOBALS['board']->getCycles();
        foreach ($cycles as $c) {
            if ($cycle_id == $c->getId()) {
                $GLOBALS['content'] .= '<option value="' . $c->getId() . '" selected>' . $c->getName() . '</option>';
            } else {
                $GLOBALS['content'] .= '<option value="' . $c->getId() . '">' . $c->getName() . '</option>';
            }
        }
        $GLOBALS['content'] .= '</select>';
        // $GLOBALS['content'] .= $cycle->getName();
        $GLOBALS['content'] .= '</h2>';
        $GLOBALS['content'] .= '<h3>Time: ' . date("d.m.Y", strtotime($cycle->getStart() . ' UTC')) . ' - ';
        $GLOBALS['content'] .= date("d.m.Y", strtotime($cycle->getStop() . ' UTC')) . '</h3>';
    }

    $GLOBALS['content'] .= '<div style="display:none" id="current_cycle">' . $cycle->getId() . '</div>';
} else {
    $GLOBALS['content'] .= '<h1>No cycle selected</h1>';
    $GLOBALS['content'] .= '<div style="display:none" id="current_cycle">0</div>';
}


if ($GLOBALS['board']->getSettingValue("USE_WIP") && $GLOBALS['board']->getSettingValue("USE_CYCLES") == "1" && $cycle->getId() >= 0) {
    $GLOBALS['content'] .= '<h3>WIP points for this cycle: <span id="current_cycle_wip">' . $cycle->getCurrentWIP() . '</span> / <span id="current_cycle_wip_limit">' . $cycle->getWIPLimit() . '</span>';
    $GLOBALS['content'] .= ' - TO-DO / On-going: <span id="current_cycle_wip_left">' . $cycle->getWIPLeft() . '</span>';
    $GLOBALS['content'] .= '</h3>';
}

if ($cycle->getId() >= 0) {
    $GLOBALS['content'] .= '<div id="filter_tabs">';
    $components = $GLOBALS['board']->getComponents();
    $GLOBALS['content'] .= '<ul>';
    $GLOBALS['content'] .= '<li><a href="#sort_by_person">Filter by person</a></li>';
    if (count($components) > 0) {
        $GLOBALS['content'] .= '<li><a href="#sort_by_component">Filter by component</a></li>';
    }
    $GLOBALS['content'] .= '<li><a href="#sort_by_select">Sort by</a></li>';
    $GLOBALS['content'] .= '<li><a id="clear_all_filters" href="#sort_by_person">Clear all filters</a></li>';
    $GLOBALS['content'] .= '<li id="search"><input id="search_input" type="text" name="search" placeholder="Search filter"></input></li>';
    $GLOBALS['content'] .= '</ul>';

    $GLOBALS['content'] .= '<div id="sort_by_person">';

    $GLOBALS['content'] .= '<form style="display: inline">';
    require_once './session_settings.php';

    if ($p_id == 'all' || $p_id == '') {
        $GLOBALS['content'] .= '<input type="checkbox" data-id="all" id="p_all" checked name="ticket_select_person_all" /><label for="p_all">All</label>';
    } else {
        $GLOBALS['content'] .= '<input type="checkbox" data-id="all" id="p_all" name="ticket_select_person_all" /><label for="p_all">All</label>';
    }

    if ($p_id == 0 && $p_id !== 'all') {
        $GLOBALS['content'] .= '<input type="checkbox" data-id="0" id="p_0" checked name="ticket_select_person" /><label for="p_0">No responsible</label>';
    } else {
        $GLOBALS['content'] .= '<input type="checkbox" data-id="0" id="p_0" name="ticket_select_person" /><label for="p_0">No responsible</label>';
    }
    $users = $GLOBALS['board']->getUsers(1);
    foreach ($users as $user) {
        if ($p_id !== 'all' && $p_id !== 0 && in_array($user->getId(), $p_arr)) {
            $GLOBALS['content'] .= '<input type="checkbox" checked data-id="' . $user->getId() . '" id="p_' . $user->getId() . '" name="ticket_select_person"/><label for="p_' . $user->getId() . '">' . $user->getName() . '</label>';
        } else {
            $GLOBALS['content'] .= '<input type="checkbox" data-id="' . $user->getId() . '" id="p_' . $user->getId() . '" name="ticket_select_person"/><label for="p_' . $user->getId() . '">' . $user->getName() . '</label>';
        }
    }
    $GLOBALS['content'] .= '</form>';
    $GLOBALS['content'] .= '<div style="display: inline; font-size: 10px; margin-left: 20px;">Use CTRL+click to select multiple</div>';
    $GLOBALS['content'] .= '</div>';

    if (count($components) > 0) {
        $GLOBALS['content'] .= '<div id="sort_by_component">';

        $GLOBALS['content'] .= '<form style="display: inline;">';

        if ($c_id == 'all' || $c_id == '') {
            $GLOBALS['content'] .= '<input type="radio" data-id="all" id="c_all" checked name="ticket_select_component_all" /><label for="c_all">All</label>';
        } else {
            $GLOBALS['content'] .= '<input type="radio" data-id="all" id="c_all" name="ticket_select_component_all" /><label for="c_all">All</label>';
        }

        if ($c_id == 0 && $c_id != 'all') {
            $GLOBALS['content'] .= '<input type="checkbox" checked data-id="0" id="c_0" name="ticket_select_component" /><label for="c_0">No component</label>';
        } else {
            $GLOBALS['content'] .= '<input type="checkbox" data-id="0" id="c_0" name="ticket_select_component" /><label for="c_0">No component</label>';
        }
        foreach ($components as $component) {
            if ($c_id !== 'all' && $c_id !== 0 && in_array($component->getId(), $c_arr)) {
                $GLOBALS['content'] .= '<input type="checkbox" checked data-id="' . $component->getId() . '" id="c_' . $component->getId() . '" name="ticket_select_component"/><label for="c_' . $component->getId() . '">' . $component->getName() . '</label>';
            } else {
                $GLOBALS['content'] .= '<input type="checkbox" data-id="' . $component->getId() . '" id="c_' . $component->getId() . '" name="ticket_select_component"/><label for="c_' . $component->getId() . '">' . $component->getName() . '</label>';
            }
        }
        $GLOBALS['content'] .= '</form>';
        $GLOBALS['content'] .= '<div style="display: inline; font-size: 10px; margin-left: 20px;">Use CTRL+click to select multiple</div>';
        $GLOBALS['content'] .= '</div>';
    }

    $GLOBALS['content'] .= '<div id="sort_by_select">';
    $GLOBALS['content'] .= '<form>';

    $GLOBALS['content'] .= '<input type="radio" data-order="DATA" id="SORT_BY_NAME" name="ticket_select_sorting"/><label for="SORT_BY_NAME">Name</label>';

    $GLOBALS['content'] .= '<input type="radio" data-order="COMPONENT" id="SORT_BY_COMPONENT" name="ticket_select_sorting"/><label for="SORT_BY_COMPONENT">Component</label>';

    $GLOBALS['content'] .= '<input type="radio" data-order="RESPONSIBLE" id="SORT_BY_RESPONSIBLE" name="ticket_select_sorting"/><label for="SORT_BY_RESPONSIBLE">Responsible</label>';


    if ($GLOBALS['board']->getSettingValue("USE_PRIORITIES")) {
        $GLOBALS['content'] .= '<input type="radio" checked data-order="PRIORITY" id="SORT_BY_PRIORITY" name="ticket_select_sorting"/><label for="SORT_BY_PRIORITY">Priority</label>';
    } else {
        $GLOBALS['content'] .= '<input type="radio" data-order="TYPE" id="SORT_BY_TYPE" name="ticket_select_sorting"/><label for="SORT_BY_TYPE">Ticket type</label>';
    }

    $GLOBALS['content'] .= '<input type="radio" data-order="CHANGED" id="SORT_BY_CHANGED" name="ticket_select_sorting"/><label for="SORT_BY_CHANGED">Last change</label>';

    $GLOBALS['content'] .= '<input type="radio" data-order="CREATED" id="SORT_BY_CREATED" name="ticket_select_sorting"/><label for="SORT_BY_CREATED">Ticket creation</label>';

    if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
        $GLOBALS['content'] .= '<input type="radio" data-order="WIP" id="SORT_BY_WIP" name="ticket_select_sorting"/><label for="SORT_BY_WIP">WIP</label>';
    }
    $GLOBALS['content'] .= '</form>';
    $GLOBALS['content'] .= '</div>';
    $GLOBALS['content'] .= '</div>';
}

$GLOBALS['content'] .= '</div>';

$phases = $GLOBALS['board']->getPhases();

if (count($phases) > 0 && $cycle->getId() >= 0) {
    $GLOBALS['content'] .= '<div id="phase_descriptors">';
    $GLOBALS['content'] .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:0; border-bottom: 0; border-top: 1px solid #ccc;">';

    $num_phases = 0;

    $GLOBALS['content'] .= '<tr width="100%">';
    foreach ($phases as $phase) {
        if (!$phase->isActive()) {
            continue;
        }

        $show_phase = true;
        $num_phases++;

        if (isset($_SESSION['settings']['show_phase' . $phase->getId()])) {
            if ($_SESSION['settings']['show_phase' . $phase->getId()] == false) {
                $show_phase = false;
            }
        }

        if ($show_phase) {
            $GLOBALS['content'] .= '<th id="phase_name_' . $phase->getId() . '" data-name="' . $phase->getName() . '"><h2>';
            $GLOBALS['content'] .= '<span class="name">' . $phase->getName() . '</span></h2><div class="phase_functions">';
            $GLOBALS['content'] .= '<a class="help_text" href="" data-id="' . $phase->getId() . '"></a>';
        } else {
            $GLOBALS['content'] .= '<th width="36px" id="phase_name_' . $phase->getId() . '" data-name="' . $phase->getName() . '"><h2>';
            $GLOBALS['content'] .= '<span class="name"></span></h2><div class="phase_functions">';
            $GLOBALS['content'] .= '<a class="help_text" data-id="' . $phase->getId() . '" href="" style="display: none;"></a>';
        }

        if (isset($_SESSION['username'])) {
            $GLOBALS['content'] .= '<a data-id="' . $phase->getId() . '" href="" class="hide_phase" style="';
            if ($show_phase) {
                $GLOBALS['content'] .= 'background-image: url(\'./img/icons/12.png\');';
            } else {
                $GLOBALS['content'] .= 'background-image: url(\'./img/icons/11.png\');';
            }
            $GLOBALS['content'] .= '"></a>';

            $subscribed = $GLOBALS['db']->get_var("SELECT id FROM phase_subscription WHERE user_id = '" . $_SESSION['userid'] . "' AND phase_id = '" . $phase->getId() . "' LIMIT 1");
            if (!$subscribed) {
                $GLOBALS['content'] .= '<a class="phase_subscribe" data-id="' . $phase->getId() . '">';
            } else {
                $GLOBALS['content'] .= '<a class="phase_unsubscribe" data-id="' . $phase->getId() . '">';
            }

            $GLOBALS['content'] .= '</a></div>';
        }
        $GLOBALS['content'] .= '</th>';
    }
    $GLOBALS['content'] .= '</tr>';

    $GLOBALS['content'] .= '</table>';
    $GLOBALS['content'] .= '</div>';

    $orig_width = 100 / $num_phases;
    $GLOBALS['content'] .= '<table width="100%" cellspacing="0" cellpadding="0" style="margin:0;" class="tickets">';
    /* Print out phase lists */
    $GLOBALS['content'] .= '<tr>';
    foreach ($phases as $phase) {
        if (!$phase->isActive()) {
            continue;
        }
        $show_phase = true;
        if (isset($_SESSION['settings']['show_phase' . $phase->getId()])) {
            if ($_SESSION['settings']['show_phase' . $phase->getId()] == false) {
                $show_phase = false;
            }
        }

        if ($show_phase) {
            $GLOBALS['content'] .= '<td id="phase_ticket_holder' . $phase->getId() . '" data-orig-width="' . $orig_width . '">';
            $GLOBALS['content'] .= '<ul id="' . $phase->getCSS() . '" class="phase' . $phase->getId() . '" data-phase-name="' . $phase->getName() . '" data-id="' . $phase->getId() . '" data-forcec="' . $phase->getForceComment() . '">';
        } else {
            $GLOBALS['content'] .= '<td id="phase_ticket_holder' . $phase->getId() . '" width="36px" data-orig-width="' . $orig_width . '">';
            $GLOBALS['content'] .= '<ul style="display: none;" id="phase' . $phase->getCSS() . '" class="phase' . $phase->getId() . '" data-phase-name="' . $phase->getName() . '" data-id="' . $phase->getId() . '" data-forcec="' . $phase->getForceComment() . '">';
        }

        $c = $cycle->getId();
        if ($phase->getCSS() == "phase0") {
            $c = 0;
        }

        printPhaseTickets($phase->getId(), $c, $p_arr, $c_arr, $ticket_id);

        $GLOBALS['content'] .= '</ul>';
        $GLOBALS['content'] .= '</td>';
    }
    $GLOBALS['content'] .= '</tr></table>';

    $GLOBALS['content'] .= '<input name="backlog-order" type="hidden" />';
    $GLOBALS['content'] .= '<div style="clear:both;"></div>';
}

$GLOBALS['content'] .= '</div>';

/*
  $GLOBALS['content'] .= '<script type="text/javascript">
  var tab_selected = -1;
  if($.cookie("tab_selected"))
  {
  tab_selected = $.cookie("tab_selected");
  }

  $("#filter_tabs").tabs({
  collapsible: true,
  selected: tab_selected,
  cache: true,
  cookie: { expires: 60 },
  select: function(event, ui)
  {
  if(tab_selected == ui.index)
  {
  tab_selected = -1;
  $.cookie("tab_selected", -1);
  }
  else
  {
  tab_selected = ui.index;
  $.cookie("tab_selected", ui.index);
  }
  }
  });

  </script>';
 */
echo $GLOBALS['content'];

require_once 'footer.php';
?>
