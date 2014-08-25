.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration Reference
=======================

After including the static template file there are the following TypoScript options.


.. _configuration-typoscript:

TypoScript Reference
--------------------

Properties
^^^^^^^^^^

.. container:: ts-properties

	============================= ===================================== =======================================================================================
	Property                      Data type                             Default
	============================= ===================================== =======================================================================================
	view.templateRootPath_        Path                                  EXT:sf_filecollection_gallery/Resources/Private/Templates/
	view.partialRootPath_         Path                                  EXT:sf_filecollection_gallery/Resources/Private/Partials/
	view.layoutRootPath_          Path                                  EXT:sf_filecollection_gallery/Resources/Private/Layouts/
	settings.lightbox_            String                                lightbox
	settings.enableLightbox_      Boolean
	settings.cssFile_             Path                                  EXT:sf_filecollection_gallery/Resources/Public/Css/sf-filecollection-gallery-basic.css
	settings.image.width_         String                                200
	settings.image.height_        String                                200m
	settings.image.lightboxWidth_ String                                800
	============================= ===================================== =======================================================================================


Property details
^^^^^^^^^^^^^^^^

.. only:: html

	.. contents::
		:local:
		:depth: 1


view.templateRootPath
"""""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.view.templateRootPath

The root path for templates.

view.partialRootPath
""""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.view.partialRootPath

The root path for partials.

view.layoutRootPath
"""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.view.layoutRootPath

The root path for layouts.

settings.lightbox
"""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.settings.lightbox

Additional output for lightbox settings.

settings.enableLightbox
"""""""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.settings.enableLightbox

Switch if lightbox is enabled.

settings.cssFile
""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.settings.cssFile

Path to CSS File.

settings.image.width
""""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.settings.image.width

Width of single image.

settings.image.height
"""""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.settings.image.height

Width of single image.

settings.image.lightboxWidth
""""""""""""""""""""""""""""
:typoscript:`plugin.tx_sffilecollectiongallery.settings.image.lightboxWidth

The maxWidth of the lightbox image.

