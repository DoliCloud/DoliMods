<?PHP

// ---------------------------------------------------------------------

function add_page_size($page_name, $page_x, $page_y)
{
  global $PREVIEW_WIDTH_MM, $PREVIEW_HEIGHT_MM;
  global $PAGE_SIZE_LIST;

  if (($page_x <= $PREVIEW_WIDTH_MM) && ($page_y <= $PREVIEW_HEIGHT_MM))
  {
    $PAGE_SIZE_LIST[] = array(0 => $page_name, $page_x, $page_y);
  }
}


// ---------------------------------------------------------------------

/**
 * generates html select dropdown list with options
 * if values is two dimensional then adds optgroup too
 *
 * @param 	string	$name			selectbox name and id
 * @param 	array		$values		options
 * @param 	mixed		$selected	selected option
 * @param 	array		$attributes additonal attributes
 *
 * @return 	string	html source with selectbox
 */
function html_selectbox($name, $values, $selected=NULL, $attributes=array())
{
	$attr_html = '';
	if(is_array($attributes) && !empty($attributes))
	{
		foreach ($attributes as $k=>$v)
		{
			$attr_html .= ' '.$k.'="'.$v.'"';
		}
	}

	$output = '<select name="'.$name.'" id="'.$name.'"'.$attr_html.'>'."\n";
	if(is_array($values) && !empty($values))
	{
		foreach($values as $key=>$value)
		{
			if(is_array($value))
			{
				$output .= '<optgroup label="'.$key.'">'."\n";
				foreach($value as $k=>$v)
				{
					$sel = $selected==$v ? ' selected="selected"' : '';
					$output .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>'."\n";
				}
				$output .= '</optgroup>'."\n";
			}
			else
			{
				$sel = $selected==$value ? ' selected="selected"' : '';
				$output .= '<option value="'.$value.'"'.$sel.'>'.$value.'</option>'."\n";
			}
		}
	}
	$output .= "</select>\n";

	return $output;
}


// ---------------------------------------------------------------------

?>