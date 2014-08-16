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
    $("button", ".create_group").button().click(function() {
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Create user group",
                    open: function(event, ui)
                    {
                        $('#group_form').submit(function()
                        {
                            var nam = $('[name=name]').val();
                            var desc = $('[name=desc]').val();

                            if (nam === "" || desc === "")
                            {
                                alert("Fill in name and description!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.user_group_settings.php", {"func": "create_group", "name": nam, "desc": desc}, function()
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.user_group_settings.php", function() {
                                    $("#dialog").dialog("close");
                                    return false;
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.create_group.php", function()
        {
            $("#dialog").dialog("open");
        });
    });

    $("a", ".permission_group").button().click(function() {
        var group_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "User group permissions",
                    open: function(event, ui)
                    {
                        $('#permission_form').bind('submit', function()
                        {
                            var checked = [];
                            $("input[name='permission[]']:checked").each(function()
                            {
                                checked.push($(this).val());
                            });
                            var permissions = checked.join();
                            $.post("./lib/dyn_content.php?jquery=jquery.user_group_settings.php", {"func": "group_permissions", "id": group_id, "permissions": permissions}, function(dada)
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.user_group_settings.php", function() {
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.group_permission.php&id=" + group_id, function()
        {
            $("#dialog").dialog("open");

            if ($('.all_permissions').is(':checked'))
            {
                $("input[name='permission[]']").each(function()
                {
                    if ($(this).attr("class") !== "all_permissions")
                        $(this).attr("disabled", true);
                });
            }

            $(".all_permissions").change(function()
            {
                if ($(this).is(':checked'))
                {
                    $("input[name='permission[]']").each(function()
                    {
                        if ($(this).attr("class") !== "all_permissions")
                            $(this).attr("disabled", true);
                    });
                }
                else
                {
                    $("input[name='permission[]']").each(function()
                    {
                        $(this).removeAttr("disabled");
                    });
                }
            });

        });

    });


    $("a", ".delete_group").button().click(function() {
        var group_id = $(this).data("id");
        var answer = confirm("Are you sure you want to delete this group?");
        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.user_group_settings.php", {"func": "delete_group", "id": group_id}, function()
            {
                $("#sub_content").load("./lib/dyn_content.php?page=page.user_group_settings.php", function() {
                    $("#dialog").dialog("close");
                    return false;
                });

            });
        }
    });

    $("a", ".edit_group").button().click(function() {
        var group_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Edit user group",
                    open: function(event, ui)
                    {
                        $('#group_form').submit(function()
                        {
                            var nam = $('[name=name]').val();
                            var desc = $('[name=desc]').val();

                            $.post("./lib/dyn_content.php?jquery=jquery.user_group_settings.php", {"func": "edit_group", "name": nam, "id": group_id, "desc": desc}, function()
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.user_group_settings.php", function() {
                                    $("#dialog").dialog("close");
                                    return false;
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.edit_group.php&id=" + group_id, function()
        {
            $("#dialog").dialog("open");
        });
    });

    $("a", ".remove_members_from_group").button().click(function() {
        var group_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Remove user from group",
                    open: function(event, ui)
                    {
                        $('#person_form').submit(function()
                        {
                            var pid = $('[name=person]').val();

                            $.post("./lib/dyn_content.php?jquery=jquery.user_group_settings.php", {"func": "remove_from_group", "gid": group_id, "pid": pid}, function()
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.user_group_settings.php", function() {
                                    $("#dialog").dialog("close");
                                    return false;
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.remove_member_from_group.php&gid=" + group_id, function()
        {
            $("#dialog").dialog("open");
        });
    });


    $("a", ".add_members_to_group").button().click(function() {
        var group_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Add users to group",
                    open: function(event, ui)
                    {
                        $('#person_form').submit(function()
                        {
                            var pid = $('[name=person]').val();

                            $.post("./lib/dyn_content.php?jquery=jquery.user_group_settings.php", {"func": "add_to_group", "gid": group_id, "pid": pid}, function(dada)
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.user_group_settings.php", function() {
                                    $("#dialog").dialog("close");
                                    return false;
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.add_member_to_group.php", function()
        {
            $("#dialog").dialog("open");
        });
    });
});

