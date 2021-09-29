clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/VerboseAdventure" --directory="build"

update:
	ppm --generate-package="src/VerboseAdventure"

install:
	ppm --no-prompt --fix-conflict --install="build/net.intellivoid.verbose_adventure.ppm"
