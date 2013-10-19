<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2005 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2013 Olivier Geffroy  <jeff@jeffinfo.com>
 * Copyright (C) 2013 Florian Henry	  <florian.henry@open-concept.pro>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * $Id: fiche.php,v 1.14 2011/07/31 22:23:31 eldy Exp $
 */


/**
        \file       htdocs/custom/ventilation/param/comptes/fiche.php
        \ingroup    ventilation compta
        \brief      Page de la fiche des comptes comptables
        \version    $Revision: 1.14 $
*/

// Dolibarr environment
$res=@include("../main.inc.php");
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");

dol_include_once("/ventilation/compta/class/comptacompte.class.php");

$langs->load("ventilation@ventilation");

$mesg = '';
$action = GETPOST('action');

if (GETPOST("action") == 'add' && $user->rights->compta->ventilation->parametrer)
{
  $compte = new ComptaCompte($db);

  $compte->numero   = GETPOST("numero");
  $compte->intitule = GETPOST("intitule");

  // exemple traitement case à cocher journal de vente
  $compte->sellsjournal = (GETPOST("sellsjournal") == 'on')?'O':'N';

  $e_compte = $compte;

  $res = $compte->create($user);

  if ($res == 0)
    {
      Header("Location: liste.php");
    }
  else
    {
      if ($res == -3)
		{
		  $_error = 1;
		  $action = "create";
		}
	      if ($res == -4)
		{
		  $_error = 2;
		  $action = "create";
		}
    }
}
elseif (GETPOST("action") == 'maj' && $user->rights->compta->ventilation->parametrer)
{
  $compte = new ComptaCompte($db, GETPOST('id'));

  $compte->numero   = GETPOST("numero");
  $compte->intitule = GETPOST("intitule");
  
  // exemple traitement case à cocher journal de vente
  $compte->sellsjournal = (GETPOST("sellsjournal") == 'on')?'O':'N';

  $e_compte = $compte;

  $res = $compte->update();

  if ($res >= 0)
    {
      Header("Location: liste.php");
    }
	else
	    $action = 'update';
}



/*
 * Cr�ation d'un compte
 *
 */
if ($action == 'create' && $user->rights->compta->ventilation->parametrer)
{
	llxHeader("","Nouveau compte");

    $html = new Form($db);
    $nbligne=0;

    print_fiche_titre($langs->trans("NewAccount"));

    print '<form action="fiche.php" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="add">';
    print '<input type="hidden" name="type" value="'.GETPOST("type").'">'."\n";

    print '<table class="border" width="100%">';
    print '<tr>';
    print '<td>'.$langs->trans("AccountNumber").'</td><td><input name="numero" size="20" value="'.$compte->numero.'">';
    if ($_error == 1)
    {
        print "Ce num�ro de compte existe d�j�";
    }
    if ($_error == 2)
    {
        print "Valeur(s) manquante(s)";
    }
    print '</td></tr>';
    print '<tr><td>'.$langs->trans("Label").'</td><td><input name="intitule" size="40" value="'.$compte->intitule.'"></td></tr>';

  	// exemple case à cocher journal de vente
    $checked = ($compte->sellsjournal == 'O')?' checked=checked':'';
    print '<tr><td>'.$langs->trans("SellsJournal").'</td><td><input type="checkbox" name="sellsjournal"'.$checked .'/></td></tr>';

    print '<tr><td>&nbsp;</td><td><input type="submit" class="button" value="'.$langs->trans("Create").'"></td></tr>';
    print '</table>';
    print '</form>';
}
elseif ($action == 'update' && $user->rights->compta->ventilation->parametrer)
{
	llxHeader("","Modification compte");

    $html = new Form($db);
    $nbligne=0;

    $compte = new ComptaCompte($db, GETPOST('id'));
    
    print_fiche_titre($langs->trans("UpdateAccount"));

    print '<form action="fiche.php" method="post">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="maj">';
    print '<input type="hidden" name="id" value="'.GETPOST("id").'">'."\n";
	print '<input type="hidden" name="type" value="'.GETPOST("type").'">'."\n";

    print '<table class="border" width="100%">';
    print '<tr>';
    print '<td>'.$langs->trans("AccountNumber").'</td><td><input name="numero" size="20" value="'.$compte->numero.'"></td></tr>';
    print '<tr><td>'.$langs->trans("Label").'</td><td><input name="intitule" size="40" value="'.$compte->intitule.'"></td></tr>';

  	// exemple case à cocher journal de vente
    $checked = ($compte->sellsjournal == 'O')?' checked=checked':'';
    print '<tr><td>'.$langs->trans("SellsJournal").'</td><td><input type="checkbox" name="sellsjournal"'.$checked .'/></td></tr>';

    print '<tr><td>&nbsp;</td><td><input type="submit" class="button" value="'.$langs->trans("Update").'"></td></tr>';
    print '</table>';
    print '</form>';
}

$db->close();

llxFooter('$Date: 2011/07/31 22:23:31 $ - $Revision: 1.14 $');
?>
