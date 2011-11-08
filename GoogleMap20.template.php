<?php
// Google Member Maps Modification

function template_map()
{
	global $context, $modSettings, $scripturl, $txt, $settings;
	
	if (!empty($modSettings['googleMapsEnable']))
	{
		echo '
					<div class="cat_bar">
						<h4 class="catbg">
							<span class="align_left">', $txt['googleMap'], '</span>
						</h4>
					</div>
					
					<div class="windowbg2">
						<span class="topslice"><span></span></span>
						<div class="content">';

		echo '
							<table width="100%">
								<tr>
									<td class="windowbg2" valign="middle" align="center">
										<div id="map" onmousewheel="cancelscroll()" style="width: 675px; height: 500px; color: #000000;"></div>
										', $txt['googlePinCount'], '
									</td>';

		// Show a right sidebar?
		if ((!empty($modSettings['googleSidebar'])) && $modSettings['googleSidebar'] == 'right')
		{
			echo '
									<td style="white-space: nowrap;">
										<div class="centertext"><em><strong>', $txt['googleMappinned'], '</strong></em></div>
										<hr style="width: 94%;" />
										<div id="gooSidebar" class="googleMapsSidebar" align="left" style="padding-left: 15px;"></div>';
			if (!empty($modSettings['googleBoldMember']))
				echo '
										<div class="centertext googleMapsLegend">
											<b>BOLD</b>&nbsp;' . $txt['googleMapOnMove'] . '
										</div>';
			echo '
									</td>';
		}

		// No sidebar then put the data below the map
		if ((!empty($modSettings['googleSidebar'])) && $modSettings['googleSidebar'] == 'none')
			echo '
								</tr>
								<tr>
									<td align="center">
										<div id="gooSidebar" class="googleMapsLegend" align="left"></div>
									</td>';

		// close this table 
		echo '
								</tr>
							</table>';

		// Load the scripts so google starts to render this page
		$modSettings['googleMapsKey'] = !empty($modSettings['googleMapsKey']) ? $modSettings['googleMapsKey'] : '';
		echo '
							<script type="text/javascript" language="JavaScript" src="http://maps.google.com/maps?file=api&v=2&key=', $modSettings['googleMapsKey'], '"></script>
							<script type="text/javascript" language="JavaScript" src="', $settings['default_theme_url'], '/scripts/Clusterer2.js"></script>
							<script type="text/javascript" language="JavaScript" src="', $scripturl, '?action=googlemap;sa=.js"></script>
							<script type="text/javascript" language="JavaScript"><!-- window.onunload=GUnload(); // --></script>';

		// Show a legend below the map as well?
		if (!empty($modSettings['googleMapsEnableLegend']))
		{
			echo '
							<div class="cat_bar">
								<h3 class="catbg"><span class="align_left">', $txt['googleMapsLegend'], '</span></h3>
							</div>
							<table class="centertext">
								<tr>';

			if (empty($modSettings['googleMapsPinGender']))
				echo '
									<td><img src="http://chart.apis.google.com/chart', $modSettings['npin'], '" alt="" />', $txt['googleMapGreenPinGD'], '</td>';
			else
				echo  '
									<td><img src="http://chart.apis.google.com/chart', $modSettings['npin'], '" alt="" />', $txt['googleMapGreenPinNG'], '</td>
									<td><img src="http://chart.apis.google.com/chart', $modSettings['mpin'], '" alt="" />', $txt['googleMapBluePin'], '</td>
									<td><img src="http://chart.apis.google.com/chart', $modSettings['fpin'], '" alt="" />', $txt['googleMapRedPin'], '</td>';
			
			if (!empty($modSettings['googleMapsEnableClusterer']))
				echo '
									<td><img src="http://chart.apis.google.com/chart', $modSettings['cpin'], '" alt="" />', $txt['googleMapPurplePin'], '</td>';
			
			echo '
								</tr>
							</table>';
		}
		
		echo '
							<table class="centertext">';
								
		// If they can place a pin, give them a hint
		if (allowedTo('googleMap_place'))
			echo '
								<tr>
									<td>
										<a href="', $scripturl, '?action=profile;area=forumprofile">', $txt['googleMapAddPinNote'], '</a>
									</td>
								</tr>';
								
		// Google earth klm output enabled as well?
		if (!empty($modSettings['KMLoutput_enable']))
			echo '
								<tr>
									<td align="center">
										<a href="', $scripturl, '?action=.kml"><img src="', $settings['default_theme_url'], '/images/google_earth_feed.gif" border="0" alt="" /></a>
									</td>
								</tr>';

		// Done with the bottom table
		echo '
							</table>';

		// Close it up jim
		echo '
						</div>
						<span class="botslice"><span></span></span>
					</div>';
	}
}
?>