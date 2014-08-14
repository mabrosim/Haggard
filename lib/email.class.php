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

require_once '../config/board.config.php';
require_once '../config/global.config.php';

class Email {

    private $headers = "";
    private $message = "";
    private $receipient = "";
    private $subject = "";

    public function __construct() {
        /* Set SMTP conf */
        ini_set("smtp", $GLOBALS['smtp_server']);
        ini_set("smtp_port", $GLOBALS['smtp_port']);

        $this->headers = 'MIME-Version: 1.0' . "\r\n";
        $this->headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $this->headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
    }

    public function setSubject($subj) {
        $this->subject = 'Haggard boards: ' . $subj;
        $this->subject = $subj;
        $this->headers .= "Subject: " . $subj . "\r\n";
    }

    public function setMessage($msg) {
        $this->message = $msg;
    }

    public function setReceipient($user) {
        if ($user && $user->getSetting('send_email')) {
            $this->setAddress($user->getEmail());
        } else {
            $this->receipient = "";
        }
    }

    public function setAddress($resp) {
        $this->receipient = $resp;
    }

    public function generateTicketFooter($ticket) {
        $this->message .= 'To see the ticket please visit <a href="' . $ticket->getURL() . '">';
        $this->message .= $ticket->getURL() . '</a><br><br>';
    }

    public function ticketMove($ticket) {
        $this->setSubject('Ticket ' . $ticket->getTitle() . ' was updated!');
        $this->message = 'Ticket ' . $ticket->getTitle() . ' was moved to ' . $ticket->getPhase()->getName() . ' by ' . $_SESSION['username'];
        $this->message .= '<br><br>';
        $this->message .= 'Responsible: <a href="mailto:' . $ticket->getResponsible()->getEmail() . '">' . $ticket->getResponsible()->getName() . '</a><br>';
        $this->message .= 'Priority: ' . $ticket->getPriority()->getName() . '<br>';
        if ($GLOBALS['board']->getSettingValue('USE_WIP')) {
            $this->message .= 'WIP points: ' . $ticket->getWIP() . '<br>';
        }

        $references = $ticket->getReferences();
        if ($references) {
            foreach ($references as $reference) {
                $this->message .= 'Reference: <a href="' . $reference->getURL() . '">' . $reference->getRef() . '</a><br><br>';
            }
        }
        $this->generateTicketFooter($ticket);
    }

    public function ticketchildrensamephase($parent, $phase) {
        $this->setSubject('All children of ' . $parent->getTitle() . ' are in same phase!');
        $this->message = 'All child tickets of ' . $parent->getTitle() . ' are in ' . $phase->getName() . '!<br><br>';
        $this->generateTicketFooter($parent);
    }

    public function send() {
        if ($GLOBALS['board']->getSettingValue("SEND_EMAIL") != 1) {
            return;
        }

        if ($GLOBALS['cur_user'] == null || $GLOBALS['cur_user']->getEmail() == $this->receipient || $GLOBALS['cur_user']->getId() == 1 ||
                !isset($this->receipient) || $this->receipient == "") {
            return;
        }

        $this->headers .= 'From: Haggard Boards <haggard@haggard>' . "\r\n";
        $this->headers .= 'Reply-to: Haggard Boards <haggard@haggard>' . "\r\n";

        if (isset($this->receipient) && $this->receipient != "" &&
                isset($this->message) && $this->message != "" &&
                isset($this->subject) && $this->subject != "") {
            $message = $this->message;
            $message .= '<br>This email was generated by ' . $GLOBALS['board']->getBoardName();
            $message .= ' (<a href="' . $GLOBALS['board']->getBoardURL() . '">' . $GLOBALS['board']->getBoardURL() . '</a>)';
            $message .= '<br><br>Please do not respond to this message.';

            if (mail($this->receipient, $this->subject, $message, $this->headers) == false) {
                $log = "Haggard [2.0] - Email error. Params: ";
                $log .= "Receipient: " . $this->receipient . ", ";
                $log .= "Subject: " . $this->subject . ", ";
                $log .= "Message: " . $this->message . ", ";
                $log .= "Headers: " . $this->headers;
                error_log($log);
            }
        } else {
            $log = "Haggard [2.0] - Email error. Params: ";
            $log .= "Receipient: " . $this->receipient . ", ";
            $log .= "Subject: " . $this->subject . ", ";
            $log .= "Message: " . $this->message . ", ";
            $log .= "Headers: " . $this->headers;
            error_log($log);
        }
    }

}

?>
