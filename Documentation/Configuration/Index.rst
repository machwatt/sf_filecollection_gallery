.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration Reference
=======================

After including the static template file there are the following TypoScript options.

Plugin Settings
---------------
You may change the settings for ordering of the images to "From collection" now.
The ordering in the frontend is then depending on the ordering in the FileCollection data.
Fallback is ascending to not cause some breaking changes.

Its now possible to use the nested display mode in the plugin settings. This causes to
the plugin to use nested templates with an preview image per included file collection.
This way you could use the extension as image gallery with preview and a single view per gallery.

.. _configuration-typoscript:

TypoScript Reference
--------------------

Properties
^^^^^^^^^^

.. container:: ts-properties

	============================= ===================================== =======================================================================================
	Property                      Data type                             Default
	============================= ===================================== =======================================================================================
	view.templateRootPaths_       :ref:`t3tsref:data-type-path`         EXT:sf_filecollection_gallery/Resources/Private/Templates/
	view.partialRootPaths_        :ref:`t3tsref:data-type-path`         EXT:sf_filecollection_gallery/Resources/Private/Partials/
	view.layoutRootPaths_         :ref:`t3tsref:data-type-path`         EXT:sf_filecollection_gallery/Resources/Private/Layouts/
	settings.lightbox_            :ref:`t3tsref:data-type-string`       lightbox
	settings.enableLightbox_      :ref:`t3tsref:data-type-boolean`
	settings.cssFile_             :ref:`t3tsref:data-type-path`         EXT:sf_filecollection_gallery/Resources/Public/Css/sf-filecollection-gallery-basic.css
	settings.image.width_         :ref:`t3tsref:data-type-string`       200
	settings.image.height_        :ref:`t3tsref:data-type-string`       200m
	settings.image.lightboxWidth_ :ref:`t3tsref:data-type-string`       800
	============================= ===================================== =======================================================================================


Property details
^^^^^^^^^^^^^^^^

.. _settings-view.templateRootPaths:

view.templateRootPaths
""""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.view.templateRootPaths =` :ref:`t3tsref:data-type-path`

Define root path for templates as a fallback array.

.. _settings-view.partialRootPaths:

view.partialRootPaths
"""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.view.partialRootPaths =` :ref:`t3tsref:data-type-path`

Define root path for partials as a fallback array.

.. _settings-view.layoutRootPaths:

view.layoutRootPaths
""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.view.layoutRootPaths =` :ref:`t3tsref:data-type-path`

Define root path for layouts as a fallback array.

.. _settings-settings.lightbox:

settings.lightbox
"""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.settings.lightbox =` :ref:`t3tsref:data-type-boolean`

Additional output for lightbox settings. NOTE: You need some other extension (or anything else) to render that lightbox.

.. _settings-settings.enableLightbox:

settings.enableLightbox
"""""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.settings.enableLightbox =` :ref:`t3tsref:data-type-string`

Switch if lightbox is enabled. NOTE: You need some other extension (or anything else) to render that lightbox.

.. _settings-settings.cssFile:

settings.cssFile
""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.settings.cssFile =` :ref:`t3tsref:data-type-path`

Path to CSS File.

.. _settings-settings.image.width:

settings.image.width
""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.settings.image.width =` :ref:`t3tsref:data-type-string`

Width of single image.

.. _settings-settings.image.height:

settings.image.height
"""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.settings.image.height =` :ref:`t3tsref:data-type-string`

Width of single image.

.. _settings-settings.image.lightboxWidth:

settings.image.lightboxWidth
""""""""""""""""""""""""""""

:typoscript:`plugin.tx_sffilecollectiongallery.settings.image.lightboxWidth =` :ref:`t3tsref:data-type-string`

The maxWidth of the lightbox image.

