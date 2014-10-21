#!/bin/bash
#set -x
#
# Copyright (c) 2014, Microsoft Mobile
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
# * Redistributions of source code must retain the above copyright notice, this
#   list of conditions and the following disclaimer.
#
# * Redistributions in binary form must reproduce the above copyright notice,
#   this list of conditions and the following disclaimer in the documentation
#   and/or other materials provided with the distribution.
#
# * Neither the name of the {organization} nor the names of its
#   contributors may be used to endorse or promote products derived from
#   this software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
# FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
# DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
# SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
# CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
# OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
# OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
#

source $(dirname $0)/utils.sh
source $(dirname $0)/minify.sh

thirdparty="$(dirname $0)/../3rdparty"
tmp="$thirdparty/~tmp"

CP="sudo cp -r --preserve=timestamps"

function wget_more_libs() {
    # this should be in sync with global.config.php listed libraries
    LIBS_URLS=(
        'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.js'
        'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css'
        'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.js'
        'http://code.jquery.com/jquery-migrate-1.2.1.js'
        'http://qtip2.com/v/2.2.0/basic/jquery.qtip.js'
        'http://qtip2.com/v/2.2.0/basic/jquery.qtip.css'
    )
    for l in "${LIBS_URLS[@]}"; do
        target=$1/$(basename $l);
        if [ ! -e $target ]; then
            wget -O $target $l;
        fi
    done
}

function init_3rdparty() {
    d=$WWWBASE/"3rdparty"
    echo "Initializing $d"
    sudo rm -fR "$d"
    if [ ! -e $d ]; then
        sudo mkdir -p "$d"
        sudo mkdir -p "$d/ezSQL/shared"
        sudo mkdir -p "$d/ezSQL/mysql"
        $CP "$thirdparty/ezSQL/shared/ez_sql_core.php" "$d/ezSQL/shared/"
        $CP "$thirdparty/ezSQL/mysql/ez_sql_mysql.php" "$d/ezSQL/mysql/"

        mkdir -p "$tmp"
        $CP "$thirdparty/jquery-cookie/src/jquery.cookie.js" $tmp
        $CP "$thirdparty/jquery-dragsort/jquery.dragsort-0.5.2.js" $tmp
        $CP "$thirdparty/jquery-livequery/jquery.livequery.js" $tmp
        $CP "$thirdparty/jquery-placeholder/jquery.placeholder.js" $tmp
        $CP "$thirdparty/jquery-farbtastic/src/farbtastic.js" $tmp
        $CP "$thirdparty/jquery-tablesorter/js/jquery.tablesorter.js" $tmp
        $CP "$thirdparty/jquery-farbtastic/src/farbtastic.js" $tmp
        $CP "$thirdparty/jquery-tablesorter/js/jquery.tablesorter.js" $tmp

        sudo mkdir -p $d/jqplot/plugins
        $CP "$thirdparty/jquery-plot/jquery.jqplot.min.js" $d/jqplot
        $CP "$thirdparty/jquery-plot/jquery.jqplot.min.css" $d/jqplot
        $CP "$thirdparty/jquery-plot/plugins/"*"min"* $d/jqplot/plugins

        wget_more_libs $tmp

        minify "js" $tmp
        for i in $tmp/*.min.js; do
            a=$(basename $i .min.js);
            sudo mv $i $d/$a.js;
        done

        minify "css" $tmp
        for i in $tmp/*.min.css; do
            a=$(basename $i .min.css);
            sudo mv $i $d/$a.css;
        done
    fi
    sudo chown -R $WEBUSER:$WEBUSER $WWWHAGGARD
}

echo "3rdparty: DONE!"
