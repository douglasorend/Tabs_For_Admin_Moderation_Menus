<?php
/**********************************************************************************
* GenericTabs.template.php - Template for the Admin Tabs mod
*********************************************************************************
* This program is distributed in the hope that it is and will be useful, but
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE .
**********************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

// This contains the html for the side bar of the admin center, which is used for all admin pages.
function template_generic_menu_sidebar_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl;

	// This is the main table - we need it so we can keep the content to the right of it.
	echo '
	<div id="main_container">';

	// What one are we rendering?
	$context['cur_menu_id'] = isset($context['cur_menu_id']) ? $context['cur_menu_id'] + 1 : 1;
	$menu_context = &$context['menu_data_' . $context['cur_menu_id']];
	$tab_context = &$menu_context['tab_data'];

	// Display the names of each section as a tab at the top of the screen:
	echo '
		<div id="tabContainer">
			<div class="tabs">
				<ul>';
	foreach ($menu_context['sections'] as $id => $section)
	{
		$first = '';
		$selected = false;
		foreach ($section['areas'] as $name => $area)
		{
			if ($first == '')
				$first = $name;
			if ($context['admin_area'] == $name)
			{
				$selected = true;
				$area_title = $id;
			}
		}
		echo '
					<li', ($selected ? ' class="tabActiveHeader"' : ''), '><a href="', $boardurl, '/index.php?action=', $_GET['action'], ';area=', $first, $menu_context['extra_parameters'], '">', (isset($section['title']) ? $section['title'] : ''), '</a></li>';
	}

	// Show the section header - and pump up the line spacing for readability.
	$section = &$menu_context['sections'][$area_title];
	echo '
				</ul>
			</div>
			<div id="tabcontent">
				<div class="tabpage"><hr/>
					<div id="left_admsection"><span id="admin_menu"></span>
					<div class="adm_section">
						<div class="cat_bar esm_cat_bar">
							<h4 class="catbg">', (isset($section['title']) ? $section['title'] : ''), '</h4>
						</div>
						<div class="esm_wrapper">
							<div class="roundframe"><div class="inneframe esm_innerframe">
							<ul class="smalltext left_admmenu esm_ul_fix">
								';

	// For every area of this section show a link to that area (bold if it's currently selected.)
	foreach ($section['areas'] as $i => $area)
	{
		// Not supposed to be printed?
		if (empty($area['label']))
			continue;

		// Is this the current area, or just some area?
		unset($context['tabs']);
		echo '<li style="border-width: 1px; border-style:none none solid none;"><strong>', $area['label'], '</strong></li>';
		$context['tabs'] = isset($area['subsections']) ? $area['subsections'] : array();

		// Process the tabs:
		if (isset($area['subsections']))
		{
			$tab_context['tabs'] = array();
			foreach ($area['subsections'] as $id => $tab)
			{
				// Can this not be accessed?
				if (!empty($tab['disabled']))
				{
					$tab_context['tabs'][$id]['disabled'] = true;
					continue;
				}

				// Did this not even exist - or do we not have a label?
				if (!isset($tab_context['tabs'][$id]))
					$tab_context['tabs'][$id] = array('label' => isset($tab['label']) ? $tab['label'] : '');
				elseif (!isset($tab_context['tabs'][$id]['label']))
					$tab_context['tabs'][$id]['label'] = isset($tab['label']) ? $tab['label'] : '';

				// Has a custom URL defined in the main admin structure?
				if (isset($tab['url']) && !isset($tab_context['tabs'][$id]['url']))
					$tab_context['tabs'][$id]['url'] = $tab['url'];
				// Any additional paramaters for the url?
				if (isset($tab['add_params']) && !isset($tab_context['tabs'][$id]['add_params']))
					$tab_context['tabs'][$id]['add_params'] = $tab['add_params'];
				// Has it been deemed selected?
				if (!empty($tab['is_selected']))
					$tab_context['tabs'][$id]['is_selected'] = true;
				// Does it have its own help?
				if (!empty($tab['help']))
					$tab_context['tabs'][$id]['help'] = $tab['help'];
				// Is this the last one?
				if (!empty($tab['is_last']) && !isset($tab_context['override_last']))
					$tab_context['tabs'][$id]['is_last'] = true;
			}

			// Find the selected tab
			foreach ($tab_context['tabs'] as $sa => $tab)
			{
				if (!empty($tab['is_selected']) || (isset($context['admin_area']) && $context['admin_area'] == $i &&
					isset($menu_context['current_subsection']) && $menu_context['current_subsection'] == $sa))
				{
					$selected_tab = $tab;
					$tab_context['tabs'][$sa]['is_selected'] = true;
				}
			}
			
			// Display the "tabs" as submenu items:
			foreach ($tab_context['tabs'] as $sa => $tab)
			{
				if (!empty($tab['disabled']))
					continue;

				echo '<li><div class="esm_nonactive_indent">';
				if (!empty($tab['is_selected']) && ($i == $menu_context['current_area']))
					echo '<img src="', $settings['images_url'], '/selected.gif" alt="*" /><strong><a href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $menu_context['current_area'] . ';sa=' . $sa, $menu_context['extra_parameters'], '">', $tab['label'], '</a></strong>';
				else
					echo '<a href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $i . ';sa=' . $sa, $menu_context['extra_parameters'], '">', $tab['label'], '</a>';
				echo '</li>
								';
			}
		}
		else
		{
			echo '<li><div class="esm_nonactive_indent">';
			if ($i == $menu_context['current_area'])
				echo '<img src="', $settings['images_url'], '/selected.gif" alt="*" /><strong><a href="', isset($area['url']) ? $area['url'] : $menu_context['base_url'] . ';area=' . $menu_context['current_area'], $menu_context['extra_parameters'], '">', $area['label'], '</a></strong>';
			else
				echo '<a href="', isset($tab['url']) ? $tab['url'] : $menu_context['base_url'] . ';area=' . $i, $menu_context['extra_parameters'], '">', $area['label'], '</a>';
			echo '</li>';
		}

		echo '<br/>';
	}

	// This is where the actual "main content" area for the admin section starts.
	echo '
								</ul>
								</div></div>
								<span class="lowerframe"><span></span></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="main_admsection">';

	if (isset($selected_tab))
	{
		echo '
			<div class="cat_bar"><h3 class="catbg">';
		if (!empty($selected_tab['icon']) || !empty($tab_context['icon']) || !empty($selected_tab['help']) || !empty($tab_context['help']))
		{
			echo '
				<span class="ie6_header floatleft">';

			if (!empty($selected_tab['icon']) || !empty($tab_context['icon']))
				echo '<img src="', $settings['images_url'], '/icons/', !empty($selected_tab['icon']) ? $selected_tab['icon'] : $tab_context['icon'], '" alt="" class="icon" />';

			if (!empty($selected_tab['help']) || !empty($tab_context['help']))
				echo '<a href="', $scripturl, '?action=helpadmin;help=', !empty($selected_tab['help']) ? $selected_tab['help'] : $tab_context['help'], '" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt['help'], '" class="icon" /></a>';

			echo $tab_context['title'], '
				</span>';
		}
		else
		{
			echo '
				', $tab_context['title'];
		}
		echo '</h3></div>
			<p class="windowbg description">', isset($selected_tab['description']) ? $selected_tab['description'] : (isset($tab_context['description']) ? $tab_context['description'] : ''), '</p>';
	}
}

// Part of the sidebar layer - closes off the main bit.
function template_generic_menu_sidebar_below()
{
	global $context, $settings, $options;

	echo '
		</div>
	</div><br class="clear" />';
}

// Functions to eliminate "no dropdown template" errors:
function template_generic_menu_dropdown_above()
{
	template_generic_menu_sidebar_above();
}

function template_generic_menu_dropdown_below()
{
	template_generic_menu_sidebar_below();
}

?>