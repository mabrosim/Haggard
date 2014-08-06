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
    $("a", ".remove_user").button().click(function() {
        var user_id = $(this).data("id");
        var answer = confirm("Are you sure you want to remove this user? All tickets and related info will be destroyed! This cannot be undone.");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.user_settings.php", {"func": "remove_user", "id": user_id}, function()
            {
                $("#sub_content").load("./lib/dyn_content.php?page=page.user_settings.php");
            });
        }
    });

    $("a", ".permission_user").button().click(function() {
        var user_id = $(this).data("id");
        $("#dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 370,
            height: "auto",
            title: "User permissions",
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
                    $.post("./lib/dyn_content.php?jquery=jquery.user_settings.php", {"func": "user_permissions", "id": user_id, "permissions": permissions}, function(dada)
                    {
                        $("#sub_content").load("./lib/dyn_content.php?page=page.user_settings.php", function() {
                            $("#dialog").dialog("close");
                        });
                    });

                    return false;
                });
            }
        });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.user_permission.php&id=" + user_id, function()
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

    $("button", ".add_user").button().click(function() {
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "New user",
                    open: function(event, ui)
                    {
                        $('#name').autocomplete({
                            source: './lib/dyn_content.php?jquery=jquery.search_user.php',
                            minLength: 2,
                            select: {}
                        });

                        $('#user_form').bind('submit', function()
                        {
                            var nam = $('[name=name]').val();

                            if (nam === "")
                            {
                                alert("Please fill name or email!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.user_settings.php", {"func": "new_user", "mail": nam}, function(dada)
                            {
                                if (dada === "true")
                                {
                                    $("#sub_content").load("./lib/dyn_content.php?page=page.user_settings.php", function() {
                                        $("#dialog").dialog("close");
                                    });
                                }
                                else
                                {
                                    alert(dada);
                                }
                            });

                            return false;
                        });
                    }

                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.add_user.php", function()
        {
            $("#dialog").dialog("open");
        });
    });
});
