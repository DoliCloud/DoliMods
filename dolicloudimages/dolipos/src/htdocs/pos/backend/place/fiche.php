<?php
/* Copyright (C) 2011 		Juanjo Menent <jmenent@2byte.es>
 * Copyright (C) 2012 		Ferran Marcet <fmarcet@2byte.es>
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
 *	    \file       htdocs/pos/backend/place/fiche.php
 *      \ingroup    pos
 *		\brief      Page to create/view a place
 *		\version    $Id: fiche.php,v 1.6 2011-08-19 07:54:24 jmenent Exp $
 */

$res=@include("../../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../../main.inc.php");                // For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");
dol_include_once('/pos/backend/class/place.class.php');
dol_include_once('/pos/backend/lib/place.lib.php');


$langs->load("pos@pos");
$langs->load('bills');

$action=GETPOST('action','alpha');

// Security check

$id=GETPOST('id','int');
$ref=GETPOST('ref','string');

if ($user->societe_id) $socid=$user->societe_id;
//$result=restrictedArea($user,'pos',$id,'pos_cash','','','rowid');


/*
 * Actions
 */
if (GETPOST('action','alpha') == 'add')
{
    $error=0;

    // Create account
    $place = new place($db);

    $place->description	= trim(GETPOST('description','alpha'));
    $place->name			= trim(GETPOST('name','alpha'));
    $place->status			= GETPOST('status','int');
	
    
    if (empty($place->name))
    {
        $message='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentities("LabelName")).'</div>';
        $action='create';       // Force chargement page en mode creation
        $error++;
    }
    
	if (! $error)
    {
        $id = $place->create($user);
        if ($id == 0)
        {
           $url=dol_buildpath("/pos/backend/place/place.php",1);
           Header("Location: ".$url); 	
        }
        else 
        {
            $message='<div class="error">'.$place->error().'</div>';
            $action='create';   // Force chargement page en mode creation
        }
    }
}

if (GETPOST('action','alpha') == 'update' && ! GETPOST('cancel','alpha'))
{
    $error=0;

    // Update account
    $place = new place($db);
    $place->fetch(GETPOST('id','int'));

 	$place->description	= trim(GETPOST('description','alpha'));
    $place->name 			= trim(GETPOST('name','alpha'));
   
	    
   	if (empty($place->name))
    {
        $message='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->transnoentities("LabelName")).'</div>';
        $action='update';       // Force chargement page en mode creation
        $error++;
    }
    
	if (! $error)
    {
        $result = $place->update($user);
        if ($result < 0)
        {
            $message='<div class="error">'.$place->error().'</div>';
            $action='edit';     // Force chargement page edition
        }
    }
}

if (GETPOST('action','alpha') == 'confirm_delete' && GETPOST('confirm','alpha') == "yes" && $user->rights->pos->backend)
{
    // Modification
    $place = new place($db);
    $place->delete(GETPOST('id','int'));

    header("Location: ".dol_buildpath("/pos/backend/place/place.php",1));
    exit;
}


/*
 * View
 */
$helpurl='EN:Module_DoliPos|FR:Module_DoliPos_FR|ES:M&oacute;dulo_DoliPos';
llxHeader('','',$helpurl);
if($conf->global->POS_HELP){
	dol_include_once('/pos/backend/class/utils.class.php');
}

$form = new Form($db);
$htmlcompany = new FormCompany($db);


/* ************************************************************************** */
/*                                                                            */
/* Affichage page en mode creation                                            */
/*                                                                            */
/* ************************************************************************** */

if ($action == 'create')
{
	$place=new place($db);

	print_fiche_titre($langs->trans("NewPlace"));

	if ($message) { print "$message<br>\n"; }

	print '<form action="'.$_SERVER["PHP_SELF"].'" name="formsoc" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="clos" value="0">';

	print '<table class="border" width="100%">';

	// Name
	print '<tr><td valign="top" class="fieldrequired">'.$langs->trans("Name").'</td>';
	print '<td colspan="3"><input size="30" type="text" class="flat" name="name" value="'.GETPOST('name','alpha').'"></td></tr>';

	
	// Description
	print '<tr><td valign="top">'.$langs->trans("Description").'</td>';
	print '<td colspan="3">';
	print '<input size="30" type="text" class="flat" name="description" value="'.GETPOST('description','alpha').'">';
	print '</td></tr>';
	
	print '<tr><td align="center" colspan="4"><input value="'.$langs->trans("CreatePlace").'" type="submit" class="button"></td></tr>';
	print '</form>';
	print '</table>';
}
/* ************************************************************************** */
/*                                                                            */
/* Visu et edition                                                            */
/*                                                                            */
/* ************************************************************************** */
else
{
    if (($id || $ref) && $action != 'edit')
	{
		$place = new place($db);
		
		$place->fetch($id,$ref);
		

		/*
		* Affichage onglets
		*/

		// Onglets
		$head=place_prepare_head($place);
		dol_fiche_head($head, 'placename', $langs->trans("Places"),0,'placedesk');

		/*
		* Confirmation to delete
		*/
		if ($action == 'delete')
		{
			$ret=$form->form_confirm($_SERVER["PHP_SELF"].'?id='.$place->id,$langs->trans("Place"),$langs->trans("ConfirmDeletePlace"),"confirm_delete",'','',1);
			if ($ret == 'html') print '<br>';
		}

		print '<table class="border" width="100%">';

		// Name
		print '<tr><td valign="top">'.$langs->trans("Name").'</td>';
		print '<td colspan="3">';
		//print $place->name;
		print $form->showrefnav($place,'ref','',1,'name','ref');
		print '</td></tr>';
		
		//Description
		print '<tr><td>';
        print $langs->trans('Description');
        print '</td><td>';
        print $place->description;
        print '</td></tr>';
        
        //Status
        print '<tr><td>';
        print $langs->trans('Status');
        print '</td><td>';
        print $place->getLibStatut(4);
        print '</td></tr>';

		print '</td></tr>';
		
		print '</table>';

		print '</div>';


		/*
		 * Barre d'actions
		 */
		print '<div class="tabsAction">';

		if ($user->rights->pos->backend)
		{
			print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&id='.$place->id.'">'.$langs->trans("Modify").'</a>';
		}

		$canbedeleted=$place->can_be_deleted();   // Renvoi vrai si compte sans mouvements
		if ($user->rights->pos->backend && $canbedeleted)
		{
			print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?action=delete&id='.$place->id.'">'.$langs->trans("Delete").'</a>';
		}

		print '</div>';

	}

    /* ************************************************************************** */
    /*                                                                            */
    /* Edition                                                                    */
    /*                                                                            */
    /* ************************************************************************** */

    if (GETPOST("id") && $action == 'edit' && $user->rights->pos->backend)
    {
        $place = new place($db);
        $place->fetch(GETPOST('id','int'));

        print_fiche_titre($langs->trans("EditPlace"));
        print "<br>";

        if ($message) { print "$message<br>\n"; }

        print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$place->id.'" method="post" name="formsoc">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="update">';
        print '<input type="hidden" name="id" value="'.GETPOST('id','int').'">'."\n\n";

        print '<table class="border" width="100%">';

        
       	// Name
		print '<tr><td valign="top" class="fieldrequired">'.$langs->trans("Name").'</td>';
		print '<td colspan="3"><input size="30" type="text" class="flat" name="name" value="'.$place->name.'"></td></tr>';
		
		// Description
		print '<tr><td valign="top" class="fieldrequired">'.$langs->trans("Description").'</td>';
		print '<td colspan="3"><input size="30" type="text" class="flat" name="description" value="'.$place->description.'"></td></tr>';
		
		print '<tr><td align="center" colspan="4"><input value="'.$langs->trans("Modify").'" type="submit" class="button">';
        print ' &nbsp; <input name="cancel" value="'.$langs->trans("Cancel").'" type="submit" class="button">';
        print '</td></tr>';
        
        print '</table>';

        print '</form>';
	}

}

llxFooter();

$db->close();
?>