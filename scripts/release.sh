#!/bin/bash
set -e

VERSION=
INTERACTIVE=
VERSION_TYPE=

function usage {
	cat <<-USAGE
		 -nv --newversion Build type can be Test|Release
		 -i --interactive Interactive mode. You will be asked questions about release
	USAGE
}


while [ "$1" != "" ]; do
	case $1 in
		-nv | --newversion )    VERSION=$2
								;;
		-i | --interactive )    INTERACTIVE=1
								;;
		-h | --help )           usage
								exit
								;;
	esac
	shift
done
echo "ver: $VERSION"
echo "INTERACTIVE: $INTERACTIVE"
echo "$1: $1"
echo "$0: $0"

if [ "$INTERACTIVE" == "1" ]; then

	PS3="Release type(prerelease): "
	options=("prerelease" "patch" "minor" "major", "manual")
	select VERSION in "${options[@]}"
	do
		case $VERSION in
			"prerelease")
				echo "Building $VERSION"
				break
				;;
			"patch")
				echo "Building $VERSION"
				break
				;;
			"minor")
				echo "Building $VERSION"
				break
				;;
			"major")
				exit
				break
				;;
			"manual")
				echo "Enter new version: "
				  read -r VERSION
				break
				;;
			*)
				VERSION="prerelease"
				exit;;
		esac
	done

	read -p "Releasing $VERSION - are you sure? (y/n) " -n 1 -r
fi

# DO the release

echo
if [[ $REPLY =~ ^[Yy]$ ]] || [ "$INTERACTIVE" == "" ]; then
	echo "Releasing $VERSION ..."

	# build files
	npm run build version=$VERSION

	  # npm run build -version=$VERSION
	# PACKAGE_VERSION=$(cat package.json \
	# 	| grep version \
	# 	| head -1 \
	# 	| awk -F: '{ print $2 }' \
	# 	| sed 's/[",]//g')

	# echo $PACKAGE_VERSION

fi



echo "test: $RELEASE_TYPE"
echo "version: $VERSION"