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

echo '<div id="footer">';
if (isset($GLOBALS['cur_user'])) {
    echo '<p>You are on timezone ' . $GLOBALS['cur_user']->getTimezone() . ' (' . date('d-m-Y H:i:s') . ')</p>';
}
echo '<p>Haggard Agile Board v2.1 (C) Microsoft Mobile 2014</p>';
echo '<p><a href="http://wiki/Haggard/" target="_blank">Haggard Wiki</a> - ';
echo '<a href="https://wiki/Haggard/SupportHaggard" target="_blank">Support Haggard</a> - ';
echo '<a href="https://www.yammer.com" target="_blank">Yammer group</a></p>';

echo '<div style="margin: 0; margin-top:10px; text-align: center;">';
echo '<a href="http://www.microsoft.com" target="_blank"><img src="./img/MS_corp_logo2.jpg"></a>';
echo '</div>';

echo '</div>';
?>
