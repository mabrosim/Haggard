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

$(document).ready(function()
{
    var logged_in = false;
    var login_name = "";

    $("#login_dialog").dialog({
        autoOpen: false,
        show: "fade",
        hide: "fade",
        modal: true,
        title: "Log in",
        height: "auto",
        open: function(event, ui)
        {
            $('#login_form').bind('submit', function(e)
            {
                var username = $('[name=username]').val();

                if (username === "")
                {
                    alert("Fill in your credentials!");
                    return false;
                }
            });
        }
    });

    $.post("./lib/dyn_content.php?jquery=jquery.login.php", {"func": "login_check"}, function(data)
    {
        if (data !== "")
        {
            logged_in = true;
            $(".login").text("Log out");
        }
        else
        {
            $(".login").text("Log in");
        }
    });

    $(".login").click(function()
    {
        if (logged_in === false)
        {
            $("#login_dialog").dialog("open");
        }
        else
        {
            $.post("./lib/dyn_content.php?jquery=jquery.login.php", {"func": "logout"}, function(data)
            {
                logged_in = false;
                $(".login").text("Log in");
                location.reload(true);
            });
        }
    });
});
