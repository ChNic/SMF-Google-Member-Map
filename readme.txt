[hr]
[center][size=16pt][b]Google Member Map[/b][/size]
[url=http://custom.simplemachines.org/mods/index.php?action=search;author=11359][b]By Spuds[/b][/url]
[/center]
[hr]

COMPATIBLE WITH SMF SMF 2.0

[color=blue][b][size=12pt][u]Introduction[/u][/size][/b][/color]
This mod installs a member map to your website which allows your members to pin their location on a map. It uses Google Maps 2.0 API to generate the map and place 'Push" pins. Before installing it is recommended to make a backup of your member and settings tables for your forum. Once installed you will need to go to Configuration -> Modification Settings -> Member Map to enable it and enter your google map API key. The key is required by the Google Maps, and can be acquired at for free at http://www.google.com/apis/maps/signup.html

Upon sign up you will need to enter your sites URL, for example, if your Forum is at http://www.example.com/forums/
In the google map sign up box enter exactly that, do not include any additional file name, just the directory path in the url.

[color=blue][b][size=12pt][u]Installation[/u][/size][/b][/color]
Simply install this package through the package manager located in your Administration Panel. Manual edits may be required if your site uses a custom theme.

How Do I Use This Mod?
In your admin panel you will need to enable it and enter your google map API key to allow it to function correctly. Next, your members will need to edit their profiles and place a pin on the map to show their location and save their profile. That pin will then display on the main member map page. The admin will also need to set the map permissions so users can see and use it. 

[color=blue][b][size=12pt][u]Support[/u][/size][/b][/color]
Please use the Google Member Map modification thread for support with this modification.

[color=blue][b][size=12pt][u]Changelog[/u][/size][/b][/color]
2.09
+ New pins are now sorted to the top of the list
+ resutucted the info bubble to use the theme classes
! added a default height to the info bubble to prevent avatars from breaking out
! fixed the home text string
! general code cleanup

2.08
! fixed undefined index error in the googlemap20 template
+ updated the template html code to remove several unneeded tables
! removed several xhtml validation errors

2.07
! fixed error where setting maximum visible pins to zero should have been unlimited and was in fact really zero
+ updated some of the language txt strings to improve readability
+ RC5 Support

2.06c
! fixed issue where during the install the txt strings were being written in to the target files twice
2.06b
! fixed missing text name caused by 2.06 googleMapsPinrForeground change
! fixed admin panel so it can accept the hex color numbers, was cast as int only
+ small change to the .css file
2.06a
! fixed sidebar right/none mixup caused by moving of txt strings
! fixed looking for lower case css file while saving it as a camel case file

2.06
These changes are ONLY for the 2.0 branch, the 1.1x branch has not been updated 
! Moved additional text strings to language files
! Gender pin will follow the user pin setting of plain or icon, originally was fixed to icon only
! Fixed some spelling
! Fixed incorrect variable googleMapsPinrForeground, should have been googleMapsPinForeground

2.05
These changes are ONLY for the 2.0 branch, the 1.1x branch has not been updated 
! Fixed 0,0 issue where members could not remove a pin they had set
+ Map control (pan / zoom) style is now selectable
! Moved map pin graphic source from defunct goggle service to goggle charts
+ Admin controls for Pin size, Pin color, Plain, Text and icon pins & Drop Shadows (member and cluster pins)
+ Option to see the latest pin adds/moves in the sidebar
! Fixed template layouts to use curve style in more places
! Member Map location will appear under profile summary if they have set a pin and disappear if they remove their pin
+ Added additional information to the map pin info pop-up, webpage, email, pm
+ updated the install to use proper 2.0 database code, separate language files.
+ separated the style sheet to its own file vs in line
+ Improved integration with profile pages so it matches the page style (dd/dt)
+ Added total count of pins to the map page
- Removed global requirements from language file
- Removed defunct package server from install
+ Consolidated the 1.1x & 2.0 installs into a single package