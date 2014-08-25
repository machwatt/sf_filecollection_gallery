# Simple FileCollection Gallery

## Installation
Install Simple FileCollection Gallery and include the static template.

## Settings
Simple FileCollection Gallery provides settings to customize the output.

	view.templateRootPath = The root path for templates
	view.partialRootPath = The root path for partials
	view.layoutRootPath = The root path for layouts

	settings.lightbox = Additional output for lightbox settings, default 'lightbox'
	settings.enableLightbox = Switch if lightbox is enabled
	settings.cssFile = Path to CSS File

	settings.image.width = Width of single image
	settings.image.height = Height of single image
	settings.image.lightboxWidth = The maxWidth of the lightbox image

## Usage
Create a FileCollection somewhere in the page tree of your TYPO3 Installation.
Select the FileCollection in the plugin via wizard or page tree.

There are some more options for the plugin:

	fileCollection = the selected fileCollection(s)
	imagesPerPage = Sets the count of images to be rendered per pagination page
	numberOfPages = Sets the count of visible pages in the pagination list
	insertAbove = Switches the pagination above the images list
	insertBelow = Switches the pagination below the images list
	enableLightbox = Switches if a lightbox is enabled for the images list
	enableDescription = If the metadata 'description' is provided, this switches the description below a single image

## Credits
This extension uses some third party code.
+ [Array Pagination Widget](http://blog.teamgeist-medien.de/2014/01/extbase-fluid-widget-paginate-viewhelper-mit-array-unterstuetzung.html)
	An Array Pagination Widget provided by Paul Beck and Armin Ruediger Vieweg
+ [IncludeFileViewHelper](https://github.com/georgringer/news)
	A ViewHelper for including the css file via the extension layout, provided by Georg Ringer in his news extension.
