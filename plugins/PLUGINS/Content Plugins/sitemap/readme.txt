Flynax Google & Yahoo XML Sitemap plugin generates XML Sitemap on the fly each time when it catches requests. This system of the Flynax Sitemap plugin provides a maximum of updated information on the content of your website as well as it does not require your actions for creation or updating of XML sitemap files.

Flynax XML Sitemap plugin has an option that limits the number of links for a sitemap and in the case when the number of links exceeds this limit it automatically divides the sitemap into two files.

With this Flynax XML Sitemap plugin you will help Google and Yahoo discover your website and let them know your content in details.


*************************************************************************************************************

NOTE!

1. Replace the string (if exists) in .htaccess file:
RewriteCond %{REQUEST_URI} !(.html)$ [NC]
with
RewriteCond %{REQUEST_URI} !(.html|.xml|.txt)$ [NC]

2. Add a new rule to the bottom of .htaccess file:
#Google and Yahoo sitemap
RewriteRule ^([a-z_]*)?sitemap([0-9]*).xml$ plugins/sitemap/sitemap.php?search=google&number=$2&mod=$1 [QSA,L]
RewriteRule ^yahoo-sitemap.txt$ /plugins/sitemap/sitemap.php?search=yahoo [QSA,L]
RewriteRule ^urllist.txt$ /plugins/sitemap/sitemap.php?search=urllist [QSA,L] 