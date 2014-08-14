
echo "Fetching 3rdparties..."

git submodule init
git submodule update

# HINT
# git archive --remote=git://git.foo.com/project.git HEAD:path/to/directory filename
# http://stackoverflow.com/questions/1125476/git-retrieve-a-single-file-from-a-repository

cd 3rdparty

#phplot
#http://sourceforge.net/projects/phplot/files/latest/download

#jqplot
#https://bitbucket.org/cleonello/jqplot/wiki/Home

#WGET

#js-packer, not needed ?

#jquery

#jquery-dragsort
#http://dragsort.codeplex.com/downloads/get/887234

#PHPExcel
#https://phpexcel.codeplex.com/SourceControl/latest#
