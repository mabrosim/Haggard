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
    $('.new_message').click(function(e)
    {
        e.preventDefault();
        var topic = $('#topic_id').data('id');
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 400,
                    height: 'auto',
                    title: "New message",
                    open: function(event, ui)
                    {
                        $('#new_message').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var message = $('[name=message]').val();
                            $.post("./lib/dyn_content.php?jquery=jquery.new_message.php", {"message": message, "topic": topic}, function()
                            {
                                $("#content").load("./lib/dyn_content.php?page=page.topic_messages.php&id=" + topic, function() {
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.new_message.php", function()
        {
            $("#dialog").dialog("open");
        });
    });

    $('.new_message_topic').click(function(e)
    {
        e.preventDefault();
        $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 400,
                    height: 'auto',
                    title: "New message topic",
                    open: function(event, ui)
                    {
                        $('#new_topic').bind('submit', function(e)
                        {
                            e.preventDefault();
                            var name = $('[name=name]').val();
                            $.post("./lib/dyn_content.php?jquery=jquery.new_message_topic.php", {"name": name}, function()
                            {

                                $("#content").load(current_page, function() {
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });
                    }
                });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.new_message_topic.php", function()
        {
            $("#dialog").dialog("open");
        });
    });

    $('.topic_messages').click(function(e)
    {
        e.preventDefault();
        var topic = $(this).data('topic');

        $("#content").fadeTo('fast', 0, function()
        {
            $("#content").load("./lib/dyn_content.php?page=page.topic_messages.php&id=" + topic, function() {
                $("#content").fadeTo('fast', 1);
            });

        });

    });

    $('.back_to_topics').click(function(e)
    {
        e.preventDefault();
        $("#content").fadeTo('fast', 0, function()
        {
            $("#content").load("./lib/dyn_content.php?page=page.message_board.php", function() {
                $("#content").fadeTo('fast', 1);
            });

        });

    });
});
