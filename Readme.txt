General
-------

This extension allows you to secure files with apache2's mod_rewrite based on several criteria. The advantage of this solution is that the file paths stay the same, there is no PHP involved when serving the files (speed increase) and the headers are sent as if it were a normal apache request. For this the apache2-module mod_rewrite MUST be installed!
The criteria you can use to define whether a file is accessible are:
- is the file in a secured folder?
	-> In the standard .htaccess file it is assumed that all files inside fileadmin/, uploads/ and typo3temp/ should be secured!
- is the file in a public folder within the secured folders?
- is the user logged in as a BE_USER?
	-> By default BE_USERS may see all files. Anything else would be beyond the scope of this extension!
- is the file linked for the logged in user?
	-> For example within a <img>- or <a>-tag.
- is the file linked for any users not logged in?
	-> For example within a <img>- or <a>-tag.

Installation
------------

When installing this extensions, several things have to be done:
- complete and save the extension configuration
	-> Some important global values are stored there. Make sure you save it at least once (even if only default values are used)
- you must add the .htaccess section (described below) to your main .htaccess
- you must create at least one "delete" scheduler task
	-> this will clean up all the generated files
- you may create a "public indexer" scheduler task
	-> this is not mandatory, however the tx_securefiles_domain_model_public data records will not be considered in that case!

Scheduler Tasks
---------------




.htaccess
---------
In the .htaccess the main work is done! Unauthorized access is prohibited, so in order to have the extension do anything you must include the following lines.
You may change some of them where indicated, for example if your TYPO3 working directory is not the DOCUMENT_ROOT of the domain. In that case you may add the "RewriteBase /some/path" to indicate to apache2 the real directory TYPO3 is in. However please refer to the apache2 manual for more information on how to use RewriteBase.
As an alternative, you may inject the extra path in the conditions below.
You may also change the file suffixes that are excluded (per default .css and .js) and the folders that are secured by default.
However the RewriteEngine must be on!


#### Rewrite settings
RewriteEngine On

# start of secure_files section
# -----------------------------
#
# store the cookies in environment variables
#
# check if a BE_USER is logged in and get the hash
RewriteCond %{HTTP_COOKIE} tx_securefiles_be=([^;]+) [NC]
RewriteRule . - [env=tx_securefiles_be:%1]

# check if a FE_USER is logged in and get the hash
RewriteCond %{HTTP_COOKIE} tx_securefiles_fe=([^;]+) [NC]
RewriteRule . - [env=tx_securefiles_fe:%1]

# resolve the relative path
RewriteCond %{REQUEST_URI} ^([^\?]+)(\?.*)?$ [NC]
RewriteRule . - [env=tx_securefiles_rel:%1]

# realurl files don't exist and nc_staticfilecache files are not in the root folder
# we only care about the actually existing files
RewriteCond %{REQUEST_FILENAME} -f

# we only want to secure fileadmin/uploads/typo3temp
# all other folders should be typo3-standard stuff!
# should the TYPO3 not be situated in the root, the following rule has to be adapted
#   e.g. www.example.com/some/folder/<TYPO3 folder structure starts here> leads to
#   RewriteCond %{REQUEST_URI} ^/some/folder/(fileadmin|uploads|typo3temp)/
RewriteCond %{REQUEST_URI} ^/(fileadmin|uploads|typo3temp)/

# exclude any kind of files/folders next
# js/css files are usually public anyway
RewriteCond %{REQUEST_FILENAME} !\.(js|css)$

# and then check if the be user has global permission
# again, you might have to insert the /some/folder if you use a different root
RewriteCond %{DOCUMENT_ROOT}/typo3temp/tx_securefiles/be/%{ENV:tx_securefiles_be} !-f

# and then check if the fe user has the permission to the file
# again, you might have to insert the /some/folder if you use a different root
RewriteCond %{DOCUMENT_ROOT}/typo3temp/tx_securefiles/fe%{ENV:tx_securefiles_rel}/%{ENV:tx_securefiles_fe} !-f

# and then check if the file is public
# again, you might have to insert the /some/folder if you use a different root
RewriteCond %{DOCUMENT_ROOT}/typo3temp/tx_securefiles/pub%{ENV:tx_securefiles_rel} !-f

# lastly send forbidden in case neither be_user, fe_user permission or public file was detected!
RewriteRule . - [F]
#
# ---------------------------
# end of secure_files section
