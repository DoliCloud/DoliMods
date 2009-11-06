<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2008      Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/includes/modules/facture/mercure/mercure.modules.php
 *	\ingroup    facture
 *	\brief      Class filte of Mercure numbering module for invoice
 *	\version    $Id: mercure2.modules.php,v 1.1 2009/11/06 19:47:45 eldy Exp $
 */
require_once(DOL_DOCUMENT_ROOT ."/includes/modules/facture/modules_facture.php");


/**
 *	\class      mod_facture_mercure
 *	\brief      Classe du modele de numerotation de reference de facture Mercure
 */
class mod_facture_mercure2 extends ModeleNumRefFactures
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $error = '';


    /**     \brief      Renvoi la description du modele de numerotation
     *      \return     string      Texte descripif
     */
	function info()
    {
		global $conf,$langs;

		$langs->load("bills");

		$form = new Form($db);

		$texte = $langs->trans('GenericNumRefModelDesc')."<br>\n";
		$texte.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$texte.= '<input type="hidden" name="action" value="updateMask">';
		$texte.= '<input type="hidden" name="maskconstinvoice" value="FACTURE_MERCURE2_MASK_INVOICE">';
		$texte.= '<input type="hidden" name="maskconstcredit" value="FACTURE_MERCURE2_MASK_CREDIT">';
		$texte.= '<table class="nobordernopadding" width="100%">';

		$tooltip=$langs->trans("GenericMaskCodes",$langs->transnoentities("Invoice"));
		$tooltip.=$langs->trans("GenericMaskCodes2");
		$tooltip.=$langs->trans("GenericMaskCodes3");
		$tooltip.=$langs->trans("GenericMaskCodes4a",$langs->transnoentities("Invoice"),$langs->transnoentities("Invoice"));
		$tooltip.=$langs->trans("GenericMaskCodes5");

		// Parametrage du prefix
		$texte.= '<tr><td>'.$langs->trans("Mask").' ('.$langs->trans("InvoiceStandard").'):</td>';
		$texte.= '<td align="right">'.$form->textwithpicto('<input type="text" class="flat" size="24" name="maskinvoice" value="'.$conf->global->FACTURE_MERCURE2_MASK_INVOICE.'">',$tooltip,1,1).'</td>';

		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';

		$texte.= '</tr>';

		// Parametrage du prefix des avoirs
		$texte.= '<tr><td>'.$langs->trans("Mask").' ('.$langs->trans("InvoiceAvoir").'):</td>';
		$texte.= '<td align="right">'.$form->textwithpicto('<input type="text" class="flat" size="24" name="maskcredit" value="'.$conf->global->FACTURE_MERCURE2_MASK_CREDIT.'">',$tooltip,1,1).'</td>';
		$texte.= '</tr>';

		$texte.= '</table>';
		$texte.= '</form>';

		return $texte;
    }

    /**     \brief      Return an example of number value
     *      \return     string      Example
     */
    function getExample()
    {
    	global $conf,$langs,$mysoc;

    	$old_code_client=$mysoc->code_client;
    	$old_code_type=$mysoc->typent_code;
    	$mysoc->code_client='CCCCCCCCCC';
    	$mysoc->typent_code='TTTTTTTTTT';
    	$numExample = $this->getNextValue($mysoc,'');
		$mysoc->code_client=$old_code_client;
		$mysoc->typent_code=$old_code_type;

		if (! $numExample)
		{
			$numExample = $langs->trans('NotConfigured');
		}
		return $numExample;
    }

	/**		\brief      Return next value
	*      	\param      objsoc      Object third party
	*      	\param      facture		Object invoice
	*      	\return     string      Value if OK, 0 if KO
	*/
	function getNextValue($objsoc,$facture)
	{
		global $db,$conf;

		require_once(DOL_DOCUMENT_ROOT ."/lib/functions2.lib.php");

		// Get Mask value
		$mask = '';
		if (is_object($facture) && $facture->type == 2) $mask=$conf->global->FACTURE_MERCURE2_MASK_CREDIT;
		else $mask=$conf->global->FACTURE_MERCURE2_MASK_INVOICE;
		if (! $mask)
		{
			$this->error='NotConfigured';
			return 0;
		}

		$where='';
		if ($facture->type == 2) $where.= " AND type = 2";
		else $where.=" AND type != 2";

		$numFinal=$this->get_next_value($db,$mask,'facture','facnumber',$where,$objsoc,$facture->date);

		return  $numFinal;
	}


	/**		\brief      Return next free value
    *      	\param      objsoc      Object third party
	* 		\param		objforref	Object for number to search
    *   	\return     string      Next free value
    */
    function getNumRef($objsoc,$objforref)
    {
        return $this->getNextValue($objsoc,$objforref);
    }


	/**
	 * Return next value for a mask
	 *
	 * @param unknown_type 	$db				Database handler
	 * @param 				$mask			Mask to use
	 * @param unknown_type 	$table			Table containing field with counter
	 * @param unknown_type 	$field			Field containing already used values of counter
	 * @param unknown_type 	$where			To add a filter on selection (for exemple to filter on invoice types)
	 * @param unknown_type 	$objsoc			The company that own the object we need a counter for
 	 * @param unknown_type 	$date			Date to use for the {y},{m},{d} tags.
	 * @return 	string		New value
	 */
	function get_next_value($db,$mask,$table,$field,$where='',$objsoc='',$date='')
	{
		global $conf;

		if (! is_object($objsoc)) $valueforccc=$objsoc;
		else $valueforccc=$objsoc->code_client;

		// Clean parameters
		if ($date == '') $date=mktime();	// We use local year and month of PHP server to search numbers
		// but we should use local year and month of user

		// Extract value for mask counter, mask raz and mask offset
		if (! preg_match('/\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}/i',$mask,$reg)) return 'ErrorBadMask';
		$masktri=$reg[1].$reg[2].$reg[3];
		$maskcounter=$reg[1];
		$maskraz=-1;
		$maskoffset=0;
		if (strlen($maskcounter) < 3) return 'CounterMustHaveMoreThan3Digits';

		// Extract value for third party mask counter
		if (preg_match('/\{(c+)(0*)\}/i',$mask,$regClientRef))
		{
			$maskrefclient=$regClientRef[1].$regClientRef[2];
			$maskrefclient_maskclientcode=$regClientRef[1];
			$maskrefclient_maskcounter=$regClientRef[2];
			$maskrefclient_maskoffset=0; //default value of maskrefclient_counter offset
			$maskrefclient_clientcode=substr($valueforccc,0,strlen($maskrefclient_maskclientcode));//get n first characters of client code where n is length in mask
			$maskrefclient_clientcode=str_pad($maskrefclient_clientcode,strlen($maskrefclient_maskclientcode),"#",STR_PAD_RIGHT);//padding maskrefclient_clientcode for having exactly n characters in maskrefclient_clientcode
			$maskrefclient_clientcode=dol_string_nospecial($maskrefclient_clientcode);//sanitize maskrefclient_clientcode for sql insert and sql select like
			if (strlen($maskrefclient_maskcounter) > 0 && strlen($maskrefclient_maskcounter) < 3) return 'CounterMustHaveMoreThan3Digits';
		}
		else $maskrefclient='';

		// Extract value for third party type
		if (preg_match('/\{(t+)\}/i',$mask,$regType))
		{
			$masktype=$regType[1];
			$masktype_value=substr(preg_replace('/^TE_/','',$objsoc->typent_code),0,strlen($regType[1]));//get n first characters of client code where n is length in mask
			$masktype_value=str_pad($masktype_value,strlen($regType[1]),"#",STR_PAD_RIGHT);
		}
		else $masktype='';

		$maskwithonlyymcode=$mask;
		$maskwithonlyymcode=preg_replace('/\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}/i',$maskcounter,$maskwithonlyymcode);
		$maskwithonlyymcode=preg_replace('/\{dd\}/i','dd',$maskwithonlyymcode);
		$maskwithonlyymcode=preg_replace('/\{(c+)(0*)\}/i',$maskrefclient,$maskwithonlyymcode);
		$maskwithonlyymcode=preg_replace('/\{(t+)\}/i',$masktype_value,$maskwithonlyymcode);
		$maskwithnocode=$maskwithonlyymcode;
		$maskwithnocode=preg_replace('/\{yyyy\}/i','yyyy',$maskwithnocode);
		$maskwithnocode=preg_replace('/\{yy\}/i','yy',$maskwithnocode);
		$maskwithnocode=preg_replace('/\{y\}/i','y',$maskwithnocode);
		$maskwithnocode=preg_replace('/\{mm\}/i','mm',$maskwithnocode);
		// Now maskwithnocode = 0000ddmmyyyyccc for example
		// and maskcounter    = 0000 for example
		//print "maskwithonlyymcode=".$maskwithonlyymcode." maskwithnocode=".$maskwithnocode."\n<br>";

		// If an offset is asked
		if (! empty($reg[2]) && preg_match('/^\+/',$reg[2])) $maskoffset=preg_replace('/^\+/','',$reg[2]);
		if (! empty($reg[3]) && preg_match('/^\+/',$reg[3])) $maskoffset=preg_replace('/^\+/','',$reg[3]);

		// Define $sqlwhere

		// If a restore to zero after a month is asked we check if there is already a value for this year.
		if (! empty($reg[2]) && preg_match('/^@/',$reg[2]))  $maskraz=preg_replace('/^@/','',$reg[2]);
		if (! empty($reg[3]) && preg_match('/^@/',$reg[3]))  $maskraz=preg_replace('/^@/','',$reg[3]);
		if ($maskraz >= 0)
		{
			if ($maskraz > 12) return 'ErrorBadMaskBadRazMonth';

			// Define reg
			if ($maskraz > 1 && ! preg_match('/^(.*)\{(y+)\}\{(m+)\}/i',$maskwithonlyymcode,$reg)) return 'ErrorCantUseRazInStartedYearIfNoYearMonthInMask';
			if ($maskraz <= 1 && ! preg_match('/^(.*)\{(y+)\}/i',$maskwithonlyymcode,$reg)) return 'ErrorCantUseRazIfNoYearInMask';
			//print "x".$maskwithonlyymcode." ".$maskraz;

			// Define $yearcomp and $monthcomp (that will be use in the select where to search max number)
			$monthcomp=$maskraz;
			$yearoffset=0;
			$yearcomp=0;
			if (date("m",$date) < $maskraz) { $yearoffset=-1; }	// If current month lower that month of return to zero, year is previous year
			if (strlen($reg[2]) == 4) $yearcomp=sprintf("%04d",date("Y",$date)+$yearoffset);
			if (strlen($reg[2]) == 2) $yearcomp=sprintf("%02d",date("y",$date)+$yearoffset);
			if (strlen($reg[2]) == 1) $yearcomp=substr(date("y",$date),2,1)+$yearoffset;

			$sqlwhere='';
			$sqlwhere.='( (SUBSTRING('.$field.', '.(strlen($reg[1])+1).', '.strlen($reg[2]).') >= '.$yearcomp;
			if ($monthcomp > 1)	// Test useless if monthcomp = 1 (or 0 is same as 1)
			{
				$sqlwhere.=' AND SUBSTRING('.$field.', '.(strlen($reg[1])+strlen($reg[2])+1).', '.strlen($reg[3]).') >= '.$monthcomp.')';
				$sqlwhere.=' OR SUBSTRING('.$field.', '.(strlen($reg[1])+1).', '.strlen($reg[2]).') >= '.sprintf("%02d",($yearcomp+1)).' )';
			}
			else
			{
				$sqlwhere.=') )';
			}
		}
		//print "masktri=".$masktri." maskcounter=".$maskcounter." maskraz=".$maskraz." maskoffset=".$maskoffset."<br>\n";

		// Define $sqlstring
		$posnumstart=strpos($maskwithnocode,$maskcounter);	// Pos of counter in final string (from 0 to ...)
		if ($posnumstart < 0) return 'ErrorBadMaskFailedToLocatePosOfSequence';
		$sqlstring='SUBSTRING('.$field.', '.($posnumstart+1).', '.strlen($maskcounter).')';
		//print "x".$sqlstring;

		// Define $maskLike
		$maskLike = dol_string_nospecial($mask);
		$maskLike = str_replace("%","_",$maskLike);
		// Replace protected special codes with matching number of _ as wild card caracter
		$maskLike = str_replace(dol_string_nospecial('{yyyy}'),'____',$maskLike);
		$maskLike = str_replace(dol_string_nospecial('{yy}'),'__',$maskLike);
		$maskLike = str_replace(dol_string_nospecial('{y}'),'_',$maskLike);
		$maskLike = str_replace(dol_string_nospecial('{mm}'),'__',$maskLike);
		$maskLike = str_replace(dol_string_nospecial('{dd}'),'__',$maskLike);
		$maskLike = str_replace(dol_string_nospecial('{'.$masktri.'}'),str_pad("",strlen($maskcounter),"_"),$maskLike);
		if ($maskrefclient) $maskLike = str_replace(dol_string_nospecial('{'.$maskrefclient.'}'),str_pad("",strlen($maskrefclient),"_"),$maskLike);
		//if ($masktype) $maskLike = str_replace(dol_string_nospecial('{'.$masktype.'}'),str_pad("",strlen($masktype),"_"),$maskLike);
		if ($masktype) $maskLike = str_replace(dol_string_nospecial('{'.$masktype.'}'),$masktype_value,$maskLike);

		// Get counter in database
		$counter=0;
		$sql = "SELECT MAX(".$sqlstring.") as val";
		$sql.= " FROM ".MAIN_DB_PREFIX.$table;
		//		$sql.= " WHERE ".$field." not like '(%'";
		$sql.= " WHERE ".$field." LIKE '".$maskLike."'";
		$sql.= " AND ".$field." NOT LIKE '%PROV%'";
		$sql.= " AND entity = ".$conf->entity;
		if ($where) $sql.=$where;
		if ($sqlwhere) $sql.=' AND '.$sqlwhere;

		//print $sql.'<br>';
		dol_syslog("functions2::get_next_value sql=".$sql, LOG_DEBUG);
		$resql=$db->query($sql);
		if ($resql)
		{
			$obj = $db->fetch_object($resql);
			$counter = $obj->val;
		}
		else dol_print_error($db);
		if (empty($counter) || preg_match('/[^0-9]/i',$counter)) $counter=$maskoffset;
		$counter++;

		if ($maskrefclient_maskcounter)
		{
			//print "maskrefclient_maskcounter=".$maskrefclient_maskcounter." maskwithnocode=".$maskwithnocode." maskrefclient=".$maskrefclient."\n<br>";

			// Define $sqlstring
			$maskrefclient_posnumstart=strpos($maskwithnocode,$maskrefclient_maskcounter,strpos($maskwithnocode,$maskrefclient));	// Pos of counter in final string (from 0 to ...)
			if ($maskrefclient_posnumstart <= 0) return 'ErrorBadMask';
			$maskrefclient_sqlstring='SUBSTRING('.$field.', '.($maskrefclient_posnumstart+1).', '.strlen($maskrefclient_maskcounter).')';
			//print "x".$sqlstring;

			// Define $maskrefclient_maskLike
			$maskrefclient_maskLike = dol_string_nospecial($mask);
			$maskrefclient_maskLike = str_replace("%","_",$maskrefclient_maskLike);
			// Replace protected special codes with matching number of _ as wild card caracter
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{yyyy}'),'____',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{yy}'),'__',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{y}'),'_',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{mm}'),'__',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{dd}'),'__',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{'.$masktri.'}'),str_pad("",strlen($maskcounter),"_"),$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{'.$maskrefclient.'}'),$maskrefclient_clientcode.str_pad("",strlen($maskrefclient_maskcounter),"_"),$maskrefclient_maskLike);

			// Get counter in database
			$maskrefclient_counter=0;
			$maskrefclient_sql = "SELECT MAX(".$maskrefclient_sqlstring.") as val";
			$maskrefclient_sql.= " FROM ".MAIN_DB_PREFIX.$table;
			//$sql.= " WHERE ".$field." not like '(%'";
			$maskrefclient_sql.= " WHERE ".$field." LIKE '".$maskrefclient_maskLike."'";
			$maskrefclient_sql.= " AND entity = ".$conf->entity;
			if ($where) $maskrefclient_sql.=$where; //use the same optional where as general mask
			if ($sqlwhere) $maskrefclient_sql.=' AND '.$sqlwhere; //use the same sqlwhere as general mask
			$maskrefclient_sql.=' AND (SUBSTRING('.$field.', '.(strpos($maskwithnocode,$maskrefclient)+1).', '.strlen($maskrefclient_maskclientcode).")='".$maskrefclient_clientcode."')";

			dol_syslog("functions2::get_next_value maskrefclient_sql=".$maskrefclient_sql, LOG_DEBUG);
			$maskrefclient_resql=$db->query($maskrefclient_sql);
			if ($maskrefclient_resql)
			{
				$maskrefclient_obj = $db->fetch_object($maskrefclient_resql);
				$maskrefclient_counter = $maskrefclient_obj->val;
			}
			else dol_print_error($db);
			if (empty($maskrefclient_counter) || preg_match('/[^0-9]/i',$maskrefclient_counter)) $maskrefclient_counter=$maskrefclient_maskoffset;
			$maskrefclient_counter++;
		}

		// Build numFinal
		$numFinal = $mask;

		// We replace special codes except refclient
		$numFinal = str_replace('{yyyy}',date("Y",$date),$numFinal);
		$numFinal = str_replace('{yy}',date("y",$date),$numFinal);
		$numFinal = str_replace('{y}' ,substr(date("y",$date),2,1),$numFinal);
		$numFinal = str_replace('{mm}',date("m",$date),$numFinal);
		$numFinal = str_replace('{dd}',date("d",$date),$numFinal);

		// Now we replace the counter
		$maskbefore='{'.$masktri.'}';
		$maskafter=str_pad($counter,strlen($maskcounter),"0",STR_PAD_LEFT);
		//print 'x'.$maskbefore.'-'.$maskafter.'y';
		$numFinal = str_replace($maskbefore,$maskafter,$numFinal);

		// Now we replace the refclient
		if ($maskrefclient)
		{
			//print "maskrefclient=".$maskrefclient." maskwithonlyymcode=".$maskwithonlyymcode." maskwithnocode=".$maskwithnocode."\n<br>";
			$maskrefclient_maskbefore='{'.$maskrefclient.'}';
			$maskrefclient_maskafter=$maskrefclient_clientcode.str_pad($maskrefclient_counter,strlen($maskrefclient_maskcounter),"0",STR_PAD_LEFT);
			$numFinal = str_replace($maskrefclient_maskbefore,$maskrefclient_maskafter,$numFinal);
		}

		// Now we replace the type
		if ($masktype)
		{
			$masktype_maskbefore='{'.$masktype.'}';
			$masktype_maskafter=$masktype_value;
			$numFinal = str_replace($masktype_maskbefore,$masktype_maskafter,$numFinal);
		}

		dol_syslog("functions2::get_next_value return ".$numFinal,LOG_DEBUG);
		return $numFinal;
	}
}
?>