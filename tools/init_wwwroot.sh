#!/bin/bash
#set -x
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

wwwroot="/var/www/html"
haggard_dir="haggard"
hdir=$wwwroot/$haggard_dir
webuser="www-data"
this_dir=$(dirname $0)
repo="$this_dir/.."
misc="$this_dir/../misc"

CP="sudo cp -r --preserve=timestamps"

source $this_dir/minify.sh
source $this_dir/utils.sh

function print_info() {
    echo ""
    echo "${0##*/} script will initialize apache www root directory for Haggard"
    echo "it also copies original haggard board files into www root"
    echo "any new board will be symbolically linked to the haggard board parent"
    echo "assuming wwwroot path is $wwwroot"
}

function init_dir() {
# 1. param is directory name to be created
# 2. param, if set
#    the first parameter is used as filetype either js or css,
#    and the dir content is minified before move.
    d=$hdir/$1
    echo "Initializing $d"
    if [ ! -e $d ]; then
        sudo mkdir "$d"
    fi

    if [ ! -f $2 ]; then
        $CP $repo/$1/index.php $d
        minify $1 $repo/$1
        for i in $repo/$1/*.min.$1; do
            a=$(basename $i .min.$1);
            sudo mv $i $d/$a.$1;
        done
    else
        $CP $repo/$1/* $d
    fi
}

function init_root() {
    $CP $misc/wwwroot_index.php $wwwroot/index.php
    $CP $misc/board_index.php $hdir/index.php
    $CP $misc/404.php $hdir/
    $CP $misc/404.php $wwwroot/
    $CP $misc/maintenance.php $hdir/
    $CP $misc/maintenance.php $wwwroot/
    $CP $this_dir/manage.sh $wwwroot/
    sudo ln -sf $hdir/img/favicon.ico $hdir/
    sudo ln -sf $hdir/img/favicon.ico $wwwroot/
    sudo ln -sf $hdir/img/bg2.png $wwwroot/
}

function init_3rdparty() {
    d=$hdir/"3rdparty"
    echo "Initializing $d"
    if [ ! -e $d ]; then
        sudo mkdir "$d"
    fi
    #TODO copy only needed 3rdparty files to wwwroot
    echo "3rdparty folder not ready!"
}

print_info
continue_prompt
echo "Initializing $hdir"
if [ ! -e $hdir ]; then
    sudo mkdir "$hdir"
fi
init_dir "config"
init_dir "img"
init_dir "lib"
init_dir "css" 1
init_dir "js" 1
init_root

init_3rdparty

sudo chown -R $webuser:$webuser $wwwroot
