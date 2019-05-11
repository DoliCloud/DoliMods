<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 */

/**
 *	\file       htdocs/prestashopget/index.php
 *	\ingroup    prestashopget
 *	\brief      Home page of prestashopget top menu
 */

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("prestashopget@prestashopget"));

$action=GETPOST('action', 'alpha');


// Securite acces client
if (! $user->rights->prestashopget->read) accessforbidden();
$socid=GETPOST('socid','int');
if (isset($user->societe_id) && $user->societe_id > 0)
{
	$action = '';
	$socid = $user->societe_id;
}

$max=5;
$now=dol_now();

$db2=getDoliDBInstance('mysqli', $conf->global->PRESTASHOPGET_DB_SERVER, $conf->global->PRESTASHOPGET_DB_USER, $conf->global->PRESTASHOPGET_DB_PASS, 'dolistore', 3306);
if (! $db2->connected)
{
    print 'Failed to connect to PrestaShop server';
    exit;
}

$product_id = GETPOST('product_id', 'int');


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("",$langs->trans("PrestashopGetArea"));

print load_fiche_titre($langs->trans("PrestashopGetArea"),'','prestashopget.png@prestashopget');


define('_DB_PREFIX_', 'ps_');

// Select for sales list
$query = "SELECT c.id_customer, c.email, c.lastname, c.firstname, c.date_add as cust_date_add, c.date_upd as cust_date_upd,
							od.id_order_detail, od.product_price, od.tax_rate, od.product_id,
							ROUND(od.product_price, 5) as amount_ht,
							ROUND(od.product_price * (100 + od.tax_rate) / 100, 2) as amount_ttc,
							od.reduction_percent, od.reduction_amount, od.product_quantity, od.product_quantity_refunded,
							o.id_order, o.date_add, o.valid
							FROM "._DB_PREFIX_."customer as c, "._DB_PREFIX_."order_detail as od,  "._DB_PREFIX_."orders as o
							WHERE o.id_order = od.id_order AND c.id_customer = o.id_customer";
if ($product_id > 0) $query.=" AND od.product_id = ".(int)$product_id;

//prestalog($query);
$subresult = $db2->query($query);

if ($subresult)
{
    $i=0;
    $totalamountearned=0;

    print '<table class="noborder centpercent">';

    // Fields title search
    // --------------------------------------------------------------------
    print '<tr class="liste_titre">';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    print '<td></td>';
    // Action column
    print '<td class="liste_titre right">';
    $searchpicto=$form->showFilterButtons();
    print $searchpicto;
    print '</td>';
    print '</tr>'."\n";

    // Fields title label
    // --------------------------------------------------------------------
    print '<tr class="liste_titre">';
    // Action column
    print getTitleFieldOfList('Module sell nb', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Customer', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Customer date creation', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Customer email', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Customer country', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('InEEC', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Date sale', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Product id', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Product label', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Product ref', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Amount earned', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Amount origin', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('Note', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'maxwidthsearch ')."\n";
    print getTitleFieldOfList('', 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
    print '</tr>'."\n";

    $num = $db2->num_rows($subresult);
    $cpt=0;
    while (($obj = $db2->fetch_object($subresult)) && ($cpt < min($num, 10000)))
    {
        $cpt++;

        $i+=$obj->product_quantity;

        print '<tr class="oddeven">';

        print '<td>'.($obj->product_quantity>1?($i+1-$obj->product_quantity).'-':'').$i.'</td>';
        print '<td>'.$obj->lastname.' '.$obj->firstname.'</td>';
        print '<td>'.$obj->cust_date_add.'</td>';
        print '<td>'.$obj->email.'</td>';
        print '<td>';
        //.BlockMySales::getCustomerCountry($id_lang, $obj->id_customer).
        print '</td>';
        print '<td></td>';
        print '<td>'.$obj->date_add.'</td>';
        print '<td>'.$obj->product_id.'</td>';
        print '<td>'.$arraylistofproducts[$obj->product_id]['name'].'</td>';
        print '<td>'.$arraylistofproducts[$obj->product_id]['reference'].'</td>';
        if (($obj->product_quantity - $obj->product_quantity_refunded) > 0 && $obj->valid == 1)
        {
            $qtyvalidated = ($obj->product_quantity - $obj->product_quantity_refunded);

            $amountearnedunit=(float) ($obj->amount_ht-$obj->reduction_amount+0);
            if ($obj->reduction_percent > 0) $amountearnedunit=round($amountearnedunit*(100-$obj->reduction_percent)/100,5);
            //$amountearned=$amountearnedunit*$subrow['product_quantity'];
            $amountearned=$amountearnedunit*$qtyvalidated;
            //if ($subrow['id_customer'] == 9824) var_dump($amountearned);

            $totalamountearned+=$amountearned;

            print '<td>'.$amountearned.'</td>';

            if ($obj->reduction_amount > 0 || $obj->reduction_percent > 0)
            {
                $totalamountunit = ($qtyvalidated > 1 ? $obj->amount_ht * $qtyvalidated : $obj->amount_ht);
                print '<td>'.round($amountearnedunit,5).' ('.($totalamountunit+0).')'.'</td>';
            }
            else
                print '<td>'.round($amountearnedunit,5).'</td>';
        }
        else
        {
            print '<td>'.'RefundedOrCancelled'.'</td>';
        }

        print '</tr>';
    }

    print '</table>';
}
else
{
    dol_print_error($db2);
}

// End of page
llxFooter();
$db->close();
