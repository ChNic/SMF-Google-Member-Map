[center][color=red][size=16pt][b]Google Member Map 2.0.10[/b][/size][/color]
[/center]

[b]Credits:[/b]
brianjw - [i]Bug Fixer / New Creator for mod[/i]
StormLrd - [i]Google Map Layout Developer[/i] (created the Pinned Member's part of the mod.)
2Ntense - [i]Feature Adder / Added many new features in 0.2[/i]
Nao - [i]Upgrader / Completely coded the mod to work with SMF 2.0[/i]
BlueDevil - [i]Converted to RC2, RC3[/i]
Spuds - [i]Assumed Mod ownership for RC4 onward[/i]

[hr]

[color=blue][b][size=12pt][u]Introduction[/u][/size][/b][/color]

This mod installs a member map to your website where your users can place a map push pin to show their location. It uses Google Maps API to make the map and put pins on the map.

Before installing it is recommended to make a backup of member and settings tables for your forum since it modifies those talbes. 

Once installed you will need to go to Features and Options to enable it, along with entering a Google map API key. The key is required by the Google Maps API, and can be acquired for free at, http://www.google.com/apis/maps/signup.html

Upon sign up at Google you will need to put in your sites URL, for example, if your Forum is located at 
	http://www.example.com/forums/
Then in the Google API sign up box enter exactly that, do not include any file name just the directory path in the url.

Google Earth can also make use of the pin data. This mod allows for the export of user pin data in to a .kml file for those that want to use Google Earth to see their member location.  Simply add a network link in Google Earth to point at http://www.example.com/forums/index.php?action=.kml to get the data for Google Earth.  The capability to export .kml files is controlled by the permission to view the map, and keep in mind Google Earth will appear as a guest to your forum!