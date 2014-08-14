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
    /* Cycle handling */
    $("a", ".done_cycle").button().click(function() {
        var cycle_id = $(this).data("id");
        var answer = confirm("Are you sure this cycle is done? This cannot be undone.");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.cycle_settings.php", {"func": "done_cycle", "id": cycle_id}, function()
            {
                $("#sub_content").load("./lib/dyn_content.php?page=page.cycle_settings.php");
            });
        }
    });

    /* Cycle handling */
    $("a", ".delete_cycle").button().click(function() {
        var cycle_id = $(this).data("id");
        var answer = confirm("Are you sure you want to delete this cycle? This cannot be undone.");

        if (answer)
        {
            $.post("./lib/dyn_content.php?jquery=jquery.cycle_settings.php", {"func": "delete_cycle", "id": cycle_id}, function()
            {
                $("#sub_content").load("./lib/dyn_content.php?page=page.cycle_settings.php");
            });
        }
    });

    $("a", ".edit_cycle").button().click(function() {
        var cycle_id = $(this).data("id");
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: "auto",
                    title: "Edit cycle",
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

                        $('#cycle_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var name = $('[name=name]').val();
                            var wip_limit = $('[name=wip_limit]').val();
                            var s_date = $('[name=start_date]').val();
                            var e_date = $('[name=end_date]').val();

                            if (name == "" || wip_limit == "" || s_date == "" || e_date == "")
                            {
                                alert("Please fill in all inputs!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.cycle_settings.php", {"func": "edit_cycle", "name": name, "wip_limit": wip_limit,
                                "s_date": s_date, "e_date": e_date, "id": cycle_id}, function(ret)
                            {
                                if (ret == "" || /^\s*$/.test(ret))
                                {
                                    $("#sub_content").load("./lib/dyn_content.php?page=page.cycle_settings.php", function()
                                    {
                                        $("#dialog").dialog("close");
                                    });
                                }
                                else
                                {
                                    alert(ret);
                                    return false;
                                }
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.edit_cycle.php&id=" + cycle_id, function()
        {
            $('#start_date, #end_date').datepicker({
                dateFormat: 'dd.mm.yy'
            });

            $("#dialog").dialog("open");
        });
    });

    $("button", ".add_cycle").button().click(function() {

        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 400,
                    height: 500,
                    title: "Add cycle",
                    open: function(event, ui)
                    {
                        $('[name=wip]').keydown(function(event) {
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

                        $('#cycle_form').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var name = $('[name=name]').val();
                            var wip_person = $('[name=wip]').val();
                            var s_date = $('[name=date]').val();
                            var weeks = $('#cycle_length').val();
                            var wip_total = $("#total_wip").text();

                            if (name == "" || wip_person == "" || s_date == "")
                            {
                                alert("Please fill in name, wip/person and start date!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.cycle_settings.php", {"func": "add_cycle", "name": name, "wip_person": wip_person,
                                "s_date": s_date, "weeks": weeks, "wip_total": wip_total}, function(ret)
                            {
                                if (ret == "" || /^\s*$/.test(ret) || ret.length === 0 || !ret)
                                {
                                    $("#sub_content").load("./lib/dyn_content.php?page=page.cycle_settings.php", function()
                                    {
                                        $("#dialog").dialog("close");
                                    });
                                }
                                else
                                {
                                    alert(ret);
                                    return false;
                                }
                            });

                            return false;
                        });
                    }
                });

        function updateWIPP(wip)
        {
            var total = 0;
            $(".slider").each(function(index, elem)
            {
                var wip_amount = Math.round(wip * $(elem).data("value") / 100);
                $("#wip_amount" + $(elem).data("id")).html(wip_amount);
                total += wip_amount;
            });

            $("#total_wip").html(total);
        }

        $("#dialog").load("./lib/dyn_content.php?page=dialog.add_cycle.php", function()
        {
            $('#date').datepicker({
                dateFormat: 'dd.mm.yy'
            });

            $('input[name=wip]').change(function() {
                updateWIPP($('input[name=wip]').val());
            });

            $(".slider").slider({
                range: "min",
                min: 0,
                max: 100,
                animate: true,
                slide: function(event, ui)
                {
                    $("#amount" + $(this).data("id")).html(ui.value + "%");
                    $(this).data("value", ui.value);
                    updateWIPP($('input[name=wip]').val());
                },
                create: function(event, ui)
                {
                    $(this).slider("option", "value", $(this).data("value"));
                }

            });

            $("#dialog").dialog("open");
        });
    });
});
