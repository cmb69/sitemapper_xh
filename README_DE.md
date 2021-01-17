# Sitemapper\_XH

Sitemapper\_XH erzeugt automatisch eine XML-Sitemap Ihrer CMSimple\_XH Installation.
Ausführliche Information über Sitemaps erhalten Sie unter [sitemaps.org](http://www.sitemaps.org/).

## Inhaltsverzeichnis

- [Vorraussetzungen](#vorraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Beschränkungen](#beschränkungen)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Vorraussetzungen
Sitemapper\_XH ist ein Plugin für CMSimple\_XH ≥ 1.7.0 und PHP ≥ 5.4.0.

## Download

Das [aktuelle Release](https://github.com/cmb69/sitemapper_xh/releases/latest) kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple\_XH-Plugins auch.
Im [CMSimple\_XH Wiki](https://wiki.cmsimple-xh.org/doku.php/de:installation#plugins)
finden sie ausführliche Hinweise.

1. Sichern Sie die Daten auf Ihrem Server.
2. Entpacken Sie die ZIP-Datei auf Ihrem Computer.
3. Laden Sie das gesamte Verzeichnis `sitemapper/` auf Ihren Server in das
   `plugins/` Verzeichnis von CMSimple\_XH hoch.
4. Vergeben Sie Schreibrechte für die Unterverzeichnisse `config/` und
   `languages/`.
5. Wählen Sie `Sitemapper` im Administrationsbereich, um zu prüfen, ob alle
   Voraussetzungen erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple\_XH-Plugins auch im Administrationsbereich der Homepage. Wählen Sie
`Plugins` → `Sitemapper`.

Sie können die Voreinstellungen von Sitemapper\_XH unter `Konfiguration`
ändern. Beim Überfahren der Hilfe-Icons mit der Maus werden Hinweise zu den
Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder sie entsprechend Ihren Anforderungen
anpassen.

## Verwendung

Sitemapper\_XH stellt Sitemaps Ihrer Website zur Verfügung. Es gibt einen
Sitemap-Index und zusätzliche Sitemaps für jede installierte Sprache.
Die Sitemaps werden dynamisch generiert, und **nicht** im
Dateisystem abgelegt.

Alle veröffentlichten Seiten, die nicht versteckt sind, werden in den
Sitemaps aufgeführt. Darüberhinaus werden verstecke Seiten aufgeführt, wenn
`ignore hidden pages` deaktiviert ist. Sie können die erzeugten Sitemaps unter
`Vorhandene Sitemaps` einsehen.

Die einfachste Möglichkeit die Sitemaps Suchmaschinen zur Verfügung zu
stellen, ist die folgende Zeile in Ihre `robots.txt` zu schreiben:

    Sitemap: http://www.example.com/?sitemapper_index

Natürlich müssen Sie die URL an Ihren Domainnamen anpassen (es ist das beste,
wenn Sie die URL zum Sitemap-Index einfach aus der Plugin-Administration durch
Copy&Paste übernehmen). Eine weitere Möglichkeit besteht darin, Ihre Sitemaps
direkt bei Suchmaschinen einzureichen
(z.B. über die [Google Webmaster Tools](http://www.google.com/webmasters/)).

Es ist möglich die Standardwerte von `changefreq` und `priority`, die in den
Plugin Einstellungen definiert wurden, für jede Seite in der Pagedata
Registerkarte `Sitemap` oberhalb des Editors zu überschreiben. Ausführliche
Erläuterungen zu diesen Einstellungen, finden Sie unter
[XML tag definitions](http://www.sitemaps.org/protocol.php#xmlTagDefinitions).

## Beschränkungen

Sitemapper\_XH erzeugt die URLs in den Sitemaps abhängig davon wie die Sitemap
abgerufen wurde. Kann auf die CMSimple_XH Installation durch
http://example.com/, https://www.example.com/, http://www.example.com/index.php,
usw. zugegriffen werden, dann können die URLs jedes mal anders sein. Um das zu
vermeiden, sollten entsprechende Weiterleitung eingerichtet werden, was
beispielsweise leicht mit dem [Seo\_XH Plugin](http://3-magi.net/de/?CMSimple_XH/Seo_XH)
durchgeführt werden kann.

Sitemapper\_XH wird nur die eigentlichen Seiten des CMSimple_XH Inhalts
auflisten. Zusätzliche Inhalte, die von Plugins verwaltet werden, z.B. Foren
oder Blogs, werden nicht in die Sitemap-Dokumente aufgenommen.

## Lizenz

Sitemapper\_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Sitemapper\_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Sitemapper\_XH erhalten haben. Falls nicht, siehe http://www.gnu.org/licenses/.

© 2011-2017 Christoph M. Becker

Tscheschiche Übersetzung © 2011-2012 Josef Němec  
Polnische Übersetzung © 2012 Kamill Krzes  
Slovakische Übersetzung © 2012 Dr. Martin Sereday  
Estnische Übersetzung © 2013 Alo Tänavots

## Danksagung

Sitemapper\_XH wurde von *Simmyne* angeregt.

Das Plugin-Logo wurde von [Wendell Fernandes](http://www.dellustrations.com/) entworfen.
Vielen Dank für die Veröffentlichung als Freeware.

Dieses Plugin verwendet „free application icons“ von [Aha-Soft](http://www.aha-soft.com/).
Vielen Dank für die freie Verwendbarkeit dieser Icons.

Vielen Dank an die Community im [CMSimple\_XH-Forum](http://www.cmsimpleforum.com/)
für Tipps, Anregungen und das Testen.
Besonders möchte ich *Ulrich* danken, dass er der erste Beta-Tester von Sitemapper\_XH war.
Und vielen Dank an *sareide*, der einen schweren Fehler bezüglich der URLs von
Subsite/Zweitsprachen-Seiten entdeckt hat.
Ebenfalls vielen Dank an *olape*, einem Langzeit-Nutzer, der viel wertvolles
Feedback liefert.

Zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/), dem „Vater“ von CMSimple,
und alle Entwickler von [CMSimple\_XH](http://www.cmsimple-xh.org/de/),
ohne die dieses fantastische CMS nicht existieren würde.
