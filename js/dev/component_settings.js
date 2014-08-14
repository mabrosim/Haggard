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
    /* Component handling */
    $("a", ".delete_component").button().click(function() {
        var component_id = $(this).data("id");
        var answer = confirm("Are you sure you want to delete this component? This cannot be undone");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.component_settings.php", {"func": "delete_component", "id": component_id}, function()
            {
                $("#sub_content").load("./lib/dyn_content.php?page=page.component_settings.php");
            });
        }
    });

    $("a", ".edit_component").button().click(function() {
        var component_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Edit component",
                    open: function(event, ui)
                    {
                        $('#component_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var title = $('[name=title]').val();

                            if (title === "")
                            {
                                alert("Please fill title!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.component_settings.php", {"func": "edit_component", "title": title, "id": component_id}, function(e)
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.component_settings.js", function() {
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });
                    }

                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.edit_component.php&id=" + component_id, function()
        {
            $("#dialog").dialog("open");
        });

    });

    $("button", ".add_component").button().click(function() {
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "New component",
                    open: function(event, ui)
                    {
                        $('#component_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var title = $('[name=title]').val();

                            if (title === "")
                            {
                                alert("Please fill title!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.component_settings.php", {"func": "new_component", "title": title}, function()
                            {
                                $("#sub_content").load("./lib/dyn_content.php?page=page.component_settings.php", function() {
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });
                    }

                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.new_component.php", function()
        {
            $("#dialog").dialog("open");
        });

    });
});

