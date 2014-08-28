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

$GLOBALS['board_version'] = "3.0";
$GLOBALS['maintenance_mode'] = false;
$GLOBALS['haggard_url'] = "haggard.domain.com";

/* E-mail settings */
$GLOBALS['send_email_notifications'] = false;
$GLOBALS['smtp_server'] = 'your.smtp.company.com';
$GLOBALS['smtp_port'] = 25;

/* LDAP configuration */
$GLOBALS['use_ldap'] = false;
$GLOBALS['ldap_domain_controllers'] = array();
$GLOBALS['ldap_admin'] = null;
$GLOBALS['ldap_password'] = null;

$GLOBALS['password_salt'] = '';

/* external scripts */
$GLOBALS['JQUERY_JS'] = '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js';
$GLOBALS['JQUERY_UI_CSS'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css';
$GLOBALS['JQUERY_UI_JS'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js';
$GLOBALS['JQUERY_QTIP_JS'] = '//qtip2.com/v/2.2.0/basic/jquery.qtip.min.js';
$GLOBALS['JQUERY_QTIP_CSS'] = '//qtip2.com/v/2.2.0/basic/jquery.qtip.min.css';
$GLOBALS['JQUERY_MIGRATE_JS'] = '//code.jquery.com/jquery-migrate-1.2.1.min.js';

/* If running benchmarks */
define("BENCHMARK", 0);
