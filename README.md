# Simple FileCollection Gallery

Simple Image Gallery which renders a FileCollection containing static or folder based images. It provides a simple layout with pagination and a lightbox. Based on fluid templating the frontend layout can be edited fast and easy.

Since this gallery renders a set of files from a FileCollection it may render something else than an image gallery,
depending on the provided template. (e.g. List of downloadable files).

## Installation
Install Simple FileCollection Gallery and include the static template.

## Settings
Simple FileCollection Gallery provides settings to customize the output.

	view.templateRootPaths = The root path for templates as fallback array
	view.partialRootPaths = The root path for partials as fallback array
	view.layoutRootPaths = The root path for layouts as fallback array

	settings.lightbox = Additional output for lightbox settings, default 'lightbox'
	settings.enableLightbox = Switch if lightbox is enabled  NOTE: You need some other extension (or anything else) to render that lightbox.
	settings.cssFile = Path to CSS File

	settings.image.width = Width of single image
	settings.image.height = Height of single image
	settings.image.lightboxWidth = The maxWidth of the lightbox image

## Usage
Create a FileCollection somewhere in the page tree of your TYPO3 Installation.
Select the FileCollection(s) in the plugin via wizard or page tree.
The order of the images is like the sorting in the FileCollection and the order of collections in the plugin.

There are some more options for the plugin:

	fileCollection = the selected fileCollection(s)
	imagesPerPage = Sets the count of images to be rendered per pagination page
	numberOfPages = Sets the count of visible pages in the pagination list
	insertAbove = Switches the pagination above the images list
	insertBelow = Switches the pagination below the images list
	enableLightbox = Switches if a lightbox is enabled for the images list  NOTE: You need some other extension (or anything else) to render that lightbox.
	enableDescription = If the metadata 'description' is provided, this switches the description below a single image

## Templating
Since Version 1.2.0 it is possible to use nested layouts with an preview image per included FileCollection.
Thanks to [Ferhat36](https://github.com/Ferhat67)


Since Version 1.1 of sf_filecollection_gallery it is possible to use the 
templateRootPaths array to provide some more template folders with fallback.

Currently there are two templates used, look at them in Resources/Private/Templates:
* Gallery/List.html for the Gallery View
* Paginate/Index.html for the pagination widget

### Creating an own template
If you want to provide an own template just add another entry to the templateRootPaths array in your TS:

	plugin.tx_sffilecollectiongallery {
		view.templateRootPaths.1 = fileadmin/path/to/Templates
	}

You might want to add some slider functionality, so we used [bxSlider](http://www.bxslider.com) for this simple tutorial.
All you need to do is render the FileCollection items as an unordered list and given that you included the CSS and JS 
for bxSlider already, add some handy JS initialization on document.ready.

Your new Gallery/List.html file might look something like this:

	{namespace s=SKYFILLERS\SfFilecollectionGallery\ViewHelpers}
	
	<f:layout name="Default"/>
	
	<f:section name="main">
		<f:if condition="{imageItems}">
			<ul class="fadeImages">
				<f:for each="{imageItems}" as="object">
					<li class="sf-filecollection-gallery-image-container">
						<f:image image="{object}" width="{settings.image.width}" height="{settings.image.height}" alt="{object.properties.alternative}" title="{object.properties.title}"/>
					</li>
				</f:for>
			</ul>
		</f:if>
	</f:section>

And the JS to get this started is as following:

	$(document).ready(function(){
		$('.fadeImages').bxSlider();
	});

Shiny! Of course you might add some more complex fluid templating.

### Use content element uid in template
Since version 1.1.4 it is possible to use the elements uid in your template.
This is necessary for some JS functionality like lightboxes or sliders.
The uid can be accessed via:
	{currentUid}

## Credits
This extension uses some third party code.
+ [Array Pagination Widget](http://blog.teamgeist-medien.de/2014/01/extbase-fluid-widget-paginate-viewhelper-mit-array-unterstuetzung.html)
	An Array Pagination Widget provided by Paul Beck and Armin Ruediger Vieweg
+ [IncludeFileViewHelper](https://github.com/georgringer/news)
	A ViewHelper for including the css file via the extension layout, provided by Georg Ringer in his news extension.
