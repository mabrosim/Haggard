<?php

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

$args = filter_input_array(INPUT_SERVER);

//TODO deny access via .htaccess config
if ($args["REQUEST_URI"] == "/haggard/") {
    include 'page.404.php';
    return;
}

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;

echo '<html xmlns="http://www.w3.org/1999/xhtml">' . PHP_EOL;

echo '<head>' . PHP_EOL;

echo '<title>Haggard Agile Board installation</title>' . PHP_EOL;
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>' . PHP_EOL;

echo '<link rel="stylesheet" href="./css/install.css" type="text/css">' . PHP_EOL;
echo '<link rel="stylesheet" href="./css/main.css" type="text/css">' . PHP_EOL;

echo '<link rel="stylesheet" href="' . $GLOBALS['JQUERY_UI_CSS'] . '" type="text/css">' . PHP_EOL;
echo '</head>';

echo '<body>';

echo '<div id="install">';
echo '<h1>Installation</h1>';

echo '<div id="install_form">';
echo '<h2>Welcome to Haggard Agile Board!</h2>';

if (isset($GLOBALS['install_error'])) {
    echo '<p style="color: red">Error: ' . $GLOBALS['install_error'] . '</p>';
}
echo '<p>With this installation script you can create easily your own board!</p>';
echo '<p>Preconditions:</p>';
echo '<ul>';
echo '<li>Correct configuration in config/database.config.php</li>';
echo '<li>Web server has write rights in config folder</li>';
echo '<li>Haggard database structure in place</li>';
echo '</ul>';

echo '<h2>Configuration</h2>';
echo '<form id="installation" action="index.php" method="post">';

echo '<div id="control">';
echo '<label>Board name </label>';
echo '<input type="text" name="name" id="name"/>';
echo '</div>';

$prefix = str_replace("/", "", $args["REQUEST_URI"]);
echo '<div id="control">';
echo '<label>Board email </label>';
echo '<input type="text" name="email" id="email" value="' . $prefix . '@' . $args["SERVER_NAME"] . '"/>';
echo '</div>';

echo '<div id="control">';
echo '<label>Board URL </label>';
echo '<input type="text" name="url" id="url" value="http://' . $args["SERVER_NAME"] . $args["REQUEST_URI"] . '"/>';
echo '</div>';

echo '<div id="control">';
echo '<label>Board timezone </label>';
echo '<select name="timezone" id="timezone" style="background-color: white;">';
$timezones = DateTimeZone::listAbbreviations();

$cities = array();
foreach ($timezones as $key => $zones) {
    foreach ($zones as $id => $zone) {
        if ($zone['timezone_id'] == "Europe/Helsinki") {
            echo '<option selected value="' . $zone['timezone_id'] . '">' . $zone['timezone_id'] . '</option>';
        } else {
            echo '<option value="' . $zone['timezone_id'] . '">' . $zone['timezone_id'] . '</option>';
        }
    }
}
echo '</select>';
echo '</div>';

echo '<p style="text-align: center;"><input class="form_button" type="submit" value="Create"><input type="reset" value="Reset" class="form_button"></p>';
echo '</form>';
echo '</div>';
echo '</div>';

require_once './lib/views/footer.php';

echo '<script type="text/javascript" src="' . $GLOBALS['JQUERY_JS'] . '"></script>' . PHP_EOL;
echo '<script type="text/javascript" src="' . $GLOBALS['JQUERY_UI_JS'] . '" charset="UTF-8"></script>' . PHP_EOL;
echo '<script type="text/javascript" src="./js/navigation.js" charset="UTF-8"></script>' . PHP_EOL;

echo '</body>';
echo '</html>';
