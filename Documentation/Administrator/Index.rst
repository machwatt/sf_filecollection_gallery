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