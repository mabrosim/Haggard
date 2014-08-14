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

require_once 'user.class.php';
require_once 'cycle.class.php';
require_once 'phase.class.php';
require_once 'priority.class.php';
require_once 'component.class.php';
require_once 'reference.class.php';
require_once 'board.class.php';

class Ticket {

    private $id = 0;
    private $board_id = 0;
    private $board = NULL;
    private $title = "";
    private $info = "";
    private $responsible = 0;
    private $wip = 0;
    private $cycle = 0;
    private $phase = 0;
    private $priority = 0;
    private $reference_string = "";
    private $references = array();
    private $active = 0;
    private $deleted = 0;
    private $component = 0;
    private $last_change = 0;
    private $created = 0;
    private $status = "Active";

    public function __construct($id) {
        if ($id) {
            $this->id = $GLOBALS['db']->escape($id);
            $ticket = $GLOBALS['db']->get_row("SELECT * FROM ticket WHERE id = '" . $id . "'");
            if (!$ticket) {
                return;
            }

            $this->board_id = $ticket->board_id;
            $this->title = $ticket->data;
            $this->responsible = $ticket->responsible;
            $this->wip = $ticket->wip;
            $this->cycle = $ticket->cycle;
            $this->phase = $ticket->phase;
            $this->priority = $ticket->priority;
            $this->reference_string = $ticket->reference_id;
            $this->active = $ticket->active;
            $this->deleted = $ticket->deleted;
            $this->component = $ticket->component;
            $this->last_change = $ticket->last_change;
            $this->created = $ticket->created;
            $this->info = $ticket->info;

            if ($this->active == 0) {
                $this->status = "Archived";
            }
            if ($this->deleted == 1) {
                $this->status = "Deleted";
            }

            $name = $GLOBALS['db']->get_var("SELECT name FROM board WHERE id = '" . $this->board_id . "'");
            $this->board = new Board($name);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return htmlentities($this->title, ENT_QUOTES);
    }

    public function getInfo() {
        return htmlentities($this->info, ENT_QUOTES);
    }

    public function getPriority() {
        return new Priority($this->priority);
    }

    public function getPriorityId() {
        return $this->priority;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getBoard() {
        return $this->board;
    }

    public function getReferences() {
        if (!$this->reference_string) {
            return NULL;
        }

        $str = trim($this->reference_string);
        $references = explode(',', $str);
        if (count($references) <= 0) {
            return NULL;
        }

        foreach ($references as $ref) {
            $this->references[] = new Reference($ref);
        }
        return $this->references;
    }

    public function getReferenceString() {
        return $this->reference_string;
    }

    public function isActive() {
        return $this->active;
    }

    public function isDeleted() {
        return $this->deleted;
    }

    public function getComponentId() {
        return $this->component;
    }

    public function getComponent() {
        return new Component($this->component);
    }

    public function getLastChange() {
        return $this->last_change;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getComponentStr() {
        $comp = $GLOBALS['db']->get_var("SELECT name FROM component WHERE id = '" . $this->component . "' LIMIT 1");
        return $comp;
    }

    public function getPriorityStr() {
        $priority = new Priority($this->priority);
        return $priority->getName();
    }

    public function addHistory($user_id, $data) {
        $user_id = $GLOBALS['db']->escape($user_id);
        $data = $GLOBALS['db']->escape($data);
        $GLOBALS['db']->query("INSERT INTO ticket_history (ticket_id, user_id, data, created) VALUES ('" . $this->id . "', '" . $user_id . "', '" . $data . "', UTC_TIMESTAMP())");
    }

    public function addComment($user_id, $comment) {
        $user_id = $GLOBALS['db']->escape($user_id);
        $comment = $GLOBALS['db']->escape($comment);
        $GLOBALS['db']->query("INSERT INTO ticket_comment (ticket_id, user_id, data, created) VALUES ('" . $this->id . "', '" . $user_id . "', '" . $comment . "', UTC_TIMESTAMP())");
    }

    public function getResponsibleId() {
        return $this->responsible;
    }

    public function getResponsible() {
        return new User($this->responsible);
    }

    public function getWIP() {
        return $this->wip;
    }

    public function getCycle() {
        return new Cycle($this->cycle);
    }

    public function getCycleId() {
        return $this->cycle;
    }

    public function setPhaseId($new_phase) {
        $this->phase = $new_phase;
    }

    public function getPhase() {
        return new Phase($this->phase);
    }

    public function getPhaseId() {
        return $this->phase;
    }

    public function getParentId() {
        $link = $GLOBALS['db']->get_row("SELECT parent FROM ticket_link WHERE child = '" . $this->id . "' LIMIT 1");

        if ($link) {
            return $link->parent;
        } else {
            return 0;
        }
    }

    public function getParent() {
        return new Ticket($this->getParentId());
    }

    public function getFirstChildId() {
        $link = $GLOBALS['db']->get_row("SELECT child FROM ticket_link WHERE parent = '" . $this->id . "' LIMIT 1");
        if ($link) {
            return $link->child;
        } else {
            return 0;
        }
    }

    public function getChildrenId() {
        $ret = array();
        $link = $GLOBALS['db']->get_results("SELECT child FROM ticket_link WHERE parent = '" . $this->id . "'");
        if ($link) {
            foreach ($link as $child) {
                $ret[] = $child->child;
            }
        }
        return $ret;
    }

    public function getNumChildren() {
        return $GLOBALS['db']->get_var("SELECT COUNT(*) FROM ticket_link WHERE parent = '" . $this->id . "'");
    }

    public function getChildren() {
        $ret = array();
        $link = $GLOBALS['db']->get_results("SELECT child FROM ticket_link WHERE parent = '" . $this->id . "'");
        if ($link) {
            foreach ($link as $child) {
                $ret[] = new Ticket($child->child);
            }
        }
        return $ret;
    }

    public function getNumComments() {
        return $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket_comment WHERE ticket_id = '" . $this->id . "'");
    }

    public function getURL() {
        return $this->board->getBoardURL() . '?ticket_id=' . $this->id;
    }

    public function copyTo($board_id, $user_id) {
        if ($board_id == null || $user_id == null) {
            return;
        }

        $b = new Board($board_id);
        $c = $b->getCurrentCycle();
        $ps = $b->getPhases();
        $p = $ps[0]->getId();

        $GLOBALS['db']->query("INSERT INTO ticket (board_id,
                                                   data,
                                                   info,
                                                   responsible,
                                                   wip,
                                                   cycle,
                                                   phase,
                                                   priority,
                                                   reference_id,
                                                   active,
                                                   deleted,
                                                   last_change,
                                                   created) VALUES
                                                   ('" . $board_id . "',
                                                    '" . $this->title . "',
                                                    '" . $this->info . "',
                                                    '" . $user_id . "',
                                                    '" . $this->wip . "',
                                                    '" . $c->getId() . "',
                                                    '" . $p . "',
                                                    '" . $this->priority . "',
                                                    '" . $this->reference_string . "',
                                                    '" . $this->active . "',
                                                    '" . $this->deleted . "',
                                                    UTC_TIMESTAMP(),
                                                    UTC_TIMESTAMP())");
        $new_id = $GLOBALS['db']->insert_id;
        $hist = "Ticket copied from board " . $this->board->getBoardName();
        $new_ticket = new Ticket($new_id);

        $new_ticket->addHistory($_SESSION['userid'], $hist);

        $comments = $GLOBALS['db']->get_results("SELECT * FROM ticket_comment WHERE ticket_id = '" . $this->id . "'");
        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $GLOBALS['db']->query("INSERT INTO ticket_comment (ticket_id, user_id, data, created) VALUES
                                                                  ('" . $new_id . "', '" . $comment->user_id . "', '" . $comment->data . "', '" . $comment->created . "')");
            }
        }
    }

    public function moveTo($board_id, $user_id) {
        if ($board_id == null || $user_id == null) {
            return;
        }

        $b = new Board($board_id);
        if ($b->getBoardId() == $this->board->getBoardId()) {
            return;
        }

        $c = $b->getCurrentCycle();
        $ps = $b->getPhases();
        $p = $ps[0]->getId();


        $GLOBALS['db']->query("UPDATE ticket SET board_id = '" . $board_id . "',
                                                 responsible = " . $user_id . ",
                                                 cycle = '" . $c->getId() . "',
                                                 phase = '" . $p . "',
                                                 last_change = UTC_TIMESTAMP()
                                                 WHERE id = '" . $this->id . "' LIMIT 1");

        $hist = "Ticket moved from board " . $this->board->getBoardName() . " to " . $b->getBoardName();
        $this->addHistory($_SESSION['userid'], $hist);
    }

}

?>
