#!/bin/bash
#set -x

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
    d=$HDIR/"3rdparty"
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
        $CP "$thirdparty/jquery-plot/jquery.jqplot.js" $tmp

        $CP "$thirdparty/jquery-farbtastic/src/farbtastic.js" $tmp
        $CP "$thirdparty/jquery-tablesorter/js/jquery.tablesorter.js" $tmp
        $CP "$thirdparty/jquery-plot/jquery.jqplot.css" $tmp

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
    sudo chown -R $WEBUSER:$WEBUSER $WWWROOT
}

echo "3rdparty: DONE!"

