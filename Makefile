clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/VerboseAdventure" --directory="build"

install:
	ppm --no-prompt --fix-conflict --install="build/net.intellivoid.verbose_adventure.ppm"
