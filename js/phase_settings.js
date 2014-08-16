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

$(function()
{
    $("a", ".do_release").button().click(function() {
        $("#dialog").load("./lib/dyn_content.php?page=dialog.make_release.php", function()
        {
            $("#dialog").dialog(
                    {
                        autoOpen: false,
                        modal: true,
                        width: 370,
                        height: "auto",
                        title: "Make release",
                        open: function(event, ui)
                        {
                            $('#release_form').bind('submit', function(e)
                            {
                                e.preventDefault();
                                var name = $('[name=release_name]').val();
                                var phase = $('[name=phase]').val();
                                var action = $('[name=action]').val();

                                $.post("./lib/dyn_content.php?jquery=jquery.make_release.php", {
                                    "name": name,
                                    "phase": phase,
                                    "action": action}, function(data) {
                                    if (data != '')
                                    {
                                        window.location = "./lib/release_note.php?release=" + data;
                                        $("#dialog").dialog("close");
                                    }
                                    else {
                                        alert("There was problem with releasing");
                                    }
                                });
                            });
                        }
                    });

            $("#dialog").dialog("open");
        });
    });

    $("a", ".inactivate_phase").button().click(function() {
        var phase_id = $(this).data("id");
        $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "tickets_in_phase", "id": phase_id}, function(ret)
        {
            if (ret == 1)
            {
                $("#dialog").load("./lib/dyn_content.php?page=dialog.phase_tickets.php&id=" + phase_id, function()
                {
                    $("#dialog").dialog(
                            {
                                autoOpen: false,
                                modal: true,
                                width: 370,
                                height: "auto",
                                title: "Inactivate phase",
                                open: function(event, ui)
                                {
                                    $('#phase_form').bind('submit', function(e)
                                    {
                                        e.preventDefault();
                                        var act = $('[name=action]').val();

                                        if (act == "delete")
                                        {
                                            $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "delete_tickets_in_phase", "id": phase_id});
                                        }
                                        else
                                        {
                                            $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "move_tickets_in_phase", "id": phase_id, "to": act});
                                        }

                                        $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "inactivate_phase", "id": phase_id}, function()
                                        {
                                            $("#sub_content").load("./lib/dyn_content.php?page=page.phase_settings.php", function() {
                                                $("#dialog").dialog("close");
                                            });
                                        });

                                        return false;
                                    });
                                }
                            });

                    $("#dialog").dialog("open");
                });
            }
            else
            {
                $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "inactivate_phase", "id": phase_id}, function()
                {
                    $("#sub_content").load("./lib/dyn_content.php?page=page.phase_settings.php");
                });
            }
        });
    });

    $("a", ".activate_phase").button().click(function() {
        var phase_id = $(this).data("id");
        $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "activate_phase", "id": phase_id}, function()
        {
            $("#sub_content").load("./lib/dyn_content.php?page=page.phase_settings.php");
        });
    });

    $("a", ".edit_phase").button().click(function() {
        var phase_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Edit phase",
                    open: function(event, ui)
                    {
                        $('[name=wip_limit]').keydown(function(event) {
                            if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                                    (event.keyCode == 65 && event.ctrlKey === true) ||
                                    (event.keyCode >= 35 && event.keyCode <= 39))
                            {
                                return;
                            }
                            else
                            {
                                if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105))
                                {
                                    event.preventDefault();
                                }
                            }
                        });

                        $('[name=ticket_limit]').keydown(function(event) {
                            if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                                    (event.keyCode == 65 && event.ctrlKey === true) ||
                                    (event.keyCode >= 35 && event.keyCode <= 39))
                            {
                                return;
                            }
                            else
                            {
                                if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105))
                                {
                                    event.preventDefault();
                                }
                            }
                        });


                        $('#phase_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var name = $('[name=name]').val();
                            var wip_limit = $('[name=wip_limit]').val();
                            var ticket_limit = $('[name=ticket_limit]').val();
                            var help_text = $('[name=help_text]').val();
                            var forcec = $('[name=force_comment]').val();
                            var email = $('#phase_email_notifications').val() || [];
                            var notify_empty = $('[name=notify_empty]:checked').val();

                            if (name === "")
                            {
                                alert("Please fill name!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.phase_settings.php", {"func": "edit_phase", "name": name, "id": phase_id, "wip_limit": wip_limit, "ticket_limit": ticket_limit, "help_text": help_text, "force_comment": forcec, "notifications": email.join(","), "notify_empty": notify_empty}, function()
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.phase_settings.php", function() {
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });
                    }

                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.edit_phase.php&id=" + phase_id, function()
        {
            $("#dialog").dialog("open");
        });
    });
});


