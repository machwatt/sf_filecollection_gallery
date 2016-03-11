.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

More information about the configuration can be found at the 'Configuration' part.

Installation
------------

To install the extension, perform the following steps:

#. Go to the Extension Manager
#. Install the extension
#. Load the static template


Templating
----------
If you want to provide different layouts and switch them via the "appearance tab"
you can access them in your template (https://github.com/Skyfillers/sf_filecollection_gallery/pull/55):
		{contentObjectData.layout}

In case you use the nested gallery from a recursive FileCollection feature, you may access the folderName of the current
object via: (f.e. if you want to render a gallery title from the current folder name)
        {object.galleryFolderName}


It is possible to use the fallback templating feature of TYPO3 6.2::

		plugin.tx_sffilecollectiongallery {
			view {
				templateRootPaths {
					0 = EXT:sf_filecollection_gallery/Resources/Private/Templates/
					1 = fileadmin/your/path/templates
				}
				partialRootPaths {
					0 = EXT:sf_filecollection_gallery/Resources/Private/Partials/
					1 = fileadmin/your/path/partials
				}
				layoutRootPaths {
					0 = EXT:sf_filecollection_gallery/Resources/Private/Layouts/
					1 = fileadmin/your/path/layouts
				}
			}
		}