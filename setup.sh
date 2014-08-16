#!/bin/bash
#set -x

TOOLS="tools"
YUIVERSION="2.4.8"
YUI="yuicompressor-$YUIVERSION.jar"

# Files to be minified
CSS_FILES=(
    css/install.css
    css/main.css
)

JS_FILES=(
    js/auto_update.js
    js/board_settings.js
    js/component_settings.js
    js/cycle_settings.js
    js/group_settings.js
    js/login_handler.js
    js/message_board.js
    js/navigation.js
    js/page_scroll.js
    js/personal_settings.js
    js/phase_settings.js
    js/search.js
    js/settings_navigation.js
    js/statistics.js
    js/statistics_navigation.js
    js/ticket_handler.js
    js/ticket_move.js
    js/ticket_sort.js
    js/ticket_table.js
    js/user_settings.js
)

function command_exists {
    command -v $1 &> /dev/null
}

command_exists wget
IS_WGET=$?
if [ $IS_WGET -eq 1 ]; then
    echo "Couldn't find wget, aborting."
    exit 1;
fi

command_exists unzip
IS_UNZIP=$?
if [ $IS_UNZIP -eq 1 ]; then
    echo "Couldn't find unzip, aborting."
    exit 1;
fi

echo "Fetching 3rdparties..."
git submodule update --init --recursive

if [ ! -e dragsort-0.5.2.zip ]; then
    wget -O dragsort-0.5.2.zip "http://download-codeplex.sec.s-msft.com/Download/Release?ProjectName=dragsort&DownloadId=887234&FileTime=130517762092170000&Build=20928"
fi
unzip -n -d 3rdparty/jquery-dragsort dragsort-0.5.2.zip

if [ ! -e favicon.ico ]; then
    ln -s img/favicon.ico .
fi

# HINT
# git archive --remote=git://git.foo.com/project.git HEAD:path/to/directory filename
# http://stackoverflow.com/questions/1125476/git-retrieve-a-single-file-from-a-repository

#phplot
#http://sourceforge.net/projects/phplot/files/latest/download

#jqplot
#https://bitbucket.org/cleonello/jqplot/wiki/Home

#WGET

#js-packer, not needed ?

#PHPExcel
#https://phpexcel.codeplex.com/SourceControl/latest#

# Minify JS and CSS files using YUI compressor if Java is present.
# Otherwise just link the min versions to full versions.

command_exists java
IS_JAVA=$?
if [ $IS_JAVA -eq 0 ]; then
    # Get YUI compressor for minifying JS and CSS files

    if [ ! -f $TOOLS/$YUI ]; then
        wget --no-check-certificate -O $TOOLS/$YUI https://github.com/yui/yuicompressor/releases/download/v$YUIVERSION/$YUI
    fi

    if [ ${#CSS_FILES[@]} -gt 0 ]; then
        echo "Minifying CSS files..."
        java -jar $TOOLS/$YUI -o '.css$:.min.css' ${CSS_FILES[*]}
    fi
    if [ ${#JS_FILES[@]} -gt 0 ]; then
        echo "Minifying JS files..."
        java -jar $TOOLS/$YUI -o '.js$:.min.js' ${JS_FILES[*]}
    fi
else
    echo "No Java found, linking JS and CSS files..."
    for f in "${CSS_FILES[@]}"; do
        target=${f%.*}.min.${f##*.}
        if [ ! -e $target ]; then
            ln -sr $f $target
        fi
    done

    for f in "${JS_FILES[@]}"; do
        target=${f%.*}.min.${f##*.}
        if [ ! -e $target ]; then
            ln -sr $f $target
        fi
    done
fi

echo "Done."
