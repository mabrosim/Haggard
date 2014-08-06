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

function padStr(i)
{
    return (i < 10) ? "0" + i : "" + i;
}

$(document).ready(function()
{
    var tmp = new Date();
    var today = padStr(tmp.getFullYear()) + "-" + padStr(tmp.getMonth() + 1) + "-" + padStr(tmp.getDate());

    tmp.setDate(tmp.getDate() - 10);
    var tenday = padStr(tmp.getFullYear()) + "-" + padStr(tmp.getMonth() + 1) + "-" + padStr(tmp.getDate());

    var ajaxDataRenderer = function(url, plot, options)
    {
        var ret = null;
        $.ajax({
            async: false,
            url: url,
            dataType: "json",
            success: function(data)
            {
                plot.legend.labels = data.labels;
                ret = data.data;
            }
        });

        return ret;
    };

    if ($('#board_activity_plot').get(0)) {
        var jsonurl = './lib/dyn_content.php?jquery=jquery.board_activity_plot.php';
        var plot = $.jqplot('board_activity_plot', jsonurl, {
            title: "Board activity",
            animate: true,
            dataRenderer: ajaxDataRenderer,
            series: [
                {
                    pointLabels: {show: true},
                    renderer: $.jqplot.BarRenderer,
                    rendererOptions: {
                        barWidth: 20,
                        barPadding: -20,
                        barMargin: 0
                    }
                },
                {
                }
            ],
            axes:
                    {
                        xaxis:
                                {
                                    renderer: $.jqplot.DateAxisRenderer,
                                    label: 'Date',
                                    tickOptions: {
                                        angle: -90,
                                        formatString: '%b %#d'
                                    },
                                    tickInterval: '1 day',
                                    min: tenday,
                                    max: today
                                },
                        yaxis:
                                {
                                    min: 0,
                                    tickInterval: 5,
                                    label: 'Activity',
                                    tickSpacing: 150
                                }

                    },
            highlighter: {
                show: true,
                showLabel: true,
                sizeAdjust: 7.5,
                bringSeriesToFront: false
            },
            cursor: {
                zoom: true,
                looseZoom: true,
                show: true,
                showTooltip: false
            },
            legend: {
                show: true,
                location: 'ne',
                placement: 'outsideGrid'
            }
        });
    }

    if ($('#phase_history_plot').get(0)) {
        var jsonurl = './lib/dyn_content.php?jquery=jquery.phase_ticket_plot.php';
        var plot = $.jqplot('phase_history_plot', jsonurl, {
            title: "Phase history",
            animate: true,
            dataRenderer: ajaxDataRenderer,
            axes:
                    {
                        xaxis:
                                {
                                    renderer: $.jqplot.DateAxisRenderer,
                                    label: 'Date',
                                    tickOptions: {
                                        angle: -90,
                                        formatString: '%b %#d'
                                    },
                                    tickInterval: '1 day',
                                    min: tenday,
                                    max: today
                                },
                        yaxis:
                                {
                                    min: 0,
                                    tickInterval: 1,
                                    label: 'Number of tickets',
                                    tickSpacing: 150
                                }

                    },
            highlighter: {
                show: true,
                showLabel: true,
                sizeAdjust: 7.5,
                bringSeriesToFront: true
            },
            cursor: {
                zoom: true,
                looseZoom: true,
                show: true,
                showTooltip: false
            },
            legend: {
                show: true,
                location: 'ne',
                placement: 'outsideGrid'
            }
        });
    }

    if ($('#user_history_plot').get(0)) {
        var jsonurl = './lib/dyn_content.php?jquery=jquery.user_ticket_plot.php';
        var plot = $.jqplot('user_history_plot', jsonurl, {
            title: "User history",
            animate: true,
            dataRenderer: ajaxDataRenderer,
            axes:
                    {
                        xaxis:
                                {
                                    renderer: $.jqplot.DateAxisRenderer,
                                    label: 'Date',
                                    tickOptions: {
                                        angle: -90,
                                        formatString: '%b %#d'
                                    },
                                    tickInterval: '1 day',
                                    min: tenday,
                                    max: today
                                },
                        yaxis:
                                {
                                    min: 0,
                                    tickInterval: 1,
                                    label: 'Number of tickets',
                                    tickSpacing: 150
                                }

                    },
            highlighter: {
                show: true,
                showLabel: true,
                sizeAdjust: 7.5,
                bringSeriesToFront: true
            },
            cursor: {
                zoom: true,
                looseZoom: true,
                show: true,
                showTooltip: false
            },
            legend: {
                show: true,
                location: 'ne',
                placement: 'outsideGrid'
            }
        });
    }
    $(".export_deleted").click(function(e)
    {
        e.preventDefault();

        window.open("./lib/xls_export.php?type=deleted", "_newtab");
    });

    $(".export_current").click(function(e)
    {
        e.preventDefault();

        window.open("./lib/xls_export.php?type=current", "_newtab");
    });

    $(".export_archived").click(function(e)
    {
        e.preventDefault();

        window.open("./lib/xls_export.php?type=archived", "_newtab");
    });

    $(".export_all").click(function(e)
    {
        e.preventDefault();
        var from = $("#from").val();
        var to = $("#to").val();

        window.open("./lib/xls_export.php?type=history&from=" + from + "&to=" + to, "_newtab");
    });

    var dates = $("#from, #to").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "dd.mm.yy",
        firstDay: 1,
        maxDate: "+0d",
        onSelect: function(selectedDate) {
            var option = this.id == "from" ? "minDate" : "maxDate",
                    instance = $(this).data("datepicker"),
                    date = $.datepicker.parseDate(
                            instance.settings.dateFormat ||
                            $.datepicker._defaults.dateFormat,
                            selectedDate, instance.settings);
            dates.not(this).datepicker("option", option, date);

            $("#ticket_changes").slideUp(100);
            $.post("./lib/dyn_content.php?jquery=jquery.ticket_history.php", {"func": "get_ticket_changes", "start": $("#from").val(), "stop": $("#to").val()}, function(data)
            {
                $("#ticket_changes").html(data);
                $("#ticket_changes").slideDown(100);

                $("#tickets").tablesorter();
            });
        }
    });

    $("#tickets").tablesorter();
    $("#current_tickets").tablesorter();
    $("#archived_tickets").tablesorter();
    $("#deleted_tickets").tablesorter();
});
