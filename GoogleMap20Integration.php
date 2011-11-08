<?php
// These functions are our integration hooks

function imb_googlemap(&$buttons)
{		
	// Menu Button hook, integrate_menu_buttons, called from subs.php
	// used to add top menu buttons 
	global $txt, $scripturl, $modSettings;

		// where do we want to place this new button
		$insert_after = 'calendar';
		$counter = 0;
		
		// find the location in the buttons array
		foreach ($buttons as $area => $dummy)
			if (++$counter && $area == $insert_after)
				break;

		// Define the new menu item(s)
		$new_menu = array(
			'googlemap' => array(
				'title' => $txt['googleMap'],
				'href' => $scripturl . '?action=googlemap',
				'show' => !empty($modSettings['googleMapsEnable']) && allowedTo('googleMap_view'),
				'sub_buttons' => array(),
			)
		);

		// Insert the new items in the existing array with array-a-matic ...it slices, it dices, it puts it back together
		$buttons = array_merge(array_slice($buttons, 0, $counter), array_merge($new_menu, array_slice($buttons, $counter)));
}

function ilp_googlemap(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{	
	// Permissions hook, integrate_load_permissions, called from ManagePermissions.php
	// used to add new permisssions
	$permissionList['membergroup']['googleMap_view'] = array(false, 'general', 'view_basic_info');
	$permissionList['membergroup']['googleMap_place'] = array(false, 'general', 'view_basic_info');
}

function ia_googlemap(&$actionArray)
{
	// Actions hook, integrate_actions, called from index.php
	// used to add new actions to the system
	$actionArray = array_merge($actionArray,array(
		'googlemap' => array('GoogleMap20.php', 'Map'),
		'membermap' => array('GoogleMap20.php', 'Map'),
		'.kml' => array('GoogleMap20.php', 'ShowKML'))
	);
}

function iaa_googlemap(&$admin_areas)
{
	// Admin Hook, integrate_admin_areas, called from Admin.php
	// used to add/modify admin menu areas
	global $txt;
	$admin_areas['config']['areas']['modsettings']['subsections']['googlemap'] = array($txt['googleMap']);
}

function imm_googlemap(&$sub_actions)
{
	// Modifications hook, integrate_modify_modifications, called from ManageSettings.php
	// used to add new menu screens areas.
	$sub_actions['googlemap'] = 'ModifyGoogleMapSettings';
}

function ModifyGoogleMapSettings()
{
	global $txt, $scripturl, $context, $settings, $sc;
	
	$context[$context['admin_menu_name']]['tab_data']['tabs']['googlemap']['description'] = $txt['googlemapdesc'];
	$config_vars = array(
			// Map - On or off?
			array('check', 'googleMapsEnable'),
			array('check', 'googleMapsEnableLegend'),
			array('select', 'googleSidebar', array('none' => $txt['nosidebar'], 'right' => $txt['rightsidebar'])),
		'',
			// Key, pins static/gender/membergroup
			array('text', 'googleMapsKey'),
			array('check', 'KMLoutput_enable'),
			array('int', 'googleMapsPinNumber'),
			array('select', 'googleMapsType', array('G_NORMAL_MAP' => $txt['map'], 'G_SATELLITE_MAP' => $txt['satellite'], 'G_HYBRID_MAP' => $txt['hybrid'])),
			array('select', 'googleNavType', array('GLargeMapControl3D' => $txt['glargemapcontrol3d'], 'GLargeMapControl' => $txt['glargemapcontrol'], 'GSmallMapControl' => $txt['gsmallmapcontrol'], 'GSmallZoomControl3D' => $txt['gsmallzoomcontrol3d'], 'GSmallZoomControl' => $txt['gsmallzoomcontrol'])),
			array('check', 'googleBoldMember'),
		'',
			// Default Location/Zoom
			array('float', 'googleMapsDefaultLat', '25'),
			array('float', 'googleMapsDefaultLong', '25'),
			array('int', 'googleMapsDefaultZoom'),
		'',
			// Member Pin Style
			array('check', 'googleMapsPinGender'),
			array('text', 'googleMapsPinBackground', '6'),
			array('text', 'googleMapsPinForeground', '6'),
			array('select', 'googleMapsPinStyle', array('plainpin' => $txt['plainpin'], 'textpin' => $txt['textpin'], 'iconpin' => $txt['iconpin'])),
			array('check', 'googleMapsPinShadow'),
			array('int', 'googleMapsPinSize', '2'),
			array('text', 'googleMapsPinText'),
			array('select', 'googleMapsPinIcon', 
				array(
					'academy' => $txt['academy'],
					'activities' => $txt['activities'],
					'airport' => $txt['airport'],
					'amusement' => $txt['amusement'],
					'aquarium' => $txt['aquarium'],
					'art-gallery' => $txt['art-gallery'],
					'atm' => $txt['atm'],
					'baby' => $txt['baby'],
					'bank-dollar' => $txt['bank-dollar'],
					'bank-euro' => $txt['bank-euro'],
					'bank-intl' => $txt['bank-intl'],
					'bank-pound' => $txt['bank-pound'],
					'bank-yen' => $txt['bank-yen'],
					'bar' => $txt['bar'],
					'barber' => $txt['barber'],
					'beach' => $txt['beach'],
					'beer' => $txt['beer'],
					'bicycle' => $txt['bicycle'],
					'books' => $txt['books'],
					'bowling' => $txt['bowling'],
					'bus' => $txt['bus'],
					'cafe' => $txt['cafe'],
					'camping' => $txt['camping'],
					'car-dealer' => $txt['car-dealer'],
					'car-rental' => $txt['car-rental'],
					'car-repair' => $txt['car-repair'],
					'casino' => $txt['casino'],
					'caution' => $txt['caution'],
					'cemetery-grave' => $txt['cemetery-grave'],
					'cemetery-tomb' => $txt['cemetery-tomb'],
					'cinema' => $txt['cinema'],
					'civic-building' => $txt['civic-building'],
					'computer' => $txt['computer'],
					'corporate' => $txt['corporate'],
					'fire' => $txt['fire'],
					'flag' => $txt['flag'],
					'floral' => $txt['floral'],
					'helicopter' => $txt['helicopter'],
					'home' => $txt['home'],
					'info' => $txt['info'],
					'landslide' => $txt['landslide'],
					'legal' => $txt['legal'],
					'location' => $txt['location'],
					'locomotive' => $txt['locomotive'],
					'medical' => $txt['medical'],
					'mobile' => $txt['mobile'],
					'motorcycle' => $txt['motorcycle'],
					'music' => $txt['music'],
					'parking' => $txt['parking'],
					'pet' => $txt['pet'],
					'petrol' => $txt['petrol'],
					'phone' => $txt['phone'],
					'picnic' => $txt['picnic'],
					'postal' => $txt['postal'],
					'repair' => $txt['repair'],
					'restaurant' => $txt['restaurant'],
					'sail' => $txt['sail'],
					'school' => $txt['school'],
					'scissors' => $txt['scissors'],
					'ship' => $txt['ship'],
					'shoppingbag' => $txt['shoppingbag'],
					'shoppingcart' => $txt['shoppingcart'],
					'ski' => $txt['ski'],
					'snack' => $txt['snack'],
					'snow' => $txt['snow'],
					'sport' => $txt['sport'],
					'star' => $txt['star'],
					'swim' => $txt['swim'],
					'taxi' => $txt['taxi'],
					'train' => $txt['train'],
					'truck' => $txt['truck'],
					'wc-female' => $txt['wc-female'],
					'wc-male' => $txt['wc-male'],
					'wc' => $txt['wc'],
					'wheelchair' => $txt['wheelchair'],
				)
			),
		'',
			// Clustering Options
			array('check', 'googleMapsEnableClusterer'),
			array('int', 'googleMapsMinMarkerCluster'),
			array('int', 'googleMapsMaxVisMarker'),
			array('int', 'googleMapsMaxNumClusters'),
			array('int', 'googleMapsMaxLinesCluster'),
		'',
			// Clustering Style
			array('text', 'googleMapsClusterBackground', '6'),
			array('text', 'googleMapsClusterForeground', '6'),
			array('select', 'googleMapsClusterStyle', array('plainpin' => $txt['plainpin'], 'textpin' => $txt['textpin'], 'iconpin' => $txt['iconpin'])),
			array('check', 'googleMapsClusterShadow'),
			array('int', 'googleMapsClusterSize', '2'),
			array('text', 'googleMapsClusterText'),
			array('select', 'googleMapsClusterIcon', 
				array(
					'academy' => $txt['academy'],
					'activities' => $txt['activities'],
					'airport' => $txt['airport'],
					'amusement' => $txt['amusement'],
					'aquarium' => $txt['aquarium'],
					'art-gallery' => $txt['art-gallery'],
					'atm' => $txt['atm'],
					'baby' => $txt['baby'],
					'bank-dollar' => $txt['bank-dollar'],
					'bank-euro' => $txt['bank-euro'],
					'bank-intl' => $txt['bank-intl'],
					'bank-pound' => $txt['bank-pound'],
					'bank-yen' => $txt['bank-yen'],
					'bar' => $txt['bar'],
					'barber' => $txt['barber'],
					'beach' => $txt['beach'],
					'beer' => $txt['beer'],
					'bicycle' => $txt['bicycle'],
					'books' => $txt['books'],
					'bowling' => $txt['bowling'],
					'bus' => $txt['bus'],
					'cafe' => $txt['cafe'],
					'camping' => $txt['camping'],
					'car-dealer' => $txt['car-dealer'],
					'car-rental' => $txt['car-rental'],
					'car-repair' => $txt['car-repair'],
					'casino' => $txt['casino'],
					'caution' => $txt['caution'],
					'cemetery-grave' => $txt['cemetery-grave'],
					'cemetery-tomb' => $txt['cemetery-tomb'],
					'cinema' => $txt['cinema'],
					'civic-building' => $txt['civic-building'],
					'computer' => $txt['computer'],
					'corporate' => $txt['corporate'],
					'fire' => $txt['fire'],
					'flag' => $txt['flag'],
					'floral' => $txt['floral'],
					'helicopter' => $txt['helicopter'],
					'home' => $txt['home1'],
					'info' => $txt['info'],
					'landslide' => $txt['landslide'],
					'legal' => $txt['legal'],
					'location' => $txt['location'],
					'locomotive' => $txt['locomotive'],
					'medical' => $txt['medical'],
					'mobile' => $txt['mobile'],
					'motorcycle' => $txt['motorcycle'],
					'music' => $txt['music'],
					'parking' => $txt['parking'],
					'pet' => $txt['pet'],
					'petrol' => $txt['petrol'],
					'phone' => $txt['phone'],
					'picnic' => $txt['picnic'],
					'postal' => $txt['postal'],
					'repair' => $txt['repair'],
					'restaurant' => $txt['restaurant'],
					'sail' => $txt['sail'],
					'school' => $txt['school'],
					'scissors' => $txt['scissors'],
					'ship' => $txt['ship'],
					'shoppingbag' => $txt['shoppingbag'],
					'shoppingcart' => $txt['shoppingcart'],
					'ski' => $txt['ski'],
					'snack' => $txt['snack'],
					'snow' => $txt['snow'],
					'sport' => $txt['sport'],
					'star' => $txt['star'],
					'swim' => $txt['swim'],
					'taxi' => $txt['taxi'],
					'train' => $txt['train'],
					'truck' => $txt['truck'],
					'wc-female' => $txt['wc-female'],
					'wc-male' => $txt['wc-male'],
					'wc' => $txt['wc'],
					'wheelchair' => $txt['wheelchair'],
				)
			),
	);

	// Saving?
	if (isset($_GET['save']))
	{
		checkSession();
		saveDBSettings($config_vars);
		redirectexit('action=admin;area=modsettings;sa=googlemap');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=googlemap';
	$context['settings_title'] = $txt['googleMapFO'];

	prepareDBSettingContext($config_vars);
}
?>