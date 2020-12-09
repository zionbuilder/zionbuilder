# Zion Page Builder Plugin
[![stars](https://img.shields.io/github/stars/zionbuilder/zionbuilder)](https://github.com/zionbuilder/zionbuilder/stargazers)
[![CI](https://github.com/zionbuilder/zionbuilder/workflows/CI/badge.svg)](https://github.com/zionbuilder/zionbuilder/actions)
[![license](https://img.shields.io/github/license/zionbuilder/zionbuilder)](/license.txt)
[![size](https://img.shields.io/badge/zip%20size-2%20783kb-blue)](https://github.com/zionbuilder/zionbuilder/releases)
[![GitHub contributors](https://img.shields.io/github/all-contributors/zionbuilder/zionbuilder)](https://github.com/zionbuilder/zionbuilder/graphs/contributors/)

Built and designed by [hogash team](https://hogash.com),
the creator of famous WordPress theme [Kallyas](https://kallyas.net/),
this plugin is the newest page builder on the market that comes with tools to build an outstanding WordPress website.

**Tags:** page builder, WordPress module, editor, visual editor, design, website builder, front-end builder

## Description
Below are listed the core competencies

### Elements and templates at your fingertip
Add elements and templates right where you need them from the page builder "Add Elements Popup"

### Basic 27 elements
* Section
* Column
* Text Editor
* Custom HTML
* Shortcode
* Google Maps
* Counter
* Progress Bars
* Image Slider
* Anchor Point
* Testimonials
* Icon List
* Alert
* Sidebar
* Soundcloud
* Pricing Box
* Tabs
* Accordions
* Image Box
* Image
* Icon
* Icon Box
* Gallery
* Heading
* Video
* Button
* Separator

### Independent composition of elements
Each element is composed from a wrapper and its sub-components. For example a button is composed from a wrapper, the button and the icon, and each one of them may have individual styles applied.

### Unified system of options
Every element has the same easy to use and intuitive options pattern found in the “Element Options” panel. It consists of 3 main tabs: general, styling, advanced and search.

### Global styles
In order to apply the same styles on multiple elements, Global CSS Classes were implemented. They can be styled either from page options panel, or from the Element options panel. No coding skills required.

### Live responsive editing
Visually decide the sizes, colors or positioning of the elements for each screen. This means that any changes made to the mobile view, will only appear on that device and it will not affect the other devices, which will still maintain the original design options.

### Custom rich text on click
Inline editor is triggered on text click, and provides options such as font family, size, weight, spacing, alignment or text-transform. It can also be dragged anywhere.

### Gradient background
You can layer multiple gradients by making use of color opacity. When set a lower opacity of gradient colors, other gradient layers become visible. Experiment with a different options on different gradient layers.

### Manage loaded resources
You can choose what fonts , colors, gradients, icon packs or templates to appear in the page builder. That means you can choose only the fonts you need in your website even if on the dashboard you have access to hundreds of other fonts.

### History of actions
Saving system, history of your actions, post revisions, users permissions or easily discard changes give you the power of becoming the sole creator of your website. Shortcuts for undo and redo are also available.

### Design features
The following extra design features come built in the Panel Element Options: Blending Bakground, ColorPicker, Pseudo – selectors, Element’s motion control, Flexbox control, Powerful background image, Filters, Borders, Shadows, Typography, Transform, Transitions

### Library system
A whole panel that can be accessed from the main bar. It contains the actions needed for import and exporting library parts, and the access to Zion library.

### Advanced features
* Post revisions
* Role Manager
* Regenerate CSS
* Replace URL
* Custom CSS
* Custom javaScript
* Renaming elements
* Element’s visibility
* Custom HTML element
* Columns sizes and offsets
* Extendable options

### User experience features
* Dragging elements
* Dragging size and spacing
* Right-click actions
* Top right toolbar shortcuts
* Custom workspace
* Keyboard shortcuts
* Searchable options
* Discard changes
* Easily change the number values
* CTRL + DRAG

## Installation

### Minimum requirements
* WordPress 5.0 or higher
* PHP 7.0 or higher
* MySQL 5.6 or higher
* WP Memory Limit at least 64M or higher
* Writing permissions for WordPress uploads directory and `.htaccess` file
* PHP Zip extension must be installed
* PHP GD extension must be installed

### Installation
1. Install using the [WordPress built-in Plugin](https://wordpress.org/plugins/zionbuilder/)
   installer Go to Plugins -> Add New
1. Search for Zionbuilder
1. Activate the plugin
1. Go to Pages or Posts > Add New
1. Press the 'Edit with Zion Page Builder' button.

For documentation and tutorials visit our [Knowledge Base](https://zionbuilder.io/help-center/).

## Multilingual plugin
The strings used in the builder are ready to be translated in any language, according to WordPress standards.

## An extendable builder – for developers and designers
The whole system of options, which is built in PHP, allows other developers to add their own options to the main panel.
The library system also allows designers to submit new templates which users will have access to through Zion Library.

## Documentation and Support
[Documentation link](https://zionbuilder.io/help-center/)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## Contributors
<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://github.com/szepeviktor/debian-server-tools/blob/master/CV.md"><img src="https://avatars3.githubusercontent.com/u/952007?v=4" width="100px;" alt=""/><br /><sub><b>Viktor Szépe</b></sub></a><br /><a href="#ideas-szepeviktor" title="Ideas, Planning, & Feedback">🤔</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

## License
 GNU GENERAL PUBLIC LICENSE
[license](https://www.gnu.org/licenses/gpl-3.0.html)

## Changelog ##
### 1.2.2 2020-12-09 ###
* Fixed focus on inline editor font size
* Fixed Panel Library maximize buton click not working
* Fixed wp 5.6 compatibility
* Fixed click on adding global color closes colorPicker
* Fixed tabs element not applying custom classes
### 1.2.1 2020-12-08 ###
* Internal Feature: allow setting default value for repeater field
* Improved: Add message when there are no saved gradients
* Improved: Added allow Html in title fields in editor mode
* Improved: Icon element markup in editor mode
* Improved: demo mode
* Improved: icon tab set normal default
* Fixed Column styling options not showing on Safari on MacOS Big Sur
* Fixed render classes on positioning the counter element
* Fixed accordions element not showing in page when first added
* Fixed Box shadow inset applies even if it is set to no
* Fixed heading align option not responsive
* Fixed custom classes not applying to various elements

### 1.2.0- 2020-12-07 ###
* Added options to style the active tab for Tabs element
* Added WordPress filters for REST controllers
* Renamed Custom HTML element to Custom Code
* Improved: Changes green dot now appears for dynamic values
* Improved Zion Builder library display Improved: The options panel will show the general tab when editing a different element
* Improved: Removed help tab from builder panel.
* Improved: Page cache not clearing properly when modifying the page
* Fixed accordion and tabs elements not working in certain conditions
* Fixed icon element options not applying in certain conditions
* Fixed shortcodes not working
* Fixed element changes not saving to history in certain conditions
* Fixed pseudo-element before options shows console error in certain conditions
* Fixed restoring from local storage not working in certain conditions
* Various code cleanup and improvements

### 1.1.0- 2020-11-24 ###
* Feature: Add Elements Popup pens automatically when column layouts are inserted
* Feature: Element options panel: Added ability to click on the element title to select the parent element
* Feature: Abillity to change Column setting for each viewport for Zion's Gallery Element
* Feature: Make Button Position option changeable per viewport
* Renamed regenerate css to regenerate css and js
* Improved history
* Improved Tabs Element
* Allow save page on content editable
* Allow browser context on right click in preview mode
* Hide element menu when scrolling
* Replaced pageEvents with windows
* Update plugin info card for pro version
* Update Urls for changelog
* Implemented element scroll to
* Replace modal tour with intro video
* Change Tab name from "Columns" to "Layouts"
* Updated elements registration
* Created methods for adding childs to elements
* Added ability to use zion builder version of Vue
* Made components package as vue plugin
* Reorganized components
* Updated to Popper2
* Removed Vuex from dependencies
* Add padding to element icon for image element
* Add v1.0.0 to lerna
* Update to Vue 3 and fix compatibility syntax
* Switched to monorepo
* Added filter for license check
* Update Data Fonts Google set
* Add ability to get render attributes for multiple tag ids
* Fixed poper behaviour on scroll
* Fixed image wrapper border on image option
* Fixed element custom css not working
* Fixed Mozilla bug on input
* Fixed locked user data
* Fixed image element margin drag hides the toolbox
* Fixed Zion Builder not working with Yoast in certain conditions
* Fixed PHP Stan erors
* Fixed CSS classes not cleared when a new css class is added or changed
* Fixed render "Array" string on elements classes
* Fixed section masks not working
* Fixed element with no options shows values as array
* Fixed CSS not generated when using 0 (zero) without any suffix like 'px' for input values
* Fixed Absolute Positioning of elements in Zion Builder is not the same as on the front end
* Fixed CSS not working correctly inside Zion Builder after changing Column ID
* Fixed Zion Keyboard Shortcuts prohibit from typing capital letters inside the Wordpress media popup screen
* Fixed css classes not cleared when a new css class is added or changed
* Fixed Custom Javascript doesn't get removed in 'Custom Javascript' under 'Page Options' when saving page

### 1.0.1- 2020-08-17 ###

* Renamed plugin
* Changed register element system
* Updated composer dependencies
* Replaced PRO description from admin
* Updated minimum PHP version
* Performance optimizations
* Implement pro connection
* Add pro badge to dashboard
* Add calendar picker
* Add new transform properties
* Moved render methods to render class
* Added loading gif to element loading
* Update keywords for elements
* Prevent adding emty values on transition property
* Added offset 0 value for columns
* Added dynamic background color option type
* Implement masks for sections and columns
* Change png to svg in section view
* Add specialFilterPak prop to iconsLibraryModal
* Add dynamic content to heading, iconbox, iconList, ImageBox,PricingBox, ProgressBars, Testimonial
* Added vendor prefixes for css styles
* Added filters to various locations
* Remove seconds from History items
* Added filter for options parsing
* Added form filters
* Added filter for element custom css
* Updated plugin version
* Added namespace to plugin file
* Updated pagebuilder meta keys
* Implemented update system
* Added updater base code
* Fixed data update not working properly
* Fixed template not importing images in certain conditions
* Fixed background size contain
* Fixed panels height dragging
* Fixed background gradient not working
* Fixed case sensitive icon search
* Fixed styles not generating proper css
* Fixed duplicate renderer var
* Fixed css duplicated
* Fixed animations visibility glitch
* Fixed flex direction row in rendered page
* Fixed uploaded icons not showing in frontend editor
* Fixed css gradients
* Fixed animation duration and delay not working properly
* Fixed missing post id for bulk actions API call
* Fixed inverse text shadow icons for horizontal and vertical spread
* Fixed no loader for templates library preview when inserting the item
* Fixed blur not rendering correctly
* Fixed compatibility with older versions of MySQL/MariaDB
* Fixed safari library scroll not working
* Fixed compatibility with legacy zionbuilder
* Fixed several PHP stan errors
* Fixed build command
* Fixed testimonial element not showing properly in frontend
* Fixed scrollTop and overflow hidden from accordion menu
* Fixed compatibility with WP 5.4.2
* Fixed error showing when using video background

### 1.0.0- 2020-05-27 ###
* Initial Public Release
