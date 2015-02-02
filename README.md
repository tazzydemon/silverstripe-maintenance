SilverStripe Under Maintenance Page Module
===========================================

Maintainer Contacts
-------------------
*  Julian Warren (tazymaybedebil_AT_gmail(dot)com)


Requirements
------------
* SilverStripe 3.0

Documentation
-------------
This is essentially just a different version of the underconstruction module (https://github.com/frankmullenger/silverstripe-underconstruction) and achieves practically the same thing. This module will create a static HTML error page in the assets folder when it is installed. Then when a non admin user tries to visit the website the error page will be displayed. Once an admin user has logged in they will be able to browse the website at will.

Additionally, when the under maintenance page is displayed it responds to the browser with a 503 - Service Unavailable HTTP status code.

This module could easily be changed to generate any kind of maintenance page and return any kind of HTTP status code.

I have updated the module so that under construction pages can be turned on or off via the SiteConfig->Access tab for convenience.

Installation Instructions
-------------------------
1. Place this directory in the root of your SilverStripe installation.
2. Visit yoursite.com/dev/build to rebuild the database and create the undermaintenance page.
3. Check that the under construction page was created by looking for an error-503.html page in the /assets folder.
4. If you want to update the error page at any time or indeed simply wish to edit the text then go to the undermaintenance page in the site tree edit the content and save as usual. The error-503.html file will be regenerated.
5. If your site is in dev mode then the under construction page will not be shown.
6. You will need to go to the SiteConfig->Access tab and tick the checkbox to 'Display an under construction page?'.


Usage Overview
--------------
1. Install the module using instructions above.
2. When you no longer want to display the undermaintenance page you can either remove this module (by removing the 'undermaintenance' directory) and running /dev/build or uncheck the relevant checkbox in SiteConfig->Access tab.


