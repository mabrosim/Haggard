#!/bin/bash
#set -x

source $(dirname $0)/utils.sh
source $(dirname $0)/minify.sh

thirdparty="$(dirname $0)/../3rdparty"

CP="sudo cp -r --preserve=timestamps"

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

        tmp="$thirdparty/~tmp/"
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
