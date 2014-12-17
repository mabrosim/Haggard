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
require_once '../3rdparty/PHPMailer/class.phpmailer.php';
require_once '../3rdparty/PHPMailer/class.smtp.php';

class Email
{

    private $message = '';
    private $recipient = '';
    private $subject = '';
    private $mailer = null;

    public function __construct()
    {
        $this->mailer = new PHPMailer();

        /* Set SMTP conf */
        $this->mailer->isSMTP();
        $this->mailer->isHTML();
        $this->mailer->SMTPDebug = 0;
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Host = $GLOBALS['smtp_server'];
        $this->mailer->Port = $GLOBALS['smtp_port'];
        $this->mailer->Username = $GLOBALS['smtp_username'];
        $this->mailer->Password = $GLOBALS['smtp_password'];
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom('haggard@haggard', 'Haggard Board');
        $this->mailer->addReplyTo('haggard_no_reply@haggard', 'Haggard Board');
    }

    public function setMessage($msg)
    {
        $this->message = $msg;
    }

    public function setRecipient($user)
    {
        if ($user && $user->getSetting('send_email')) {
            $this->setAddress($user->getEmail());
        } else {
            $this->recipient = "";
        }
    }

    public function setAddress($resp)
    {
        $this->recipient = $resp;
    }

    public function ticketMove($ticket)
    {
        $this->setSubject('Ticket ' . $ticket->getTitle() . ' was updated!');

        $this->message = 'Ticket ' . $ticket->getTitle() . ' was moved to ' . $ticket->getPhase()->getName();
        $this->message .= ' by <a href="mailto:' . $GLOBALS['cur_user']->getEmail() . '">' . $GLOBALS['cur_user']->getName() . '</a><br>';
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

    public function setSubject($subj)
    {
        $this->subject = '[Haggard] ' . $subj;
    }

    public function generateTicketFooter($ticket)
    {
        $this->message .= 'Ticket URL: <a href="' . $ticket->getURL() . '">';
        $this->message .= $ticket->getURL() . '</a><br><br>';
    }

    public function ticketchildrensamephase($parent, $phase)
    {
        $this->setSubject('All children of ' . $parent->getTitle() . ' are in same phase!');
        $this->message = 'All child tickets of ' . $parent->getTitle() . ' are in ' . $phase->getName() . '!<br><br>';
        $this->generateTicketFooter($parent);
    }

    public function send()
    {
        if ($GLOBALS['board']->getSettingValue("SEND_EMAIL") != 1) {
            return;
        }

        if ($GLOBALS['cur_user'] == null || $GLOBALS['cur_user']->getEmail() == $this->recipient || $GLOBALS['cur_user']->getId() == 1 ||
            !isset($this->recipient) || $this->recipient == ""
        ) {
            return;
        }

        if (isset($this->recipient) && $this->recipient != "" &&
            isset($this->message) && $this->message != "" &&
            isset($this->subject) && $this->subject != ""
        ) {
            $this->mailer->addAddress($this->recipient);
            $this->mailer->Subject = $this->subject;

            $message = $this->message;
            $message .= '<br>This email was generated by ' . $GLOBALS['board']->getBoardName();
            $message .= ' (<a href="' . $GLOBALS['board']->getBoardURL() . '">' . $GLOBALS['board']->getBoardURL() . '</a>)';
            $message .= '<br><br>Please do not respond to this message.';

            $this->mailer->msgHTML($message);

            //send the message, check for errors
            if (!$this->mailer->send()) {
                error_log("Mailer Error: " . $this->mailer->ErrorInfo);
            }
        } else {
            $log = "Haggard [3.0] - Email error. Params: ";
            $log .= "Recipient: " . $this->recipient . ", ";
            $log .= "Subject: " . $this->subject . ", ";
            $log .= "Message: " . $this->message . ", ";
            error_log($log);
        }
    }
}
