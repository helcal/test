Creator/Updater - Rhett Lowe
Date - 7-6-12

1) Create brand folders and set brand GUID Ranges
    - Create file "brands.csv" in same directory as "create_bulk_info.php"
    - CSV Format - no headers
        Fields
        0   =>  'brand_real_name'   Real name (can have caps and spaces)
        1   =>  'brand_name'        Computer friendly version of name: simple
        2   =>  'guid_from'         doesn't matter but shouldn't interfere with other brands
        3   =>  'guid_to'           see guid_to
        4   =>  'orig_server'       lsl for lesliewatches.com or hg for highglowonline.com. if you add more add info for it in "/private/V2.0/updates/update_functions.php" 
    - Run "create_bulk_info.php". it is set to run above "/includes/" folder 

2) get all categories and Images from the remote servers
    - Create file "brands_transfer_info.csv" in "/private/V2.0/updates/" with "transfer_data.php"
    CSV Format - no headers
        0   =>  "ORIG_SITE"         lsl for lesliewatches.com or hg for highglowonline.com. if you add more add info for it in "/private/V2.0/updates/update_functions.php" 
        1   =>  "NEWURL"            URL Pertaining to root of "ORIG_SITE" EX: http://www.highglowonline.com/
        2   =>  "IMAGE_FOLDER"      folder ON REMOTE SERVER where images are held EX: if images are on "http://www.highglowonline.com/user_image/", IMAGE_FOLDER would be "user_images/"
        3   =>  "BRAND_FOLDER"      name of the folder on local server where images will be stored. it should be the 'brand_name' from 'brands.csv' above with trailing slash ('/').
        4   =>  "BRAND_NAME"        should be same as 'brand_name' in 'brands.csv' above
        5   =>  "BRAND_MASTER_ID"   get this number from the clients back-end. SIGN-IN>>Manage Brands>>Brand ID Column
        6   =>  "PARENT_GUID"       ID of the Main category for the brand. If you have JUST run "create_bulk_info.php" and added no new items yet you may use the number in the LAST GUID on the Manage Brands page.
        7   =>  "USERID"            Always '1' unless otherwise stated
        8   =>  "STARTING_CATID"    Goto Remote site (ex: http://www.highglowonline.com/) click on a category, and use the number at the end of the category. EX: in "http://www.highglowonline.com/category~Montblanc-Pens~466" the "STARTING_CATID" would be '466'
    - run "transfer_data.php" in '/private/V2.0/updates/'

3) Create Language tables in DB and generate All Thumbnails
    - Use the same "brands_transfer_info.csv" from above in the same location.
    - goto /includes/functions/functions.php and goto "function update_brand_configuration"
    - look for 4 if statements. Here si the first "if($brand_conf_arr[XSMALL]!=$img_xsmall)"
    - Do this loop
        i.      comment all 4 statements (including code inside)
        ii.     uncomment 1 statement and code
        iii.    set condition to run (don't delete original condition, just comment it)
        iv.     upload to server and run "transfer_lang_img.php"
            iv.v.   if there is a timeout and the code doesn't complete, shorten the runtime by breaking "brands_transfer_info.csv" into parts
        v.      reset conditional statement on if statement from step iii 
        vi.     re-comment if statement
        vii.    repeat steps ii-vi until all images are created
        viii.   uncomment all if statements so they are as if unmodified 
    - you might need to edit "function update_image_thumbnail" temporarily to complete extremely large brands.

DONE!!!!
