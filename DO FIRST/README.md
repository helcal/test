**iShowcase Skeleton
***

This is a blank used to creat the basic starting point for an iShowcase website.
This brank will not work without the iShowcase Core. If you forget to install the core in the /includes/ folder the site wilnot run.
Many files have been left empty to show where they go as examples. You might not use all files and should ask questions if you don't know what one does, why it is there, what goes in it, or if your site will use it.

***

The .gitignores are set to ignore env.php and .htaccess in their correct locations. there is a copy of a basic env.php, .htaccess, and basic DB for you to install upon cloning.

***env.php 
public_html/private/V2.0/config/env.php
it must be included in the config.php in the same folder/

***.htaccess
public_html/.htaccess

***

Do a search for the word brand. You will have to modify "brand" to the brand name you wish to use
EX: for Moyer fine Jewelers the /home/brand/public_html is modified to /home/moyerish/public_html
EX: for Moyer Fine Jewelers the public_html/brand.php file is modified to moyer.php -- as is the public_html/brand/ folder >> public_html/moyer/

Lastly, Make sure NOT to push this repository back to git unless you have changed the git origin url.
***DONNOT PUSH TO ISHOWCASE/SKELETON REPO

When finished with the clone, you should NEVER merge with original skeleton.
Delete the .git/ directory at the root of this repo. ( "rm -fR .git" )
Then do a git init to reinitialize new repo.