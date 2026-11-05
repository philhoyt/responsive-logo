# Responsive Site Logo

[![CI](https://github.com/philhoyt/responsive-logo/actions/workflows/ci.yml/badge.svg)](https://github.com/philhoyt/responsive-logo/actions/workflows/ci.yml)

A block that shows one logo on desktop and a different one at or below a mobile breakpoint.

## Requirements

| | Minimum | Tested up to |
|---|---|---|
| WordPress | 6.6 | 7.1 |
| PHP | 7.4 | 8.4 |

## Installation

1. Download `responsive-site-logo.zip` from the latest [release](https://github.com/philhoyt/responsive-logo/releases).
2. In wp-admin go to **Plugins > Add New Plugin > Upload Plugin**, choose the zip and click **Install Now**.
3. Activate the plugin.

Updates come from GitHub releases through Plugin Update Checker, which is bundled in `lib/`.

## Usage

Add the **Responsive Site Logo** block (category Media) to a header template part or a page. The block sidebar has two panels:

- **Images**: a desktop logo and a mobile logo, each picked from the media library. Alt text and MIME type are taken from the attachment. With only one image set, it is shown at every width.
- **Settings**: the mobile breakpoint (320-1200px in steps of 10, default 768; screens that wide or narrower get the mobile logo), a max width (0-800px, 0 for none) and a **Link to Homepage** toggle (on by default).

When both images are set, a Desktop/Mobile toggle above the editor preview switches between them.

The block is rendered by `src/site-logo/render.php`. It prints a `<style>` block scoped to the block's unique id with the media query, then both images with the classes `rsl-logo--desktop` and `rsl-logo--mobile`. An SVG attachment is read from disk and inlined with `role="img"` and the alt text as `aria-label`; XML declarations, doctypes, `<script>` elements and `on*` attributes are stripped first. Any other image type renders as an `<img>`. The home link uses `home_url( '/' )` at render time, so it follows the site URL.

Block attributes (`responsive-site-logo/site-logo`): `desktopLogoId`, `desktopLogoUrl`, `desktopLogoAlt`, `desktopLogoType`, `mobileLogoId`, `mobileLogoUrl`, `mobileLogoAlt`, `mobileLogoType`, `breakpoint`, `maxWidth`, `linkToHome`. Supports wide and full alignment and margin and padding.

## Development

```bash
npm install && composer install

npm run start      # webpack watch (copies render.php into build/)
npm run build      # production build
npm run format     # wp-scripts format
npm run lint:js
npm run lint:css
npm run lint:php   # phpcs (WPCS), same as composer run lint
composer run lint:fix
npm run plugin-zip # build responsive-site-logo.zip from the "files" list in package.json
```

`composer install` also copies Plugin Update Checker from `vendor/` into `lib/`, which is what ships; `vendor/` is excluded from the zip by `.distignore`. There is no test suite. CI runs phpcs on PHP 7.4 to 8.4 and the JS and CSS linters and build on Node 22.

## Releases

Push a lowercase `v` tag:

```bash
git tag v1.0.2 && git push origin v1.0.2
```

`.github/workflows/release.yml` installs dependencies, runs `npm run build` and `npm run plugin-zip`, pulls that version's section out of `readme.txt` for the release notes, and attaches `responsive-site-logo.zip` and `readme.txt` to the GitHub release. Plugin Update Checker on installed sites is set to require a `.zip` release asset, so a release without one is skipped. An uppercase `V` tag is ignored on purpose: the update checker only strips a lowercase prefix.

Before tagging, bump the version in `responsive-site-logo.php`, `package.json`, `src/site-logo/block.json` and the `Stable tag` and changelog in `readme.txt`.

## Limitations

- It does not read or write the core site logo; the two images belong to the block.
- One breakpoint per block. A third image for tablet widths is not supported.
- An inline SVG is sanitised with regular expressions, not an XML parser; only upload SVGs you trust.
- The `<style>` block is printed inline with each block instance, one per block on the page.

## Changelog

### 1.0.2
- Fixed: the editor controls and the front-end logo are styled again; the stylesheets were missing from the build.

### 1.0.1
- Fixed: two or more Responsive Site Logo blocks on one page no longer cause a fatal error.
- Fixed: all strings use the plugin's text domain so translations apply to the editor and the front end.

### 1.0.0
- Initial release.

## License

GPL-2.0-or-later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
