default:
	echo "Choose checkin, checkout, bin, install, or gitconfig"

install:
	./doinstall.sh --install

update:
	./doinstall.sh

start: update
	/etc/init.d/iglooportal start

stop:
	/etc/init.d/iglooportal stop


# NOTE: Forced removal of unwanted objects (even in history) using:
# http://git-scm.com/book/ca/Git-Internals-Maintenance-and-Data-Recovery
# then git push -f origin master

gitconfig:
	git config --global push.default current # simple
	git config --global credential.helper 'cache --timeout=3600'

checkout:
	git pull

checkin: # e.g. downwa
	git add -v --all
	git commit -v
	git push

