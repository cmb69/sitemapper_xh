PHPUNIT=phpunit.bat
PHPCI=pci.bat
SVN=svn

.PHONY: tests
tests:
	cd tests/; $(PHPUNIT) --colors .; cd ..

.PHONY: coverage
coverage:
	cd tests/; $(PHPUNIT) --coverage-html coverage/ .; cd ..

.PHONY: pci
pci:
	$(PHPCI) -id pcidirs.txt -d .

.PHONY: export
export:
	$(SVN) export -q . sitemapper/
	rm -rf sitemapper/sitemapper.komodoproject sitemapper/Makefile sitemapper/pcidirs.txt sitemapper/tests/
	cp sitemapper/config/config.php sitemapper/config/defaultconfig.php
	cp sitemapper/languages/en.php sitemapper/languages/default.php
