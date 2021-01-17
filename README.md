# Sitemapper\_XH

Sitemapper\_XH automatically creates an XML-Sitemap of your CMSimple\_XH installation.
For detailed information about Sitemaps see [sitemaps.org](http://www.sitemaps.org/).

## Table of Contents

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
- [License](#license)
- [Credits](#credits)

## Requirements

Sitemapper\_XH is a plugin for CMSimple\_XH ≥ 1.7.0 and PHP ≥ 5.4.0.

## Download

The [lastest release](https://github.com/cmb69/sitemapper_xh/releases/latest) is available for download on Github.

## Installation

The installation is done as with many other CMSimple\_XH plugins. See the
[CMSimple\_XH Wiki](https://wiki.cmsimple-xh.org/doku.php/installation#plugins)
for further details.

1. Backup the data on your server.
2. Unzip the distribution on your computer.
3. Upload the whole directory `sitemapper/` to your server into the `plugins/` directory of CMSimple\_XH.
4. Set write permissions for the subdirectories `config/` und `languages/`.
5. Browse to the administration of Sitemapper\_XH (`Plugins` → `Sitemapper`),
   and check if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple\_XH plugins in
the back-end of the Website. Select `Plugins` → `Sitemapper`.

You can change the default settings of Sitemapper\_XH under `Config`. Hints for
the options will be displayed when hovering over the help icons with the mouse.

Localization is done under `Language`. You can translate the character
strings to your own language (if there is no appropriate language file
available), or customize them according to your needs.

## Usage

Sitemapper\_XH makes Sitemaps of your Website available. There is a Sitemap
index document, and additional Sitemap documents for each installed language.
These documents are created on the fly, and are **not** stored in
the filesystem.

All published pages that are not hidden will be included in the
Sitemaps. Additionally hidden pages will be included when `ignore hidden pages`
is disabled. You can view the generated Sitemaps in the plugin administration
under `Available Sitemaps`.

The simplest way to make the Sitemaps available to search engines is to put
the following line to your `robots.txt`:

    Sitemap: http://www.example.com/?sitemapper_index

Of course you have to adjust the URL to your domain name (it is best to just
copy and paste the URL to the Sitemap index document from the plugin
administration). Another possibility is to submit your Sitemaps to search
engines directly (e.g. via the
[Google Webmaster Tools](http://www.google.com/webmasters/)).

It is possible to override the default settings of `changefreq` and
`priority`, which are defined in the plugin config, for each page in the
pagedata tab `Sitemap` above the editor. For details on this settings, see
[XML tag definitions](http://www.sitemaps.org/protocol.php#xmlTagDefinitions).

## Limitations

Sitemapper\_XH generates the URLs in the Sitemaps depending on how the Sitemap
has been requested. If the CMSimple\_XH installation is accessible via
http://example.com/, https://www.example.com/, http://www.example.com/index.php,
etc., the URLs may be different each time. To avoid that, you should set up
appropriate redirects, what can easily be done with the
[Seo\_XH plugin](http://3-magi.net/?CMSimple_XH/Seo_XH), for instance.

Sitemapper\_XH will only list the actual pages of the content of CMSimple\_XH.
Additional content that is managed by plugins, e.g. forums or blogs, will not be
included in the Sitemap documents.

## License

Sitemapper\_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Sitemapper\_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Sitemapper\_XH.  If not, see http://www.gnu.org/licenses/.

© 2011-2017 Christoph M. Becker

Czech translation © 2011-2012 Josef Němec  
Polish translation © 2012 Kamill Krzes  
Slovak translation © 2012 Dr. Martin Sereday  
Estonian translation © 2013 Alo Tänavots

## Credits

Sitemapper\_XH was inspired by *Simmyne*.

The plugin logo was designed by [Wendell Fernandes](http://www.dellustrations.com/).
Many thanks for publishing this icon as freeware.

This plugin uses free applications icons from [Aha-Soft](http://www.aha-soft.com/).
Many thanks for making these icons freely available.

Many thanks to the community at the [CMSimple\_XH-Forum](http://www.cmsimpleforum.com/)
for tips, suggestions and testing.
Particularly I want to thank *Ulrich* for being the first beta tester of Sitemapper\_XH.
And many thanks to *sareide*, who detected a severe bug regarding the URLs of subsite/second language pages.
Also many thanks to *olape*, a long time user providing lots of valuable feedback.

Last but not least many thanks to [Peter Harteg](http://www.harteg.dk/), the “father” of CMSimple,
and all developers of [CMSimple_XH](http://www.cmsimple-xh.org/),
without whom this amazing CMS would not exist.
