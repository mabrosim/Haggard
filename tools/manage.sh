#!/bin/bash
#
# Copyright (c) 2013-2014, Microsoft Mobile
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

root="/var/www/html"
haggard="haggard"
configDir="config"
configFile="board.config.php"
libDir="lib"

excludes=($configDir $libDir $configFile $haggard .. . doc .git .gitignore tools TODO LICENSE README.md)

print_logo() {
    clear
    echo "  _   _                                       _  "
    echo " | | | |                                     | | "
    echo " | |_| |  __ _   __ _   __ _   __ _  _ __  __| | "
    echo " |  _  | / _\` | / _\` | / _\` | / _\` || '__|/ _\` | "
    echo " | | | || (_| || (_| || (_| || (_| || |  | (_| | "
    echo " \_| |_/ \__,_| \__, | \__, | \__,_||_|   \__,_| "
    echo "                 __/ |  __/ |                    "
    echo "                |___/  |___/                     "
    echo "                                                 "
    echo "            Board management script              "
    echo "-------------------------------------------------"
}

update_board() {
    if [ -z $1 ]
    then
        echo "Error: no board name given"
        exit 1
    else
        local board=$1
    fi

    if [ -z $2 ]
    then
        local s=$root/$haggard
        local t=$root/$board
    else
        local s=$root/$haggard/$2
        local t=$root/$board/$2
    fi

    if [ ! -d $t ]
    then
        if [ -z $2 ]; then echo "Installing board: $board"; fi
        echo "Creating directory: $t"
        mkdir "$t"
    else
        if [ -z $2 ]; then echo "Updating board: $board"; fi
    fi

    local skip=0

    # create new links
    FILES=$(ls -a $s)

    for f in $FILES
    do
        skip=0

        for i in ${excludes[@]}
        do
            if [ $f == $i ]
            then
                echo "Skipping: $s/$i"
                skip=1
                break
            fi
        done

        if [ $skip == 1 ]
        then
            continue
        fi

        if [ ! -e $t/$f ]
        then
            echo "Linking: $s/$f"
            ln -s $s/$f $t/$f
        fi
    done

    # remove broken links
    FILES=$(ls -a $t)

    for f in $FILES
    do
        if [ ! -e $t/$f ]
        then
            echo "Removing link: $t/$f"
            rm $t/$f
        fi
    done
}

print_logo

while true
do
    read -n 1 -p "Do you want to update $haggard repository? [yn] " yn
    echo ''
    case $yn in
        [Yy])
            echo "* Updating *"
            cd "$root/$haggard"
            sudo git pull
            sudo chown -R root:root $root/$haggard
            break
            ;;
        [Nn])
            break
            ;;
    esac
done

if [ -z $@ ]
then
    while true
    do
        read -n 1 -p "Do you want to update all board links? [yn] " yn
        echo ''
        case $yn in
            [Yy])
                break
                ;;
            [Nn])
                echo 'Exiting...'
                exit 1
                ;;
        esac
    done

    DIRS=$(find $root -maxdepth 1 -type d -printf "%f\n")
else
    DIRS=$@
fi

cd "$root"

for d in $DIRS
do
    skip=0

    for i in ${excludes[@]}
    do
        if [ $d == $i ] || [ -L $i ]
        then
            echo "Skipping: $root/$d"
            skip=1
            break
        fi
    done

    if [ $skip == 1 ]
    then
        continue
    fi

    update_board $d
    update_board $d $configDir
    update_board $d $libDir
    sudo chown apache:apache $root/$d/$configDir
done

echo 'All done!'
exit 0
