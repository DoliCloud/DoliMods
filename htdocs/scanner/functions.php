<?php
/* Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *  \file           htdocs/scanner/index.php
 *  \brief          Main page of scanner module
 */

/**
 * Set page size into global variable
 *
 * @param 	string	$page_name		Page name
 * @param 	string	$page_x			X
 * @param 	string	$page_y			Y
 * @return	void
 */
function add_page_size($page_name, $page_x, $page_y)
{
	global $PREVIEW_WIDTH_MM, $PREVIEW_HEIGHT_MM;
	global $PAGE_SIZE_LIST;

	if (($page_x <= $PREVIEW_WIDTH_MM) && ($page_y <= $PREVIEW_HEIGHT_MM)) {
		$PAGE_SIZE_LIST[] = array(0 => $page_name, $page_x, $page_y);
	}
}


/**
 * Generates html select dropdown list with options
 * if values is two dimensional then adds optgroup too
 *
 * @param 	string	$name			selectbox name and id
 * @param 	array		$values		options
 * @param 	mixed		$selected	selected option
 * @param 	array		$attributes additonal attributes
 *
 * @return 	string	html source with selectbox
 */
function html_selectbox($name, $values, $selected = null, $attributes = array())
{
	$attr_html = '';
	if (is_array($attributes) && !empty($attributes)) {
		foreach ($attributes as $k=>$v) {
			$attr_html .= ' '.$k.'="'.$v.'"';
		}
	}

	$output = '<select class="flat" name="'.$name.'" id="'.$name.'"'.$attr_html.'>'."\n";
	if (is_array($values) && !empty($values)) {
		foreach ($values as $key=>$value) {
			if (is_array($value)) {
				$output .= '<optgroup label="'.$key.'">'."\n";
				foreach ($value as $k=>$v) {
					$sel = $selected==$v ? ' selected="selected"' : '';
					$output .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>'."\n";
				}
				$output .= '</optgroup>'."\n";
			} else {
				$sel = $selected==$value ? ' selected="selected"' : '';
				$output .= '<option value="'.$value.'"'.$sel.'>'.$value.'</option>'."\n";
			}
		}
	}
	$output .= "</select>\n";

	return $output;
}
