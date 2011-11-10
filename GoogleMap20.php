<?php
// -----------------------------------------------------------------------------------------------
// "Google Member Map" Mod for Simple Machines Forum (SMF) V2.0
// version 2.0.9
// -----------------------------------------------------------------------------------------------

// Are we calling this directly, umm lets just say no
if (!defined('SMF'))
	die('Hacking attempt...');

/**
 * Map()
 *
 * Traffic cop, checks permissions
 * calls the template which in turn calls this to request the xml file or js file to template inclusion
 *
 * @return
 */
function Map()
{
	global $db_prefix, $context, $txt, $smcFunc, $modSettings;

	// Are we allowed to view the map?
	isAllowedTo('googleMap_view');

	// create the pins for use, do it now so its available everywhere
	gm_buildpins();

	// Map using the internal XML File? Or the JS file?
	if (isset($_GET['sa']) && $_GET['sa'] == '.xml')
		return MapsXML();
	if (isset($_GET['sa']) && $_GET['sa'] == '.js')
		return MapsJS();

	// Get the template ready.... not really much else to do.
	// Lets find number of members that have placed their map pin
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) as TOTALFOUND
		FROM {db_prefix}members
		WHERE latitude <> false AND longitude <> false',
		array(
		)
	);

	// Pull the answer
	$totalSet = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	// If the total people who have set a pin is greater than googleMapsPinNumber,
	// We check for if we have any bounds that got passed from the JS,
	// If no bounds we just pick the number of googleMapsPinNumber to show...
	// and if we are under the max allowed, just show them all!
	if ($totalSet[0] >= $modSettings['googleMapsPinNumber'] && $modSettings['googleMapsPinNumber'] != 0)
		$txt['googlePinCount'] = $txt['googleMapThereare'] . ' <b>(' . $modSettings['googleMapsPinNumber'] . '+)</b> ' . $txt['googleMapPinsOnMap'];
	else
		$txt['googlePinCount'] = $txt['googleMapThereare'] . ' <b>(' . $totalSet[0] . ')</b> '. $txt['googleMapPinsOnMap'];

	// load up our template and style sheet
	loadTemplate('GoogleMap20','GoogleMap20');
	$context['sub_template'] = 'map';
	$context['page_title'] = $txt['googleMap'];
}

/**
 * MapsJS()
 *
 * creates the javascript file based on the admin settings
 * called from the map template file via map sa = js
 *
 * @return
 */
function MapsJS()
{
	global $db_prefix, $context, $scripturl, $txt, $modSettings;

	if (!isset($context['npin']))
		$context['npin'] = 'dddd';

	// Lets dump everything in the buffer and start clean and new and fresh
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();
	ob_start('ob_sessrewrite');

	echo '
	// arrays to hold copies of the markers and html used by the sidebar
	// because the function closure trick doesnt work there
	var gmarkers = [];
	var htmls = [];

	// This function picks up the click and opens the corresponding info window
	function myclick(i) {
		gmarkers[i].openInfoWindowHtml(htmls[i]);
	}

	function MakeMap() {
	// Globals.
	// Icon(s), and if gender is enabled, php will allow those to be defined.
	function cancelscroll() {
		window.event.returnValue = false;
		window.event.cancelBubble = true;
	}';

	// Build those magnificant pins !!
	$npin = $modSettings['npin'];
	$cpin = $modSettings['cpin'];
	$mpin = $modSettings['mpin'];
	$fpin = $modSettings['fpin'];

	// need this var, being lazy here and just checking again
	$mshd = (!empty($modSettings['googleMapsPinShadow'])) ? $mshd = '_withshadow' : $mshd = '';
	$cshd = (!empty($modSettings['googleMapsClusterShadow'])) ? $cshd = '_withshadow' : $cshd = '';

	// Validate the icon size is not to small
	$m_iconsize = (isset($modSettings['googleMapsPinSize']) && $modSettings['googleMapsPinSize'] > 19) ? $modSettings['googleMapsPinSize'] : 20;
	$c_iconsize = (isset($modSettings['googleMapsClusterSize']) && $modSettings['googleMapsClusterSize'] > 19) ? $modSettings['googleMapsClusterSize'] : 20;

	// set our member and pin sizes the image sizes are 21 X 34 for standard 40 X 37 with a shadow
	// we need to tweak the sizes based on these W/H ratios to maintain aspect ratio and overall size so that a mixed shadown./no appear the same size
	$m_icon_w = ($mshd != '') ? $m_iconsize*1.08 : $m_iconsize*.62;
	$m_icon_h = $m_iconsize;
	$c_icon_w = ($cshd != '') ? $c_iconsize*1.08 : $c_iconsize*.62;
	$c_icon_h = $c_iconsize;

	// Now set all those anchor points based on the icon size, icon at pin mid bottom, info mid top ish....
	$m_iconanchor_w = ($mshd != '') ? $m_icon_w/3 : $m_icon_w/2;
	$m_iconanchor_h = $m_icon_h;
	$m_infoanchor_h = $m_icon_h/4;
	$c_iconanchor_w = ($cshd != '') ? $c_icon_w/3 : $c_icon_w/2;
	$c_iconanchor_h = $c_icon_h;
	$c_infoanchor_h = $m_icon_h/4;

	echo '
	// Our standard pin
	var icon = new GIcon()
	icon.image = "http://chart.apis.google.com/chart' . $npin . '";
	icon.iconSize = new GSize(' . $m_icon_w . ',' . $m_icon_h . ');
	icon.iconAnchor = new GPoint(' . $m_iconanchor_w . ',' . $m_iconanchor_h . ');
	icon.infoWindowAnchor = new GPoint(' . $m_iconanchor_w . ',' . $m_infoanchor_h . ');

	// For that clustering thing!
	var clusterIcon = new GIcon();
	clusterIcon.image = "http://chart.apis.google.com/chart' . $cpin . '";
	clusterIcon.iconSize = new GSize(' . $c_icon_w . ',' . $c_icon_h . ');
	clusterIcon.iconAnchor = new GPoint(' . $c_iconanchor_w . ',' . $c_iconanchor_h .');
	clusterIcon.infoWindowAnchor = new GPoint(' . $c_iconanchor_w . ',' . $c_infoanchor_h . ');';

	if (!empty($modSettings['googleMapsPinGender']))
		echo '
	// for the ladies and gents
	var iconm = new GIcon();
	iconm.image = "http://chart.apis.google.com/chart' . $mpin . '";
	iconm.iconSize = new GSize(' . $m_icon_w . ',' . $m_icon_h . ');
	iconm.iconAnchor = new GPoint(' . $m_iconanchor_w . ',' . $m_iconanchor_h . ');
	iconm.infoWindowAnchor =  new GPoint(' . $m_iconanchor_w . ',' . $m_infoanchor_h . ');

	var iconf = new GIcon();
	iconf.image = "http://chart.apis.google.com/chart' . $fpin . '";
	iconf.iconSize = new GSize(' . $m_icon_w . ',' . $m_icon_h . ');
	iconf.iconAnchor = new GPoint(' . $m_iconanchor_w . ',' . $m_iconanchor_h . ');
	iconf.infoWindowAnchor =  new GPoint(' . $m_iconanchor_w . ',' . $m_infoanchor_h . ');';

	echo '
	if (GBrowserIsCompatible()) {
		// this variable will collect the html which will eventually be placed in the sidebar
		var sidebar_html = "";
		var i = 0;

		// A function to create the marker and set up the event window
		function createMarker(point, icon, name, html) {
			var marker = new GMarker(point, icon);
			GEvent.addListener(marker, "click", function() {
			map.getCenter(point);
			map.panTo(point);
			marker.openInfoWindowHtml(html);
			});

			// save the info we need to use later for the sidebar
			gmarkers[i] = marker;
			htmls[i] = html;
			name = name.replace(/\[b\](.*)\[\/b\]/gi, "<b>$1</b>");

			// add a line to the sidebar html';

	if (!empty($modSettings['googleSidebar']) && ($modSettings['googleSidebar'] == 'right'))
		echo '
			sidebar_html += \'<a href="javascript:myclick(\' + i + \')">\' + name + \'</a><br /> \';';

	if (!empty($modSettings['googleSidebar']) && ($modSettings['googleSidebar'] == 'none'))
		echo '
			sidebar_html += \'<a href="javascript:myclick(\' + i + \')">\' + name + \'</a>, \';';

	echo '
			// Now that we cached it lets return the marker....
			i++;
			return marker;
		}

		// create the map
		var map = new GMap2(document.getElementById("map"));
		map.addControl(new ' . $modSettings['googleNavType'] . '());
		map.addControl(new GMapTypeControl());
		map.disableScrollWheelZoom();
		map.enableContinuousZoom();

		// Lets load up the default long/lat/zoom for the map.
		map.setCenter(new GLatLng(' . $modSettings['googleMapsDefaultLat'] . ', ' . $modSettings['googleMapsDefaultLong'] . '), ' . $modSettings['googleMapsDefaultZoom'] . ',' . $modSettings['googleMapsType'] . ');

		// This is so we can try to cluster some of those pins together so the map does not get over loaded.
		var clusterer = new Clusterer(map);
		clusterer.icon = clusterIcon;
		clusterer.minMarkersPerClusterer = ' . $modSettings['googleMapsMinMarkerCluster'] . ';
		clusterer.maxVisibleMarkers = ' . $modSettings['googleMapsMaxVisMarker'] . ';
		clusterer.GridSize = ' . $modSettings['googleMapsMaxNumClusters'] . ';
		clusterer.MaxLinesPerInfoBox = ' . $modSettings['googleMapsMaxLinesCluster'] . ';

		// Read the data
		var request = GXmlHttp.create();
		request.open("GET", "' . $scripturl . '?action=googlemap;sa=.xml", true);

		request.onreadystatechange = function() {
			if (request.readyState == 4) {
				var xmlDoc = request.responseXML;

				// obtain the array of markers and loop through it
				var markers = xmlDoc.documentElement.getElementsByTagName("marker");

				for (var i = 0; i < markers.length; i++) {
					// obtain the attribues of each marker
					var lat = parseFloat(markers[i].getAttribute("lat"));
					var lng = parseFloat(markers[i].getAttribute("lng"));
					var point = new GLatLng(lat,lng);
					var html = markers[i].childNodes[0].nodeValue;
					var label = markers[i].getAttribute("label");

					// create the marker';

	if (!empty($modSettings['googleMapsPinGender']))
		echo '
					if (parseFloat(markers[i].getAttribute("gender")) == 0)
						var marker = createMarker(point, icon, label, html);
					if (parseFloat(markers[i].getAttribute("gender")) == 1)
						var marker = createMarker(point, iconm, label, html);
					if (parseFloat(markers[i].getAttribute("gender")) == 2)
						var marker = createMarker(point, iconf, label, html);';
	else
		echo '
					var marker = createMarker(point, icon, label, html);';

	if (!empty($modSettings['googleMapsEnableClusterer']))
		echo '
					clusterer.AddMarker(marker, label);';
	else
		echo '
					map.addOverlay(marker);';

	echo '
				}

			// put the assembled sidebar_html contents into the sidebar div
			document.getElementById("gooSidebar").innerHTML = sidebar_html;
			}
		}
		request.send(null);
	} else {
		alert("Sorry, the Google Maps API is not compatible with this browser");
	}
}
	setTimeout(\'MakeMap()\', 500);
';
	obExit(false);
}

/**
 * MapsXML()
 *
 * creates the xml data for use on the map
 * pin info window content
 * map sidebar layout
 *
 * @return
 */
function MapsXML()
{
	global $smcFunc, $context, $settings, $options, $scripturl, $txt, $modSettings, $user_info, $themeUser, $memberContext;

	// Lets dump everything in the buffer and start clean and new and fresh
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();
	ob_start('ob_sessrewrite');

	// XML Header
	header('Content-Type: application/xml; charset=' . (empty($context['character_set']) ? 'ISO-8859-1' : $context['character_set']));

	// Lets find number of members have set their map
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) as TOTALFOUND
		FROM {db_prefix}members
		WHERE latitude <> false AND longitude <> false',
		array(
		)
	);

	// Pull the answer and store it...
	$totalSet = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	// If the total people set there lat/long is greater than googleMapsPinNumber,
	// We check for if we have any bounds that got passed from the JS,
	// If no bounds we just pick the number of googleMapsPinNumber to show...
	// and if we are under the max allowed, just show them all!
	if ($totalSet[0] >= $modSettings['googleMapsPinNumber'] && $modSettings['googleMapsPinNumber'] != 0)
	{
		// Lets set this to nothing, just to be safe if we dont have variables...
		$sql_addon = "";

		// Check to see if we have any ranges before we add to the SQL statment...
		// This could stand to have a bit better security on it but its gonna be let by the side for now
		if ((isset($_GET['minX'])) && (isset($_GET['maxX'])) && (isset($_GET['minY'])) && (isset($_GET['maxY'])))
		{
			$sql_addon = ' AND latitude > ' . $_GET['minX'] . '
			AND latitude < ' . $_GET['maxX'] . '
			AND longitude > ' . $_GET['minY'] . '
			AND longitude < ' . $_GET['maxY'];
		}

		// Lets just make this simple for the query...
		$maxPins = (int) $modSettings['googleMapsPinNumber'];

		// Load the data up at random to the number set in the admin panel
		$query = 'SELECT id_member
		FROM {db_prefix}members
		WHERE latitude <> false AND longitude <> false
		' . $sql_addon . '
		ORDER BY RAND()
		LIMIT 0, {int:maxPins}';
	}
	else
	{
		// Looks like we passed under the max number of pins so we just load everyone...recently moved first
		$query = 'SELECT id_member, real_name, IF(pindate > {int:last_week}, pindate, 0) AS pindate
		FROM {db_prefix}members
		WHERE latitude <> false AND longitude <> false
		ORDER BY pindate DESC, real_name ASC';
	}

	// with the SQL request defined, lets make the query
	$last_week = time() - (7 * 24 * 60 * 60);
	$request = $smcFunc['db_query']('',
		$query,
		array(
			'last_week' => $last_week,
			'max_pins' => isset($maxPins) ? $maxPins : 0,
		)
	);

	// Ok this is block of code takes care of the entire load all member data into $themeUser/$memberContext on per # basis
	$temp = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$temp[] = $row['id_member'];
	$smcFunc['db_free_result']($request);

	// Load all of the data for these members who have pins
	loadMemberData($temp);
	foreach ($temp as $v)
		loadMemberContext($v);

	// Let's actually start making the XML
	echo '<?xml version="1.0" encoding="', $context['character_set'], '"?' . '>
	<markers>';
	if (isset($memberContext))
	{
		// to prevent the avatar being outside the popup window we need to set a max div height, since smf does not have
		// the avatar height availalbe, google will misrender the div until it gets the image in cache you see
		$div_height = max(isset($modSettings['avatar_max_height_external']) ? $modSettings['avatar_max_height_external'] : 0,isset($modSettings['avatar_max_height_upload']) ? $modSettings['avatar_max_height_upload'] : 0);

		// Assuming we have data to work with...
		foreach ($memberContext as $marker)
		{
			$datablurb = '
		<div class="googleMaps">
			<h4>
				<a href="' . $marker['online']['href'] . '">
					<img src="' . $marker['online']['image_href'] . '" alt="' . $marker['online']['text'] . '" /></a>
				<a href="' . $marker['href'] . '">' . $marker['name'] . '</a>
			</h4>';

			// avatar?
			if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($marker['avatar']['image']))
				$datablurb .= '
				<div class="floatright" style="height:' . $div_height . 'px">' . $marker['avatar']['image'] . '<br /></div>';

			// user info section
			$datablurb .= '
			<div class="floatleft">
				<ul class="reset">';

			// Show the member's primary group (like 'Administrator') if they have one.
			if (!empty($marker['group']))
				echo '
					<li class="membergroup">', $marker['group'], '</li>';

			// Show the post group if and only if they have no other group or the option is on, and they are in a post group.
			if ((empty($settings['hide_post_group']) || $marker['group'] == '') && $marker['post_group'] != '')
				$datablurb .= '
					<li class="postgroup">' . $marker['post_group'] . '</li>';

			// groups stars
			$datablurb .= '
					<li class="stars">' . $marker['group_stars'] . '</li>';

			// show the title, if they have one
			if (!empty($marker['title']) && !$user_info['is_guest'])
			$datablurb .= '
					<li class="title">' . $marker['title'] . '</li>';

			// Show the profile, website, email address, and personal message buttons.
			if ($settings['show_profile_buttons'])
			{
				$datablurb .= '
					<li>';

				// messaging icons
				$datablurb .= '
						<ul>
							<li>' . $marker['icq']['link'] . '</li>
							<li>' . $marker['msn']['link'] . '</li>
							<li>' . $marker['aim']['link'] . '</li>
							<li>' . $marker['yim']['link'] . '</li>
						</ul>
						<ul>';

				// Don't show an icon if they haven't specified a website.
				if ($marker['website']['url'] != '' && !isset($context['disabled_fields']['website']))
					$datablurb .= '
							<li>
								<a href="' . $marker['website']['url'] . '" title="' . $marker['website']['title'] . '" target="_blank" class="new_win">' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/www_sm.gif" height="16" alt="' . $marker['website']['title'] . '" border="0" />' : $txt['www']) . '
							</li>';

				// Don't show the email address if they want it hidden.
				if (in_array($marker['show_email'], array('yes', 'yes_permission_override', 'no_through_forum')))
					$datablurb .= '
							<li>
								<a href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $marker['id'] . '">' . ($settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/email_sm.gif" alt="' . $txt['email'] . '" height="16" title="' . $txt['email'] . '" />' : $txt['email']) . '
							</li>';

				// Show the PM tag
				$datablurb .= '
							<li>
								<a href="' . $scripturl . '?action=pm;sa=send;u=' . $marker['id'] . '">';
				$datablurb .= $settings['use_image_buttons'] ? '<img src="' . $settings['images_url'] . '/im_' . ($marker['online']['is_online'] ? 'on' : 'off') . '.gif" height="16" border="0" />' : ($marker['online']['is_online'] ? $txt['pm_online'] : $txt['pm_offline']);
				$datablurb .= '
							</li>
						</ul>
					</li>';
			}

			$datablurb .= '
				</ul>
			</div>
		</div>';

			// Let's bring it all together...
			$markers = '<marker lat="' . round($marker['googleMap']['latitude'], 6) . '" lng="' . round($marker['googleMap']['longitude'], 6) . '" ';

			if ($marker['gender']['name'] == $txt['male'])
				$markers .= 'gender="1"';
			elseif ($marker['gender']['name'] == $txt['female'])
				$markers .= 'gender="2"';
			else $markers .= 'gender="0"';

			if (isset($modSettings['googleBoldMember']) && $modSettings['googleBoldMember'] && $marker['googleMap']['pindate'] >= $last_week)
				$markers .= ' label="[b]' . $marker['name'] . '[/b]"><![CDATA[' . $datablurb . ']]></marker>';
			else
				$markers .= ' label="' . $marker['name'] . '"><![CDATA[' . $datablurb . ']]></marker>';

			echo $markers;
		}
	}
	echo '
	</markers>';

	// Ok we should be done with output, dump it to user...
	obExit(false);
}

/**
 * ShowKML()
 *
 * @return
 */
function ShowKML()
{
	global $smcFunc, $settings, $options, $context, $scripturl, $txt, $modSettings, $user_info, $mbname, $themeUser, $memberContext;

	// Are we allowed to view the map?
	isAllowedTo('googleMap_view');

	// If it's not enabled, die.
	if (empty($modSettings['KMLoutput_enable']))
		obExit(false);

	// This is an kml file, its like an XML file...
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']))
		@ob_start('ob_gzhandler');
	else
		ob_start();
	ob_start('ob_sessrewrite');

	// Lets make sure its sent as KML
	header('Content-type: application/keyhole;');

	// It will be called ourforumname.kml
	header('Content-Disposition: attachment; filename="' . $mbname . '.kml"');

	// Load the data up, and seeing how its google earth, lets just send everything.
	// If we get complaints about this, then we shall have to figure out how to limit it.
	$request = $smcFunc['db_query']('', '
		SELECT id_member
		FROM {db_prefix}members
		WHERE latitude <> false AND longitude <> false',
		array(
		)
	);

	// Ok this is block of code takes care of the entire load all member data into $memberContext on per # basis
	$temp = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$temp[] = $row['id_member'];

	loadMemberData($temp);
	foreach ($temp as $v)
		loadMemberContext($v);

	$smcFunc['db_free_result']($request);

	// Start building the output
	echo '<?xml version="1.0" encoding="', $context['character_set'], '"?' . '>
	<kml xmlns="http://earth.google.com/kml/2.0">
	<Folder>
		<name>' . $mbname . '</name>
		<open>0</open>';
	if (isset($memberContext))
	{
		// Assuming we have data to work with...
		foreach ($memberContext as $marker)
		{
			echo '
		<Placemark id="' . $marker['name'] . '">
			<description>
				<![CDATA[';
			echo '
				<table class="googleMaps" border="0" style="white-space: nowrap;">
					<tr>
						<td style="white-space: nowrap;" align="left"><a href="' . $marker['online']['href'] . '"><img src="' . $marker['online']['image_href'] . '" alt="' . $marker['online']['text'] . '" class="avatar" height="16" border="0" /></a> <a href="' . $marker['href'] . '">' . $marker['name'] . '</a></td>';
			if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($marker['avatar']['href']))
				echo '
						<td rowspan="3"><img src="' . $marker['avatar']['href'] . '" height="80" /></td>';
			echo '</tr><tr>
						<td style="white-space: nowrap;">' . $marker['title'] . '</td>
   					</tr><tr>
						<td style="white-space: nowrap;" align="center">
							' . $marker['icq']['link'] . '
							' . $marker['aim']['link'] . '
							' . $marker['yim']['link'] . '
							' . $marker['msn']['link'] . '
						</td>
					</tr><tr>
						<td style="white-space: nowrap;" colspan="2" align="left">';
			if (($marker['website']['url'] != '') && ($marker['website']['title'] != ''))
				echo '
							<a href="' . $marker['website']['url'] . '">' . $marker['website']['title'] . '</a>';
			echo '
						</td>
					</tr><tr>
						<td style="white-space: nowrap;" colspan="2" align="left">' . $marker['blurb'] . '</td>
					</tr>
				</table>';
			echo ']]>
			</description>
			<name>' . $marker['name'] . '</name>
			<LookAt>
				<longitude>' . round($marker['googleMap']['longitude'], 6) . '</longitude>
				<latitude>' . round($marker['googleMap']['latitude'], 6) . '</latitude>
				<range>15000</range>
			</LookAt>
			<styleUrl>root://styles#default+icon=0x304</styleUrl>';
			if (!empty($modSettings['googleMapsPinGender']))
			{
				echo '
			<Style>
				<IconStyle>
					<color>';
				if ($marker['gender']['name'] == 'Male')
					echo 'ffff6464';
				elseif ($marker['gender']['name'] == 'Female')
					echo 'ff6464ff';
				else
					echo 'ff64ff64';
				echo '
					</color>
				</IconStyle>
			</Style>';
			}
			echo '
			<Point>
				<extrude>1</extrude>
				<altitudeMode>clampToGround</altitudeMode>
				<coordinates>' . round($marker['googleMap']['longitude'], 6) . ',' . round($marker['googleMap']['latitude'], 6) . ',2</coordinates>
			</Point>
		</Placemark>';
		}
	}
	echo '
	</Folder>
</kml>';

	// Ok done, should send everything now..
	obExit(false);
}

/**
 * gm_buildpins()
 *
 * Does the majority of work in determining how the map pin should look based on admin settings
 *
 * @return
 */
function gm_buildpins()
{
	global $modSettings;

	// lets work out all those options
	$modSettings['googleMapsClusterBackground'] = gm_validate_color('googleMapsClusterBackground','FF66FF');
	$modSettings['googleMapsPinBackground'] = gm_validate_color('googleMapsPinBackground','66FF66');
	$modSettings['googleMapsClusterForeground'] = gm_validate_color('googleMapsClusterForeground','202020');
	$modSettings['googleMapsPinForeground'] = gm_validate_color('googleMapsPinForeground','202020');

	// what kind of pins have been chosen
	$mpin = gm_validate_pin('googleMapsPinStyle','d_map_pin_icon');
	$cpin = gm_validate_pin('googleMapsClusterStyle','d_map_pin_icon');

	// shall we add in shadows
	$mshd = (isset($modSettings['googleMapsPinShadow']) && $modSettings['googleMapsPinShadow']) ? $mshd = '_withshadow' : $mshd = '';
	$cshd = (isset($modSettings['googleMapsClusterShadow']) && $modSettings['googleMapsClusterShadow']) ? $cshd = '_withshadow' : $cshd = '';

	// set the member and cluster pin styles, icon or text
	if ($mpin == 'd_map_pin_icon')
		$mchld = ((isset($modSettings['googleMapsPinIcon']) && trim($modSettings['googleMapsPinIcon']) != '') ? $modSettings['googleMapsPinIcon'] : 'info');
	elseif ($mpin == 'd_map_pin_letter')
		$mchld = (isset($modSettings['googleMapsPinText']) && trim($modSettings['googleMapsPinText']) != '') ? $modSettings['googleMapsPinText'] : '';
	else
	{
		$mpin = 'd_map_pin_letter';
		$mchld = '';
	}

	if ($cpin == 'd_map_pin_icon')
		$cchld = ((isset($modSettings['googleMapsClusterIcon']) && trim($modSettings['googleMapsClusterIcon']) != '') ? $modSettings['googleMapsClusterIcon'] : 'info');
	elseif ($cpin == 'd_map_pin_letter')
		$cchld = (isset($modSettings['googleMapsClusterText']) && trim($modSettings['googleMapsClusterText']) != '') ? $modSettings['googleMapsClusterText'] : '';
	else
	{
		$cpin = 'd_map_pin_letter';
		$cchld = '';
	}

	// and now for the colors
	$mchld .= '|' . $modSettings['googleMapsPinBackground'] . '|' . $modSettings['googleMapsPinForeground'];
	$cchld .= '|' . $modSettings['googleMapsClusterBackground'] . '|' . $modSettings['googleMapsClusterForeground'];

	// Build those magnificant pins !!
	$modSettings['npin'] = '?chst=' . $mpin . $mshd . '&chld=' . $mchld;
	$modSettings['cpin'] = '?chst=' . $cpin . $cshd . '&chld=' . $cchld;
	if ($mpin == 'd_map_pin_icon')
	{
		$modSettings['fpin'] = '?chst=d_map_pin_icon' . $mshd . '&chld=WCfemale|FF0099';
		$modSettings['mpin'] = '?chst=d_map_pin_icon' . $mshd . '&chld=WCmale|0066FF';
	}
	else
	{
		$modSettings['fpin'] = '?chst=d_map_pin_letter' . $mshd . '&chld=|FF0099|'.$modSettings['googleMapsPinForeground'];
		$modSettings['mpin'] = '?chst=d_map_pin_letter' . $mshd . '&chld=|0066FF|'.$modSettings['googleMapsPinForeground'];
	}
	return;
}

/**
 * gm_validate_color()
 *
 * Makes sure we have a 6digit hex for the color definitions or sets a default value
 *
 * @param mixed $color
 * @param mixed $default
 * @return
 */
function gm_validate_color($color,$default)
{
	global $modSettings;

	// no leading #'s please
	if (substr($modSettings[$color], 0, 1) == '#')
		$modSettings[$color] = substr($modSettings[$color],1);

	// is it a hex
	if (!preg_match('/^[a-f0-9]{6}$/i', $modSettings[$color]))
		$modSettings[$color] = $default;

	return strtoupper($modSettings[$color]);
}

/**
 * gm_validate_pin()
 *
 * outputs the correct goggle chart pin type based on selection
 *
 * @return
 */
function gm_validate_pin($area,$default)
{
	global $modSettings;

	if (isset($modSettings[$area]))
	{
		switch ($modSettings[$area])
		{
			case 'plainpin':
				$pin = 'd_map_pin';
				break;
			case 'textpin':
				$pin = 'd_map_pin_letter';
				break;
			case 'iconpin':
				$pin = 'd_map_pin_icon';
				break;
			default:
				$pin = 'd_map_pin_icon';
		}
	}
	else
		$pin = $default;

	return $pin;
}
?>