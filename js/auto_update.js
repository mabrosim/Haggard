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

var pageGenID = 0;

var processUpdate = function(response)
{
    if (pageGenID < response)
    {
        if (current_page !== null && current_page !== "" && pageGenID !== 0)
        {
            $("#content").load("./lib/dyn_content.php?page=page." + current_page + ".php");
        }

        pageGenID = response;
    }
};

var checkUpdates = function(response)
{
    var serverPoll = setInterval(function()
    {
        if (current_page === "board")
        {
            $.post('./lib/dyn_content.php?jquery=jquery.autoupdate.php',
                    {"func": "last_update"}, processUpdate);
        }

        $.post('./lib/dyn_content.php?jquery=jquery.notifications.php', {'func': 'number_new'}, function(data)
        {
            if (parseInt(data) > 0)
            {
                $('#notification_count').css('background-color', '#FF0000');
                $('#notification_count').html(data);
            }
        });

    }, 10000);
};

//  Check for updates every 10 seconds
$(document).ready(checkUpdates);
