#!/bin/bash
# This script automate wordpress.org release process

display_version() {
    echo "Plugin Release script v1.0"
    echo "Copyright © 2018"
}
display_help() {
    echo "usage: wordpress-release.sh [--help] [--version] --path=PATH --tag=TAG"
    echo
    echo "This script is able to perform a WordPress.org commit and tag if asked."
    echo "  --path=PATH"
    echo "    wordpress.org repository folder, it must be a SVN checkout of the project."
    echo "  --tag=TAG"
    echo "    The tag to be created inside WordPress.org, it will also be made on Github."
    echo

    display_version
}

SCRIPT_ROOT=$( cd "$(dirname $0)" && pwd )
tag=""
path=$SCRIPT_ROOT
commit="FALSE"

while [ "$1" != "" ]
do
  case "$1" in
    -h | --help)
        display_help
        exit 0
        ;;
    --version)
        display_version
        exit 0
        ;;
    --commit)
        commit="TRUE"
        shift
        ;;
    -v | --verbose)
        verbose="verbose"
        shift
        ;;
    -p | --path)
        path="$(cd $2; pwd)"
        if [ ! -d $path ]
        then
            echo "Invalid folder given as export root"
            exit 1
        fi
        shift 2
        ;;
    -t | --tag)
        tag="$2"
        if [ ! -d $tsroot ]
        then
            echo "Invalid folder given as export root"
            exit 1
        fi
        shift 2
        ;;
    *)  # No more options
        shift 1
        ;;
  esac
done

cd "$SCRIPT_ROOT/.."
composer archive --format=tar --dir="$path" --file=export
php $SCRIPT_ROOT/readme-builder.php > "$path/readme.txt"
cd "$path"
tar -xzf "export.tar"
rm "export.tar"

#Rebuilt MD5File structure from backuped database
if [ "$commit" == "TRUE" ]
then
    svn add -q *
    svn commit
fi
