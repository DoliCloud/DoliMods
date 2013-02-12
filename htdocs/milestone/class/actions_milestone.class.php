<?php
/* Copyright (C) 2010-2012 Regis Houssin  <regis@dolibarr.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *	\file       htdocs/milestone/class/actions_milestone.class.php
 *	\ingroup    milestone
 *	\brief      Fichier de la classe des jalons
 */

dol_include_once('/milestone/class/dao_milestone.class.php');


/**
 *	\class      ActionsMilestone
 *	\brief      Classe permettant la gestion des jalons
 */
class ActionsMilestone
{
	var $db;
	var $error;
	var $dao;
	var $element='milestone';
	var $table_element='milestone';
	
	// Id of module
	var $module_number=1790;

	var $id;
	var $label;
	var $description;
	var $priority;
	
	var $object;
	var $objParent;
	var $elementid;
	var $elementtype;
	
	var $rang;
	var $rangtouse;
	
	var $datec;
	var $dateo;
	var $datee;

	var $tpl=array();
	var $lines=array();			// Tableau en memoire des jalons


	/**
	 *	Constructor
	 * 
	 *	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}
	
	/**
	 *
	 */
	function getInstanceDao()
	{
		if (! is_object($this->dao))
		{
			$this->dao=new DaoMilestone($this->db);
		}
	
		return $this->dao;
	}
	
	/**
	 *
	 */
	function selectMilestoneLines($object,$selected='',$htmlname='fk_parent_line')
	{
		global $langs;
		
		$langs->load('milestone@milestone');
		
		$milestones=array();
		
		$dao = $this->getInstanceDao();
		
		foreach($object->lines as $line)
		{
			if ($line->product_type == 9 && $line->special_code == $this->module_number)
			{
				$milestones[] = $line;
			}
		}
		
		if (empty($milestones))
		{
			$out = '<select id="select_'.$htmlname.'" class="flat" name="'.$htmlname.'" disabled="disabled">';
			$out.= '<option value="-1" selected="selected">'.$langs->trans('NoMilestone').'</option>';
		}
		else
		{
			$out = '<select id="select_'.$htmlname.'" class="flat" name="'.$htmlname.'">';
			$out.= '<option value="-1"></option>';
			foreach($milestones as $line)
			{
				$ret = $dao->fetch($object,$line->rowid);
				if ($selected && $selected == $line->rowid) $out.= '<option value="'.$line->rowid.'" selected="selected">'.$dao->label.'</option>';
				else $out.= '<option value="'.$line->rowid.'">'.$dao->label.'</option>';
			}
		}
		$out.= '</select>';
		
		return $out;
	}
	
	/**
	 *
	 */
	function selectObjectLines($object,$htmlname='product_id')
	{
		$out = '<select id="select_'.$htmlname.'" class="flat" name="'.$htmlname.'">';
		$out.= '<option value="-1" selected="selected"></option>';
		foreach($object->lines as $line)
		{
			if ($line->product_type < 3 && empty($line->fk_parent_line))
			{
				$out.= '<option value="'.$line->rowid.'">';
				$out.= (empty($line->ref) ? '' : $line->ref.' - ').$line->libelle;
				$out.= '</option>';
			}
		}
		$out.= '</select>';
		
		return $out;
	}
	
	/**
	 * 
	 */
	function formCreateProductOptions($parameters=false, &$object, &$action='')
	{
		global $langs;
		
		$out='';
		
		$langs->load('milestone@milestone');
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		$out.= $langs->trans('AddTo').' '.$this->selectMilestoneLines($object,$selected);
		$out.= '<br />';
		
		return $out;
	}
	
	/**
	 * 
	 */
	function formEditProductOptions($parameters=false, &$object, &$action='')
	{
		global $langs;
		
		$out='';
		
		$langs->load('milestone@milestone');
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		$out.= $langs->trans('MoveTo').' '.$this->selectMilestoneLines($object,$fk_parent_line);
		$out.= '<br />';
		
		return $out;
	}

	/**
	 * 	Return HTML form for add a milestone
	 */
	function formAddObject($parameters=false)
	{
		global $conf, $langs, $user;
		global $bcnd, $var;
		
		if ($user->rights->milestone->creer)
		{
			$langs->load('milestone@milestone');
			
			if (is_array($parameters) && ! empty($parameters))
			{
				foreach($parameters as $key=>$value)
				{
					$$key=$value;
				}
			}
			
			dol_include_once('/milestone/tpl/addmilestoneform.tpl.php');
		}
	}
	
	/**
	 * 	Return HTML form for builddoc bloc
	 */
	function formBuilddocOptions($parameters=false)
	{
		global $conf, $langs;
		global $bc, $var;
		
		$langs->load('milestone@milestone');
		
		$out='';
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		$out.= '<input type="hidden" name="modulepart" value="' . $modulepart . '">';
		
		$checkedHideDetails = '';
		$checkedHideDesc = '';
		$tag = $modulepart . '_' . $id;

		if (isset($_SESSION['milestone_hidedetails_' . $tag]))
		{
			$checkedHideDetails = ($_SESSION['milestone_hidedetails_' . $tag] ? ' checked="checked"' : '');
		}
		else
		{
			$checkedHideDetails = ($conf->global->MILESTONE_HIDE_PRODUCT_DETAILS ? ' checked="checked"' : '');
		}
		
		if (isset($_SESSION['milestone_hidedesc_' . $tag]))
		{
			$checkedHideDesc = ($_SESSION['milestone_hidedesc_' . $tag] ? ' checked="checked"' : '');
		}
		else
		{
			$checkedHideDesc = ($conf->global->MILESTONE_HIDE_PRODUCT_DESC ? ' checked="checked"' : '');
		}
		
		$out.= '<tr '.$bc[$var].'>';
		$out.= '<td colspan="4"><input type="checkbox" name="hidedetails" value="2"' . $checkedHideDetails . ' /> '.$langs->trans('HideDetails').'</td>';
		$out.= '</tr>';
		$out.= '<tr '.$bc[$var].'>';
		$out.= '<td colspan="4"><input type="checkbox" name="hidedesc" value="1"' . $checkedHideDesc . ' /> '.$langs->trans('HideDescription').'</td>';
		$out.= '</tr>';
		
		return $out;
	}
	
	/**
	 * 	Return HTML with selected milestone
	 * 	@param		object			Parent object
	 * 	TODO mettre le html dans un template 
	 */
	function printObjectLine($parameters=false, &$object, &$action='viewline', $hookmanager=false)
	{
		global $conf,$langs,$user;
		global $form,$bc,$bcnd;
		
		$dao = $this->getInstanceDao();
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		$return = $dao->fetch($object,$line);
	
		$element = $object->element;
		// TODO uniformiser
		if ($element == 'propal') $element = 'propale';

		// Ligne en mode visu
		if ($action != 'editline' || $selected != $line->rowid)
		{
			print '<tr id="row-'.$line->id.'" '.$bc[$var].'>';

			print '<td '.($showamount?'':'colspan="6"').'>';
			print '<a name="'.$line->rowid.'"></a>'; // ancre pour retourner sur la ligne;
			
			$text = img_object($langs->trans('Milestone'),'milestone@milestone');
			$text.= ' '.$dao->label.'<br>';
			$description=($conf->global->PRODUIT_DESC_IN_FORM?'':dol_htmlentitiesbr($line->description));
			print $form->textwithtooltip($text,$description,3,'','',$i);
			
			// Show range
			//print_date_range($line->date_start,$line->date_end);
			
			// Add description in form
			if ($conf->global->PRODUIT_DESC_IN_FORM)
			{
				print ($line->description?'<br>'.dol_htmlentitiesbr($line->description):'');
			}
			
			print "</td>\n";
			
			// Icone d'edition et suppression
			if ($object->statut == 0 && $user->rights->$element->creer)
			{
				$colspan='';
				
				if ($user->rights->milestone->creer)
				{
					print '<td align="center">';
					print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=editline&amp;lineid='.$line->rowid.'#'.$line->rowid.'">';
					print img_edit();
					print '</a>';
					print '</td>';
				}
				else
				{
					print '<td>&nbsp;</td>';
				}
				
				if ($user->rights->milestone->supprimer)
				{
					print '<td align="center">';
					print '<a href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=ask_deletemilestone&amp;lineid='.$line->rowid.'">';
					print img_delete();
					print '</a></td>';
				}
				else
				{
					print '<td>&nbsp;</td>';
				}
				
				if ($num > 1)
				{
					print '<td align="center" class="tdlineupdown">';
					if ($i > 0)
					{
						print '<a class="lineupdown" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=up&amp;rowid='.$line->rowid.'">';
						print img_up();
						print '</a>';
					}
					if ($i < $num-1)
					{
						print '<a class="lineupdown" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=down&amp;rowid='.$line->rowid.'">';
						print img_down();
						print '</a>';
					}
					print '</td>';
				}
			}
			else
			{
				print '<td colspan="3">&nbsp;</td>';
			}
			
			print '</tr>';
			
			$subtotal=0;
			foreach($object->lines as $objectline)
			{
				if ($objectline->fk_parent_line == $line->rowid)
				{
					$object->printObjectLine($action,$objectline,$var,$num,$i,$dateSelector,$seller,$buyer,$selected,$hookmanager);
					$subtotal++;
				}
			}
			
			if ($subtotal)
			{
				print '<tr>';
				print '<td align="right" colspan="5">'.$langs->trans("SubTotal").' :</td>';
				print '<td align="right" nowrap="nowrap">'.price($line->total_ht).'</td>';
				print '<td colspan="3">&nbsp;</td>';
				print '</tr>';
			}
		}
		
		// Ligne en mode update
		if ($object->statut == 0 && $action == 'editline' && $user->rights->$element->creer && $selected == $line->rowid)
		{
			print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'#'.$line->rowid.'" method="POST">';
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="updatemilestone">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="lineid" value="'.$line->rowid.'">';
			print '<input type="hidden" name="special_code" value="'.$line->special_code.'">';
			print '<input type="hidden" name="product_type" value="'.$line->product_type.'">';
			
			// Label
			print '<tr '.$bcnd[$var].'>';
			print '<td colspan="5">';
			print '<a name="'.$line->rowid.'"></a>'; // ancre pour retourner sur la ligne
			print '<input size="30" type="text" id="label" name="label" value="'.$dao->label.'"></td>';
			print '<td align="center" colspan="4" rowspan="2" valign="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
			print '<br><input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></td>';
			print '</tr>';
			
			// Description
			print '<tr '.$bcnd[$var].'>';
			print '<td colspan="5">';
			
			// Editor wysiwyg
			require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
			$nbrows=ROWS_2;
			if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
			$doleditor=new DolEditor('description',$line->description,'',100,'dolibarr_details','',false,true,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_DETAILS,$nbrows,70);
			$doleditor->Create();
			
			print '</td>';
			print '</tr>' . "\n";
			
			print "</form>\n";
		}
	}
	
	/**
	 * 	Return HTML with origin selected milestone
	 * 	@param		object			Parent object
	 * 	TODO mettre le html dans un template 
	 */
	function printOriginObjectLine($parameters=false, &$object, &$action='')
	{
		global $conf,$langs;
		global $form, $bc;
		
		$dao = $this->getInstanceDao();
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		$return = $dao->fetch($object,$line);
		
		print '<tr '.$bc[$var].'><td colspan="6">';
		$text = img_object($langs->trans('Milestone'),'milestone@milestone');
		$text.= ' '.$dao->label.'<br>';
		$description=($conf->global->PRODUIT_DESC_IN_FORM?'':dol_htmlentitiesbr($line->desc));
		print $form->textwithtooltip($text,$description,3,'','',$i);
		print "</td></tr>\n";
		
		$subtotal=0;
		foreach($object->lines as $objectline)
		{
			if ($objectline->fk_parent_line == $line->rowid)
			{
				$object->printOriginLine($objectline,$var);
				$subtotal++;
			}
		}
		
		if ($subtotal)
		{
			print "\n".'<tr>';
			print '<td align="right" colspan="3">'.$langs->trans("SubTotal").' :</td>';
			print '<td align="right" nowrap="nowrap">'.price($line->total_ht).'</td>';
			print '<td colspan="2">&nbsp;</td>';
			print '</tr>'."\n";
		}
	}
	
	/**
	 * 	Return action of hook
	 * 	@param		object			Linked object
	 */
	function doActions($parameters=false, &$object, &$action='')
	{
		global $conf,$user,$langs;
		
		$dao = $this->getInstanceDao();
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		$element = $object->element;
		// TODO uniformiser
		if ($element == 'propal') $element = 'propale';

		/*
		 * 	Add milestone
		 */
		if ($action == 'addmilestone' && $user->rights->milestone->creer && $user->rights->$element->creer)
		{
			$error=0;

			if (! GETPOST('milestone_label') || GETPOST('milestone_label') == $langs->transnoentities('Label'))
			{
				$langs->load('milestone@milestone');
				$this->error = '<div class="error">'.$langs->trans("ErrorMilestoneFieldRequired",$langs->transnoentities("Label")).'</div>';
				$error++;
			}
			
			if (! $error)
			{	
				$id = (GETPOST("id") ? GETPOST("id") : GETPOST("facid"));

				$object->fetch($id);
				
				$linemax = $object->line_max();
				$rangtouse = $linemax+1;
				
				$dao->objParent	= $object;
				$dao->label 		= GETPOST('milestone_label');
				$dao->description	= GETPOST('milestone_desc');
				$dao->product_type	= GETPOST('product_type');
				$dao->special_code	= GETPOST('special_code');
				$dao->rang			= $rangtouse;

				if ($dao->create($user) < 0)
				{
					$this->error = '<div class="error">'.$dao->error.'</div>';
				}
				else
				{
					Header ('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
					exit;
				}
			}
		}
		
		/*
		 * 	Update Milestone
		 */
		if ($action == 'updatemilestone' && $user->rights->milestone->creer && $user->rights->$element->creer && $_POST["save"] == $langs->trans("Save"))
		{
			$id = GETPOST("id")?GETPOST("id"):GETPOST("facid");
			
			if (! $object->fetch($id) > 0)
			{
				dol_print_error($db,$object->error);
				exit;
			}
			
			$object->fetch_thirdparty();
			
			$dao->objParent	= $object;
			$dao->id			= GETPOST('lineid');
			$dao->label 		= GETPOST('label');
			$dao->description	= GETPOST('description');
			$dao->product_type	= GETPOST('product_type');
			$dao->special_code	= GETPOST('special_code');
		
			$res = $dao->update($user);
			
			// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! GETPOST('lang_id')) $newlang=GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}
			//propale_pdf_create($db, $propal->id, $propal->modelpdf, $outputlangs);
		}
		
		// Remove line
		if ($action == 'confirm_deletemilestone' && GETPOST('confirm') == 'yes' && $user->rights->milestone->creer && $user->rights->$element->creer)
		{
			$id = GETPOST("id")?GETPOST("id"):GETPOST("facid");
			
			$object->fetch($id);
			$object->fetch_thirdparty();
			
			$dao->objParent	= $object;
			
			if ($dao->delete(GETPOST('lineid')))
			{
				// delete childs
				foreach($object->lines as $line)
				{
					if ($line->fk_parent_line == GETPOST('lineid'))
					{
						$ret = $object->deleteline($line->rowid);
					}
				}
				
				// reorder lines
				$object->line_order(true);
			}
				
			// Define output language
			$outputlangs = $langs;
			$newlang='';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! GETPOST('lang_id')) $newlang=GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->client->default_lang;
			if (! empty($newlang))
			{
				$outputlangs = new Translate("",$conf);
				$outputlangs->setDefaultLang($newlang);
			}
			//propale_pdf_create($db, $propal->id, $propal->modelpdf, $outputlangs);
			
			if ($object->element != 'facture') Header ('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
			else Header ('Location: '.$_SERVER["PHP_SELF"].'?facid='.$object->id);

			exit;
		}
		
		// Builddoc options
		if ($action == 'builddoc')
		{
			$tag = GETPOST('modulepart') . '_' . (GETPOST('facid') ? GETPOST('facid') : GETPOST('id'));
			
			if (GETPOST('hidedetails'))
			{
				$_SESSION['milestone_hidedetails_' . $tag] = true;
			}
			else
			{
				$_SESSION['milestone_hidedetails_' . $tag] = false;
			}
			
			if (GETPOST('hidedesc'))
			{
				$_SESSION['milestone_hidedesc_' . $tag] = true;
			}
			else
			{
				$_SESSION['milestone_hidedesc_' . $tag] = false;
			}
		}
	}
	
	/**
	 * 	Form confirm
	 * 
	 *	@param	array	$parameters		Extra parameters
	 *	@param	object	$object			Object
	 *	@param	string	$action			Type of action
	 *	@return	void
	 */
	function formconfirm($parameters=false, &$object, &$action)
	{
		global $langs;
		global $form;
		
		$langs->load('milestone@milestone');
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		$out='';
		
		/*
		 * 	Delete milestone confirmation
		 */
		if ($action == 'ask_deletemilestone')
		{
			$out=$form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteMilestone'), $langs->trans('ConfirmDeleteMilestone'), 'confirm_deletemilestone','',0,1);
		}
		
		return $out;
	}
	
	/**
	 *	Return line description translated in outputlangs and encoded in UTF8
	 *
	 *	@param		array	$parameters		Extra parameters
	 *	@param		object	$object			Object
	 *	@param    	string	$action			Type of action
	 *	@return		void
	 */
	function pdf_writelinedesc($parameters=false, &$object, &$action='')
	{
		$dao = $this->getInstanceDao();
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		if ($object->lines[$i]->product_type == 9 && $object->lines[$i]->special_code == $this->module_number)
		{
			$dao->fetch($object, $object->lines[$i]);
						
			$pdf->SetXY ($posx, $posy-1);
			$pdf->SetFillColor(230, 230, 230);
			$pdf->MultiCell(200-$posx, $h+2.5, '', 0, '', 1);
			
			$pdf->SetXY ($posx, $posy);
			$pdf->SetFont('', 'BU', 9);
			$pdf->MultiCell($w, $h-2, $outputlangs->convToOutputCharset($dao->label), 0, 'L');
			
			$nexy = $pdf->GetY();
			
			$pdf->SetFont('', 'I', 9);
			$description = dol_htmlentitiesbr($object->lines[$i]->desc, 1);
			
			if ($object->lines[$i]->date_start || $object->lines[$i]->date_end)
	        {
	        	// Show duration if exists
	        	if ($object->lines[$i]->date_start && $object->lines[$i]->date_end)
	        	{
	        		$period='('.$outputlangs->transnoentitiesnoconv('DateFromTo',dol_print_date($object->lines[$i]->date_start, $format, false, $outputlangs),dol_print_date($object->lines[$i]->date_end, $format, false, $outputlangs)).')';
	        	}
	        	if ($object->lines[$i]->date_start && ! $object->lines[$i]->date_end)
	        	{
	        		$period='('.$outputlangs->transnoentitiesnoconv('DateFrom',dol_print_date($object->lines[$i]->date_start, $format, false, $outputlangs)).')';
	        	}
	        	if (! $object->lines[$i]->date_start && $object->lines[$i]->date_end)
	        	{
	        		$period='('.$outputlangs->transnoentitiesnoconv('DateUntil',dol_print_date($object->lines[$i]->date_end, $format, false, $outputlangs)).')';
	        	}
	        	
	        	$description.="<br>".dol_htmlentitiesbr($period, 1);
	        }

	        if (! empty($description))
	        {
	        	$pdf->writeHTMLCell($w, $h, $posx, $nexy+1, $outputlangs->convToOutputCharset($description), 0, 1);
	        }
			
			$pdf->SetFont('', '', 9);
		}
		else
		{
			$labelproductservice='- '.pdf_getlinedesc($object, $i, $outputlangs, $hideref, $hidedesc, $issupplierline);
			$pdf->writeHTMLCell($w, $h, $posx, $posy, $outputlangs->convToOutputCharset($labelproductservice), 0, 1);
		}
	}

	/**
	 * 	Return line total excluding tax
	 * 	@param		object				Object
	 * 	@param		$i					Current line number
	 *  @param    	outputlang			Object lang for output
	 */
	function pdf_getlinetotalexcltax($parameters=false,$object,$action='')
	{
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		if ( $object->lines[$i]->product_type == 9 && $object->lines[$i]->special_code == $this->module_number && $object->lines[$i]->total_ht > 0)
		{
			return price($object->lines[$i]->total_ht);
		}
		else if ( $object->lines[$i]->product_type != 9 && empty($hidedetails) )
		{
			return price($object->lines[$i]->total_ht);
		}
	}
	
	/**
	 * 	Return line vat rate
	 * 	@param		object				Object
	 * 	@param		$i					Current line number
	 *  @param    	outputlang			Object lang for output
	 */
	function pdf_getlinevatrate($parameters=false,$object,$action='')
	{
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		if (empty($hidedetails) && $object->lines[$i]->product_type != 9 && (empty($object->lines[$i]->special_code) || $object->lines[$i]->special_code == 3))
		{
			return vatrate($object->lines[$i]->tva_tx,1,$object->lines[$i]->info_bits);
		}
	}
	
	/**
	 * 	Return line unit price excluding tax
	 * 	@param		object				Object
	 * 	@param		$i					Current line number
	 *  @param    	outputlang			Object lang for output
	 */
	function pdf_getlineupexcltax($parameters=false,$object,$action='')
	{
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		if (empty($hidedetails) && $object->lines[$i]->product_type != 9 && (empty($object->lines[$i]->special_code) || $object->lines[$i]->special_code == 3))
		{
			return price($object->lines[$i]->subprice);
		}
	}
	
	/**
	 * 	Return line quantity
	 * 	@param		object				Object
	 * 	@param		$i					Current line number
	 *  @param    	outputlang			Object lang for output
	 */
	function pdf_getlineqty($parameters=false,$object,$action='')
	{
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		if (empty($hidedetails) && $object->lines[$i]->product_type != 9 && empty($object->lines[$i]->special_code))
		{
			return $object->lines[$i]->qty;
		}
	}
	
	/**
	 * 	Return line remise percent
	 * 	@param		object				Object
	 * 	@param		$i					Current line number
	 *  @param    	outputlang			Object lang for output
	 */
	function pdf_getlineremisepercent($parameters=false,$object,$action='')
	{
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}
		
		if (empty($hidedetails) && $object->lines[$i]->product_type != 9 && empty($object->lines[$i]->special_code))
		{
			return dol_print_reduction($object->lines[$i]->remise_percent,$outputlangs);
		}
	}
	
	/**
	 *	Load an object from its id and create a new one in database
	 *	@param      objFrom			From object
	 *	@param      idTo			To object id
	 * 	@return		int				New id of clone
	 */
	function createfrom($parameters=false,$object,$action='')
	{
		global $user;
		
		$dao = $this->getInstanceDao();
		
		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		$error=0;
		
		if ((! empty($objFrom) && is_object($objFrom)) && ! empty($object->id) && ! empty($object->element))
		{
			$classname = ucfirst($object->element);
			$objTo = new $classname($this->db);
			$objTo->fetch($object->id);

			$dao->objParent = $objTo;

			for($i=0; $i < count($objTo->lines); $i++)
			{
				if ($objTo->lines[$i]->product_type == 9 && $objTo->lines[$i]->special_code == $this->module_number)
				{
					$dao->fetch($objFrom,$objFrom->lines[$i]);
					$dao->objParent->line = $objTo->lines[$i];
					$ret = $dao->create($user,1);
					if ($ret < 0) $error++;
				}
			}
		}
		
		if (! $error) return 1;
		else return -1;
	}

}
?>
