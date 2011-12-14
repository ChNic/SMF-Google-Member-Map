<?php
// Google Member Maps Modification

function template_map()
{
	global $context, $modSettings, $scripturl, $txt, $settings;
	
	if (!empty($modSettings['googleMap_Enable']))
	{
		echo '
					<div class="cat_bar">
						<h4 class="catbg">
							<span class="align_left">', $txt['googleMap'], '</span>
						</h4>
					</div>
					
					<div class="windowbg2">
						<span class="topslice"><span></span></span>
						<div class="content">
							<table width="100%">
								<tr>';

		// Show a left sidebar?
		if ((!empty($modSettings['googleMap_Sidebar'])) && $modSettings['googleMap_Sidebar'] == 'left')
		{
			echo '
									<td style="white-space: nowrap;padding-left: 15px;">
										<div class="centertext"><em><strong>', $txt['googleMap_Pinned'], '</strong></em></div>
										<hr style="width: 94%;" />
										<div id="googleSidebar" class="googleMap_Sidebar" ></div>';
			if (!empty($modSettings['googleMap_BoldMember']))
				echo '
										<div class="centertext googleMap_Legend">
											<strong>' . $txt['googleMap_bold'] . '</strong>&nbsp;' . $txt['googleMap_OnMove'] . '
										</div>';
			echo '
									</td>';
		}					
		
		// our map
		echo '
									<td class="windowbg2" valign="middle" align="center">
										<div id="map" style="width: 675px; height: 500px; color: #000000;"></div>';
									
		// Set the text for the number of pins we are, or can, show
		if ($context['total_pins'] >= $modSettings['googleMap_PinNumber'] && $modSettings['googleMap_PinNumber'] != 0)
			echo 
										sprintf($txt['googleMap_Thereare'], '<strong>(' . $modSettings['googleMap_PinNumber'] . '+)</strong>');
		else
			echo 
		
										sprintf($txt['googleMap_Thereare'], '<strong>(' . $context['total_pins'] . ')</strong>');
		echo '
									</td>';

		// Show a right sidebar?
		if (!empty($modSettings['googleMap_Sidebar']) && $modSettings['googleMap_Sidebar'] == 'right')
		{
			echo '
									<td style="white-space: nowrap;padding-right: 15px;">
										<div class="centertext"><em><strong>', $txt['googleMap_Pinned'], '</strong></em></div>
										<hr style="width: 94%;" />
										<div id="googleSidebar" class="googleMap_Sidebar"></div>';
			if (!empty($modSettings['googleMap_BoldMember']))
				echo '
										<div class="centertext googleMap_Legend">
											<strong>' . $txt['googleMap_bold'] . '</strong>&nbsp;' . $txt['googleMap_OnMove'] . '
										</div>';
			echo '
									</td>';
		}

		// close this table
		echo '
								</tr>
							</table>';

		// Show a legend?
		if (!empty($modSettings['googleMapsEnableLegend']))
		{
			echo '
							<div class="cat_bar">
								<h3 class="catbg"><span class="align_left">', $txt['googleMap_Legend'], '</span></h3>
							</div>
							<table class="centertext">
								<tr>';

			if (empty($modSettings['googleMap_PinGender']))
				echo '
									<td><img src="http://chart.apis.google.com/chart', $modSettings['npin'], '" alt="" />', $txt['googleMap_MemberPin'], '</td>';
			else
				echo  '
									<td><img src="http://chart.apis.google.com/chart', $modSettings['npin'], '" alt="" />', $txt['googleMap_AndrogynyPin'], '</td>
									<td><img src="http://chart.apis.google.com/chart', $modSettings['mpin'], '" alt="" />', $txt['googleMap_MalePin'], '</td>
									<td><img src="http://chart.apis.google.com/chart', $modSettings['fpin'], '" alt="" />', $txt['googleMap_FemalePin'], '</td>';
			
			if (!empty($modSettings['googleMap_EnableClusterer']) && ($context['total_pins'] > (!empty($modSettings['googleMap_MinMarkertoCluster']) ? $modSettings['googleMap_MinMarkertoCluster'] : 0)))
				echo '
									<td><img src="http://chart.apis.google.com/chart', $modSettings['cpin'], '" alt="" />', $txt['googleMap_GroupOfPins'], '</td>';
			
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
										<a href="', $scripturl, '?action=profile;area=forumprofile">', $txt['googleMap_AddPinNote'], '</a>
									</td>
								</tr>';
								
		// Google earth klm output enabled?
		if (!empty($modSettings['googleMap_KMLoutput_enable']))
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
				
		// Load the scripts so we can render the map
		echo '
					<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
					<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/markerclusterer_packed.js"></script>
					<script type="text/javascript" src="', $scripturl, '?action=googlemap;sa=.js;count='. $context['total_pins'] .'"></script>';
	}
}
?>