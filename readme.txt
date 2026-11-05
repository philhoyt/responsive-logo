=== Responsive Site Logo ===
Contributors: philhoyt
Tags: logo, responsive, mobile, block
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A block that shows one logo on desktop and a different one at or below a mobile breakpoint

== Description ==

The Responsive Site Logo block takes two images from the media library, a desktop logo and a mobile logo, and shows whichever fits the screen. The breakpoint is set per block (320-1200px, default 768px); screens that wide or narrower get the mobile logo. If only one image is set, it is shown at every width.

The block also has an optional max width (up to 800px) and a toggle that wraps the logo in a link to the home page. An SVG logo is inlined in the page rather than loaded as an image; script tags, event handlers and XML declarations are stripped from it first. Other formats render as an ordinary img tag with the alt text from the media library.

The block is rendered on the server, so the home link always points at the current site URL. In the editor, a Desktop/Mobile toggle above the preview switches between the two images.

== Installation ==

1. Download `responsive-site-logo.zip` from the latest release at https://github.com/philhoyt/responsive-logo/releases.
2. In wp-admin go to Plugins > Add New Plugin > Upload Plugin, choose the zip and click Install Now.
3. Activate the plugin.
4. Add the Responsive Site Logo block to a header template part or a page and pick the two images in the block sidebar.

Updates are delivered from GitHub releases and appear in wp-admin like any other plugin update.

== Frequently Asked Questions ==

= Can I use a single image? =

Yes. With only a desktop or only a mobile image set, that image is shown at every width.

= Does it replace the core Site Logo block? =

No. It does not read or write the site logo set under Settings or in the Site Editor; the two images belong to the block.

== Changelog ==

= 1.0.2 =
* Fixed: the editor controls and the front-end logo are styled again; the stylesheets were missing from the build.

= 1.0.1 =
* Fixed: two or more Responsive Site Logo blocks on one page no longer cause a fatal error.
* Fixed: all strings use the plugin's text domain so translations apply to the editor and the front end.

= 1.0.0 =
* Initial release.
