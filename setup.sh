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

source tools/utils.sh

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
    wget -O dragsort-0.5.2.zip "http://download-codeplex.sec.s-msft.com/Download/Release?ProjectName=dragsort&DownloadId=887234&FileTime=130517762092170000&Build=20959"
fi
unzip -n -d 3rdparty/jquery-dragsort dragsort-0.5.2.zip

# Get YUI compressor for minifying JS and CSS files
if [ ! -f ./tools/$YUI ]; then
    wget --no-check-certificate -O ./tools/$YUI https://github.com/yui/yuicompressor/releases/download/v$YUIVERSION/$YUI
fi

sudo ./tools/init_wwwroot.sh

echo "Done!"
