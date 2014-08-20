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
    d=$hdir/$1
    echo "Initializing $d"
    if [ ! -e $d ]; then
        sudo mkdir "$d"
    fi

    if [ ! -f $2 ]; then
        minify $1 $repo/$1
        sudo cp $repo/$1/index.php $d
        sudo mv $repo/$1/*$2* $d
    else
        sudo cp -R $repo/$1/* $d
    fi
}

function init_root() {
    sudo cp -f $misc/wwwroot_index.php $wwwroot/index.php
    sudo cp -f $misc/board_index.php $hdir/index.php
    sudo cp -f $misc/404.php $hdir/
    sudo cp -f $misc/404.php $wwwroot/
    sudo cp -f $misc/maintenance.php $hdir/
    sudo cp -f $misc/maintenance.php $wwwroot/
    sudo cp -f $this_dir/manage.sh $wwwroot/
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
init_dir "css" ".min."
init_dir "js" ".min."
init_root

init_3rdparty

sudo chown -R $webuser:$webuser $wwwroot
