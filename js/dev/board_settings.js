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
    var confirm_save = false;

    $("a", ".remove_archived_tickets").button().click(function() {
        var answer = confirm("Are you sure you want to delete ALL archived tickets? This cannot be undone.");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "delete_archived_tickets"});
        }
    });

    $("a", ".delete_all_data").button().click(function() {
        var answer = confirm("Are you sure you want to delete ALL board data? This cannot be undone.");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "delete_all_data"}, function() {
                alert("All data has been erased! Please remove the folder from the server!");
                window.location = "https://haggard";
            });
        }

    });

    $("a", ".remove_all_tickets").button().click(function() {
        var answer = confirm("Are you sure you want to delete ALL tickets? This cannot be undone.");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "delete_all_tickets"});
        }

    });

    $("a", ".mass_archive").button().click(function() {
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: 'auto',
                    title: "Mass archive tickets",
                    open: function(event, ui)
                    {
                        $("#older").datepicker({
                            changeMonth: true,
                            numberOfMonths: 1,
                            dateFormat: "dd.mm.yy",
                            firstDay: 1,
                            maxDate: "+0d"
                        });

                        $('#mass_archive_form').bind('submit', function(e)
                        {
                            var phase_name = $('[name=phase] option:selected').text();
                            var phase_id = $('[name=phase]').val();
                            var older = $('[name=older]').val();
                            if (older == "" || older == null)
                            {
                                alert("Set the time range!");
                                return false;
                            }

                            var answer = confirm("Are you sure to archive all tickets from phase " + phase_name + " ?");
                            if (answer)
                            {
                                $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "mass_archive", "phase": phase_id, "older": older});
                            }
                        });

                        return false;
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.mass_archive_tickets.php", function()
        {
            $("#dialog").dialog("open");
        });

    });

    $("a", ".auto_archive").button().click(function() {
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: 'auto',
                    title: "Auto archive tickets",
                    open: function(event, ui)
                    {
                        $('#enabled').change(function(e)
                        {
                            if ($(this).is(':checked'))
                            {
                                $('.disable').removeAttr('disabled');
                            }
                            else
                            {
                                $('.disable').attr('disabled', true);
                            }
                        });

                        $('#auto_archive_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var enabled = 0;
                            if ($('[name=enabled]').attr('checked'))
                                enabled = 1;

                            var threshold = $('[name=last_update]').val();
                            var phases = "";
                            var slice = false;

                            $('.phase_select').each(function(i)
                            {
                                if ($(this).attr('checked'))
                                {
                                    phases += $(this).val() + ",";
                                    slice = true;
                                }
                            });

                            if (slice == true)
                            {
                                phases = phases.slice(0, -1);
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "auto_archive", "enabled": enabled, "threshold": threshold, "phases": phases}, function(data)
                            {
                                $("#dialog").dialog("close");
                            });
                        });

                        return false;
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.auto_archive_tickets.php", function()
        {
            $("#dialog").dialog("open");
        });

    });

    $("a", ".import_data").button().click(function() {
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: 'auto',
                    title: "Import data from old board",
                    open: function(event, ui)
                    {
                        $('#import_data_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var host = $('[name=host]').val();
                            var username = $('[name=username]').val();
                            var password = $('[name=password]').val();
                            var name = $('[name=name]').val();

                            $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "import_data", "host": host, "username": username, "password": password, "name": name}, function(ret)
                            {
                                alert(ret);
                                $("#dialog").dialog("close");
                            });
                        });

                        return false;
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.import_data.php", function()
        {
            $("#dialog").dialog("open");
        });

    });


    $('[name=use_cycles]').change(function() {
        if ($('[name=use_cycles]:checked').val() != 'use_cycles')
        {
            alert("CAUTION: This will erase all cycles except the current one. Continue with save button");
            confirm_save = true;
        }
    });

    $('#color_change').bind('submit', function(e)
    {
        e.preventDefault();
        $('#dialog').dialog('close');
    });

    $('.use_priorities').change(function()
    {
        $('.ticket_color_change').toggle();
    });

    $('#personal_settings').bind('submit', function(e)
    {
        e.preventDefault();
        var answer = 1;
        if (confirm_save == true)
        {
            var answer = confirm("You have changed something that may cause lose of information. Are you sure about these settings?");
        }

        if (answer)
        {
            var board_team = $('[name=board_team]').val();
            var board_team_email = $('[name=board_team_email]').val();
            var board_url = $('[name=board_url]').val();
            var use_wip = $('[name=use_wip]:checked').val();
            var use_cycles = $('[name=use_cycles]:checked').val();
            var use_linking = $('[name=use_linking]:checked').val();
            var use_statistics = $('[name=use_statistics]:checked').val();
            var use_logging = $('[name=use_logging]:checked').val();
            var use_priorities = $('[name=use_priorities]:checked').val();
            var show_ticket_help = $('[name=show_ticket_help]:checked').val();
            var use_firstname = $('[name=use_firstname]:checked').val();
            var private_board = $('[name=private_board]:checked').val();
            var send_email = $('[name=send_email]:checked').val();

            var color1 = $('[name=color1]').val();
            var color2 = $('[name=color2]').val();
            var color3 = $('[name=color3]').val();
            var color4 = $('[name=color4]').val();

            var ticket_type1 = $('[name=ticket_type1]').val();
            var ticket_type2 = $('[name=ticket_type2]').val();
            var ticket_type3 = $('[name=ticket_type3]').val();
            var ticket_type4 = $('[name=ticket_type4]').val();

            $.post("./lib/dyn_content.php?jquery=jquery.board_settings.php", {"func": "board_settings",
                "board_team": board_team,
                "board_team_email": board_team_email,
                "private_board": private_board,
                "send_email": send_email,
                "use_wip": use_wip,
                "use_cycles": use_cycles,
                "use_linking": use_linking,
                "use_statistics": use_statistics,
                "use_logging": use_logging,
                "use_priorities": use_priorities,
                "use_firstname": use_firstname,
                "color1": color1,
                "color2": color2,
                "color3": color3,
                "color4": color4,
                "ticket_type1": ticket_type1,
                "ticket_type2": ticket_type2,
                "ticket_type3": ticket_type3,
                "ticket_type4": ticket_type4,
                "show_ticket_help": show_ticket_help,
                "board_url": board_url}, function(data)
            {
                $('#saveSettings').show().delay(5000).fadeOut();
            });

        }

        return false;
    });

    $('#color_change').bind('submit', function(e)
    {
        e.preventDefault();
        $('#dialog').dialog('close');
    });

    $('.color_pick').unbind();
    $('.color_pick').click(function()
    {
        var color_id = $(this).data('colorid');
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: 'auto',
                    title: "Change color for ticket type " + color_id
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.change_ticket_color.php", function()
        {
            $("#dialog").dialog("open");
            $.farbtastic($('#colorpicker'), function(color)
            {
                $(".color" + color_id).css("background-color", color);
                $("[name=color" + color_id + "]").val(color);
            });

            $.farbtastic($('#colorpicker')).setColor($("[name=color" + color_id + "]").val());
        });
    });

    $("a", ".enable_guest_account").button().livequery('click', function() {
        $.post("./lib/dyn_content.php?jquery=jquery.guest_account.php", {"func": "enable"}, function(data) {
            $("#guest_password").text("Password is " + data + "   ").show();
            $(".enable_guest_account .ui-button-text").text("Disable");
            $(".enable_guest_account").removeClass("enable_guest_account").addClass("disable_guest_account");
        });
    });

    $("a", ".disable_guest_account").button().livequery('click', function() {
        $.post("./lib/dyn_content.php?jquery=jquery.guest_account.php", {"func": "disable"}, function(data) {
            $("#guest_password").hide();
            $(".disable_guest_account .ui-button-text").text("Enable");
            $(".disable_guest_account").removeClass("disable_guest_account").addClass("enable_guest_account");
        });
    });

});
