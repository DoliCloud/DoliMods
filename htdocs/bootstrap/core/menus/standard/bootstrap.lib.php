<?php
/* Copyright (C) 2010-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010-2012 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2011-2012 Philippe Grand       <philippe.grand@atoo-net.com>
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
 *  \file		htdocs/core/menus/standard/eldy.lib.php
 *  \brief		Library for file eldy menus
 */


/**
 * Core function to output top menu eldy
 *
 * @param 	DoliDB	$db				Database handler
 * @param 	string	$atarget		Target
 * @param 	int		$type_user     	0=Internal,1=External,2=All
 * @return	void
 */
function print_bootstrap_menu($db,$atarget,$type_user)
{
	global $user,$conf,$langs,$dolibarr_main_db_name;
	$langs->load("admin");
	$langs->load("users");
	

	// On sauve en session le menu principal choisi
	if (isset($_GET["mainmenu"])) $_SESSION["mainmenu"]=$_GET["mainmenu"];
	if (isset($_GET["idmenu"]))   $_SESSION["idmenu"]=$_GET["idmenu"];
	$_SESSION["leftmenuopened"]="";

	$id='mainmenu';
	$nopane='optioncss=print';
	
	print '<div class="navbar navbar-fixed-top">';
		print '<div class="navbar-inner">';
			print '<div class="container-fluid">';
	// Home
	$isactive='active';
	print '<script type="text/javascript"
        src='.dol_buildpath("/bootstrap/js/jquery.min.js",1).'>
</script>
<script type="text/javascript">
function func(e) {
  $(e);
}
</script>';

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "home")
	{
		$isactive='active'; $_SESSION['idmenu']='';
	}
	else
	{
		$isactive = '';
	}
	$idsel='home';
		print '<div class="nav-collapse">';
          print '<ul class="nav">';
            print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'"  href="'.DOL_URL_ROOT.'/index.php?'.$id.'=home" onclick="javascript:func(this)" >'.$langs->trans("Home").'<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/admin/index.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)" >'.$langs->trans("Setup").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/company.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)" >'.$langs->trans("MenuCompanySetup").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/modules.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Modules").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/menus.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Menus").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/ihm.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("GUISetup").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/boxes.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Boxes").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/delais.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Alerts").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/proxy.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Security").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/limits.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("MenuLimits").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/pdf.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("PDF").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/mails.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Emails").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/sms.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Sms").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/dict.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("DictionnarySetup").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/const.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("OtherSetup").'</a>';
                        print '</li>';
					print '</ul>';
                print '</li>';

                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/admin/system/index.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("SystemInfo").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/admin/system/dolibarr.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Dolibarr").'</a>';
                            print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/admin/system/constall.php?'.$id.'=home" onclick="javascript:func(this)">'.$langs->trans("AllParameters").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/admin/system/modules.php?'.$id.'=home" onclick="javascript:func(this)">'.$langs->trans("Modules").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/admin/triggers.php?'.$id.'=home" onclick="javascript:func(this)">'.$langs->trans("Triggers").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/admin/system/about.php?'.$id.'=home" onclick="javascript:func(this)">'.$langs->trans("About").'</a></li>';
                            print '</ul>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/system/os.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("OS").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/system/web.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("WebServer").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/system/phpinfo.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Php").'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/admin/system/database.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Database").'</a>';
                             print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/admin/system/database-tables.php?'.$id.'=home" onclick="javascript:func(this)">'.$langs->trans("Tables").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/admin/system/database-tables-contraintes.php?'.$id.'=home" onclick="javascript:func(this)">'.$langs->trans("Constraints").'</a></li>';
                             print '</ul>';
                        print '</li>';
					print '</ul>';
                print '</li>';

				print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/admin/tools/index.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("SystemTools").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/dolibarr_export.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Backup").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/dolibarr_import.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Restore").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/update.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("MenuUpgrade").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/eaccelerator.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("EAccelerator").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/listevents.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Audit").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/listsessions.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Sessions").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/admin/tools/purge.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Purge").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/support/index.php?'.$id.'=home" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("HelpCenter").'</a>';
                        print '</li>';
					print '</ul>';
                print '</li>';

				print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/user/home.php" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("MenuUsersAndGroups").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/user/index.php" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Users").'</a>'; 
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/user/fiche.php?action=create" onclick="javascript:func(this)">'.$langs->trans("NewUser").'</a></li>';
                            print '</ul>';
                        print '</li>';
                        print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/user/group/index.php" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("Groups").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/user/group/fiche.php?action=create" onclick="javascript:func(this)">'.$langs->trans("NewGroup").'</a></li>';
                            print '</ul>';
                        print '</li>';                       
                    print '</ul>';
                print '</li>';
              print '</ul>';
            
?>
<script type='text/javascript'>//<![CDATA[
        $(window).load(function(){
            jQuery('.submenu').hover(function () {
                jQuery(this).children('ul').removeClass('submenu-hide').addClass('submenu-show');
            }, function () {
                jQuery(this).children('ul').removeClass('.submenu-show').addClass('submenu-hide');
            }).find("a:first").append(" &raquo; ");
        });//]]>
        </script>
<?php
	// Third parties
	if ($conf->societe->enabled || $conf->fournisseur->enabled)
	{
		$langs->load("companies");
		$langs->load("suppliers");
		$langs->load("commercial");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "companies")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}
		
		$idsel='companies';
		if (($conf->societe->enabled && $user->rights->societe->lire)
		|| ($conf->fournisseur->enabled && $user->rights->fournisseur->lire))
		{
			print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'" href="'.DOL_URL_ROOT.'/societe/index.php?'.$id.'=companies" onclick="javascript:func(this)">'.$langs->trans("ThirdParties").'<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/societe/index.php?'.$id.'=companies" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("ThirdParties").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/societe/soc.php?action=create" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("MenuNewThirdParty").'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/comm/prospect/list.php" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("ListProspectsShort").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php?sortfield=s.datec&sortorder=desc&begin=&stcomm=-1">'.$langs->trans("LastProspectDoNotContact").'</a></li>';
                            
							
                                    print '<li><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php?sortfield=s.datec&sortorder=desc&begin=&stcomm=0">'.$langs->trans("LastProspectNeverContacted").'</a></li>';
                            
							
                                    print '<li><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php?sortfield=s.datec&sortorder=desc&begin=&stcomm=1">'.$langs->trans("LastProspectToContact").'</a></li>';
                            
                                    print '<li><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php?sortfield=s.datec&sortorder=desc&begin=&stcomm=2">'.$langs->trans("LastProspectContactInProcess").'</a></li>';
                            
                                    print '<li><a href="'.DOL_URL_ROOT.'/comm/prospect/list.php?sortfield=s.datec&sortorder=desc&begin=&stcomm=3">'.$langs->trans("LastProspectContactDone").'</a></li>';
                            
                                    print '<li><a href="'.DOL_URL_ROOT.'/comm/list.php?">'.$langs->trans("MenuNewProspect").'</a></li>';
                            print '</ul>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/comm/list.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ListCustomersShort").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/societe/soc.php?action=create&type=c">'.$langs->trans("MenuNewCustomer").'</a></li>';
                            print '</ul>';
                        print '</li>';
						// Fournisseurs
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/fourn/liste.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ListSuppliersShort").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/societe/soc.php?action=create&type=f">'.$langs->trans("MenuNewSupplier").'</a></li>';
                            print '</ul>';
                        print '</li>';
						
					print '</ul>';			
				  print '</li>';
				  // Contacts
				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/contact/list.php?'.$id.'=contacts" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)" >'.(! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("Contacts") : $langs->trans("ContactsAddresses")).'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/contact/fiche.php?'.$id.'=contacts&action=create" class="dropdown-toggle" data-toggle="dropdown" >'.(! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("NewContact") : $langs->trans("NewContactAddress")).'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/contact/list.php?'.$id.'=contacts" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/contact/list.php?'.$id.'=contacts&type=p">'.$langs->trans("Prospects").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/contact/list.php?'.$id.'=contacts&type=c">'.$langs->trans("Customers").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/contact/list.php?'.$id.'=contacts&type=f">'.$langs->trans("Suppliers").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/contact/list.php?'.$id.'=contacts&type=o">'.$langs->trans("Others").'</a></li>';
                            print '</ul>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
				
				  // Categories
				if ($conf->categorie->enabled)
				{
					$langs->load("categories");
				  // Categories prospects/customers
				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/categories/index.php?'.$id.'=cat&type=2" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("CustomersProspectsCategoriesShort").'</a>';
					if ($user->societe_id == 0)
					{
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/categories/fiche.php?action=create&amp;type=2" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewCategory").'</a>';
                        print '</li>';
					print '</ul>';
					}
				  print '</li>';

					// Categories suppliers
					print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/categories/index.php?'.$id.'=cat&amp;type=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("SuppliersCategoriesShort").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/categories/fiche.php?action=create&amp;type=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewCategory").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';					
				}
				print '</ul>';
		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print_text_menu_entry($langs->trans("ThirdParties"));
				print '</a>';
				print_end_menu_entry();
			}
		}
	}


	// Products-Services
	if ($conf->product->enabled || $conf->service->enabled)
	{
		$langs->load("products");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "products")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}
		$chaine="";
		if ($conf->product->enabled) { $chaine.=$langs->trans("Products"); }
		if ($conf->product->enabled && $conf->service->enabled) { $chaine.="/"; }
		if ($conf->service->enabled) { $chaine.=$langs->trans("Services"); }

		$idsel='products';
		if ($user->rights->produit->lire || $user->rights->service->lire)
		{
			print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'" href="'.DOL_URL_ROOT.'/product/index.php?'.$id.'=products" onclick="javascript:func(this)">';
			  print_text_menu_entry($chaine);
			  print '<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/product/index.php?'.$id.'=product&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Products").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/fiche.php?'.$id.'=product&amp;action=create&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewProduct").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/liste.php?'.$id.'=product&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/popuprop.php?'.$id.'=stats&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/reassort.php?'.$id.'=stats&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Stocks").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
				// Services
				print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/product/index.php?'.$id.'=service&amp;type=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Services").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/fiche.php?'.$id.'=service&amp;action=create&amp;type=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewService").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/liste.php?'.$id.'=service&amp;type=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/popuprop.php?'.$id.'=stats&amp;type=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
				// Categories
				if ($conf->categorie->enabled)
				{
				print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/categories/index.php?'.$id.'=cat&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Categories").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/categories/fiche.php?action=create&amp;type=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewCategory").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
				}
				// Stocks
				if ($conf->stock->enabled)
				{
					$langs->load("stocks");
					print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/product/stock/index.php?'.$id.'=stock" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Stocks").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/stock/fiche.php?action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuNewWarehouse").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/stock/liste.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("stock").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/stock/valo.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("EnhancedValue").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/product/stock/mouvement.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Movements").'</a>';
                        print '</li>';
					print '</ul>';		
					print '</li>';
				}
				// Expeditions
				if ($conf->expedition->enabled)
				{
					$langs->load("sendings");
					print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/expedition/index.php?'.$id.'=sendings" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Shipments").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/expedition/fiche.php?action=create2&'.$id.'=sendings" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewSending").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/expedition/liste.php?'.$id.'=sendings" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/expedition/stats/index.php?'.$id.'=sendings" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
                        print '</li>';
					print '</ul>';		
					print '</li>';
				}

				 
				print '</ul>';
		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print_text_menu_entry($chaine);
				print '</a>';
				print_end_menu_entry();
			}
		}
	}

	// Commercial
	$menuqualified=0;
    if (! empty($conf->propal->enabled)) $menuqualified++;
    if (! empty($conf->commande->enabled)) $menuqualified++;
    if (! empty($conf->fournisseur->enabled)) $menuqualified++;
    if (! empty($conf->contrat->enabled)) $menuqualified++;
    if (! empty($conf->ficheinter->enabled)) $menuqualified++;
    if ($menuqualified)
    {
		$langs->load("commercial");
		$langs->load("propal");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "commercial")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}

		$idsel='commercial';
		if($user->rights->societe->lire || $user->rights->societe->contact->lire)
		{
			print '<li class="dropdown '.$isactive.'">';
            print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'" href="'.DOL_URL_ROOT.'/societe/index.php?'.$id.'=commercial" onclick="javascript:func(this)">'.$langs->trans("Commercial").'<b class="caret"></b></a>';
				print '<ul class="dropdown-menu">';
				// Propal
					print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/comm/propal/index.php?'.$id.'=propals" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Prop").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/societe/societe.php?'.$id.'=propals" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewPropal").'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals&viewstatut=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("PropalsDraft").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals&viewstatut=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("PropalsOpened").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals&viewstatut=2" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("PropalStatusSigned").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals&viewstatut=3" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("PropalStatusNotSigned").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals&viewstatut=4" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("PropalStatusBilled").'</a>';
								print '</li>';
							print '</ul>';			
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/comm/propal/stats/index.php?'.$id.'=propals" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				 // Customers orders
				 $langs->load("orders");
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/commande/index.php?'.$id.'=orders" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)">'.$langs->trans("CustomersOrders").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/societe/societe.php?'.$id.'=orders" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewOrder").'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/comm/propal.php?'.$id.'=propals" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&viewstatut=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StatusOrderDraftShort").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&viewstatut=1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StatusOrderValidated").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&viewstatut=2" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StatusOrderOnProcessShort").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&viewstatut=3" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StatusOrderToBill").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&viewstatut=4" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StatusOrderProcessed").'</a>';
								print '</li>';
								print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&viewstatut=-1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StatusOrderCanceledShort").'</a>';
								print '</li>';
							print '</ul>';			
                        print '</li>';
						print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/commande/stats/index.php?'.$id.'=orders" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				 // Suppliers orders
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/fourn/commande/index.php?'.$id.'=orders_suppliers" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("SuppliersOrders").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/societe/societe.php?'.$id.'=orders_suppliers" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewOrder").'</a>';
                        print '</li>';
						print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/fourn/commande/liste.php?'.$id.'=orders_suppliers" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/commande/stats/index.php?'.$id.'=orders_suppliers" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				 // Contrat
				if (! empty($conf->contrat->enabled))
				{
					$langs->load("contracts");
					print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/contrat/index.php?'.$id.'=contracts" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Contracts").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/societe/societe.php?'.$id.'=contracts" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewContract").'</a>';
                        print '</li>';
						print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/contrat/liste.php?'.$id.'=contracts" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/contrat/services.php?'.$id.'=contracts" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuServices").'</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/contrat/services.php?'.$id.'=contracts&amp;mode=0" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuInactiveServices").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/contrat/services.php?'.$id.'=contracts&amp;mode=4" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuRunningServices").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/contrat/services.php?'.$id.'=contracts&amp;mode=4&amp;filter=expired" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuExpiredServices").'</a>';
								print '</li>';
								print '<li>';
								print '<a href="'.DOL_URL_ROOT.'/contrat/services.php?'.$id.'=contracts&amp;mode=5" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuClosedServices").'</a>';
								print '</li>';
							print '</ul>';			
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				}
				 // Interventions
				 $langs->load("interventions");
				  print '<li class="dropdown submenu">';
                  print '<a href="'.DOL_URL_ROOT.'/fichinter/list.php?'.$id.'=ficheinter" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Interventions").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/fichinter/fiche.php?action=create&'.$id.'=ficheinter" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewIntervention").'</a>';
                        print '</li>';
						print '<li>';
                        print '<a href="'.DOL_URL_ROOT.'/fichinter/list.php?'.$id.'=ficheinter" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
			print '</ul>';
		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print print_text_menu_entry($langs->trans("Commercial"));
				print '</a>';
				print_end_menu_entry();
			}
		}
	}

	// Financial
	if ($conf->comptabilite->enabled || $conf->accounting->enabled
	|| $conf->facture->enabled || $conf->deplacement->enabled || $conf->don->enabled || $conf->tax->enabled)
	{
		$langs->load("compta");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "accountancy")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}

		$idsel='accountancy';
		if ($user->rights->compta->resultat->lire || $user->rights->accounting->plancompte->lire
		|| $user->rights->facture->lire || $user->rights->deplacement->lire || $user->rights->don->lire || $user->rights->tax->charges->lire)
		{
			print '<li class="dropdown '.$isactive.'">';
            print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'" href="'.DOL_URL_ROOT.'/compta/index.php?'.$id.'=accountancy" onclick="javascript:func(this)">';
			print_text_menu_entry($langs->trans("MenuFinancial"));
			print '<b class="caret"></b></a>';
				print '<ul class="dropdown-menu">';
					// Customers invoices
					$langs->load("bills");
					print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/facture.php?'.$id.'=customers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("BillsCustomers").'</a>';
						print '<ul class="dropdown-menu submenu-show submenu-hide">';
							print '<li>';
							print '<a href="'.DOL_URL_ROOT.'/compta/clients.php?action=facturer&amp;'.$id.'=customers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewBill").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/facture/fiche-rec.php?'.$id.'=customers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Repeatables").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/facture/impayees.php?'.$id.'=customers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Unpaid").'</a>';
							print '</li>';
							print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/compta/paiement/liste.php?'.$id.'=customers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Payments").'</a>';
								print '<ul class="dropdown-menu submenu-show submenu-hide">';
								if ($conf->global->BILL_ADD_PAYMENT_VALIDATION)
								{
									print '<li><a href="'.DOL_URL_ROOT.'/compta/paiement/avalider.php?'.$id.'=customers_bills">'.$langs->trans("MenuToValid").'</a></li>';
								}
									print '<li><a href="'.DOL_URL_ROOT.'/compta/paiement/rapport.php?'.$id.'=customers_bills">'.$langs->trans("Reportings").'</a></li>';
								print '</ul>';
									print '</li>';
							print '<li>';
							print '<a href="'.DOL_URL_ROOT.'/compta/facture/stats/index.php?'.$id.'=customers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
							print '</li>';
						print '</ul>';			
					print '</li>';
					// Suppliers
				    print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/fourn/facture/index.php?'.$id.'=suppliers_bills" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("BillsSuppliers").'</a>';
						print '<ul class="dropdown-menu submenu-show submenu-hide">';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/fourn/facture/fiche.php?action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewBill").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/fourn/facture/impayees.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Unpaid").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/fourn/facture/paiement.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Payments").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/facture/stats/index.php?'.$id.'=suppliers_bills&mode=supplier" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
							print '</li>';
						print '</ul>';			
					print '</li>';
				  // Orders
				  $langs->load("orders");
				  print '<li>';
                    print '<a href="'.DOL_URL_ROOT.'/commande/liste.php?'.$id.'=orders&amp;viewstatut=3" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuOrdersToBill").'</a>';
				  print '</li>';
				  // Donations
				  $langs->load("donations");
				if (! empty($conf->don->enabled))
				{
				    print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/index.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Donations").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/dons/fiche.php?action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewDonation").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/dons/liste.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
							print '</li>';
						print '</ul>';		
				    print '</li>';
				}
				// Trips and expenses
				if (! empty($conf->deplacement->enabled))
				{
					$langs->load("trips");
				    print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/deplacement/index.php?'.$id.'=tripsandexpenses&amp;mainmenu=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("TripsAndExpenses").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/deplacement/fiche.php?action=create&'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("New").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/deplacement/list.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
							print '</li>';
							print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/deplacement/stats/index.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Statistics").'</a>';
							print '</li>';
						print '</ul>';		
				    print '</li>';
				}
				// Taxes and social contributions
				if (! empty($conf->tax->enabled))
				{
				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/charges/index.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuTaxAndDividends").'</a>';
						print '<ul class="dropdown-menu submenu-show submenu-hide">';
							print '<li class="dropdown submenu">';
							print '<a href="'.DOL_URL_ROOT.'/compta/sociales/index.php?'.$id.'=tax_social" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuSocialContributions").'</a>';											
								print '<ul class="dropdown-menu submenu-show submenu-hide">';								
									print '<li><a href="'.DOL_URL_ROOT.'/compta/sociales/charges.php?'.$id.'=tax_social&action=create">'.$langs->trans("MenuNewSocialContribution").'</a></li>';				
									print '<li><a href="'.DOL_URL_ROOT.'/compta/charges/index.php?'.$id.'=tax_social&amp;mainmenu=accountancy&amp;mode=sconly">'.$langs->trans("Payments").'</a></li>';
								print '</ul>';
							print '</li>';	
							// VAT
							if (empty($conf->global->TAX_DISABLE_VAT_MENUS))
							{
								$langs->load("bills");
								print '<li class="dropdown submenu">';
								print '<a href="'.DOL_URL_ROOT.'/compta/tva/index.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("VAT").'</a>';											
									print '<ul class="dropdown-menu submenu-show submenu-hide">';								
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/fiche.php?'.$id.'=tax_vat&action=create">'.$langs->trans("NewPayment").'</a></li>';				
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/reglement.php?'.$id.'=tax_vat">'.$langs->trans("Payments").'</a></li>';
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/clients.php?'.$id.'=tax_vat">'.$langs->trans("ReportByCustomers").'</a></li>';
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/quadri_detail.php?'.$id.'=tax_vat">'.$langs->trans("ReportByQuarter").'</a></li>';
									print '</ul>';
								print '</li>';					
				
						  //Local Taxes
							if($mysoc->country_code=='ES' && $mysoc->localtax2_assuj=="1")
							{
								print '<li class="dropdown submenu">';
										print '<a href="'.DOL_URL_ROOT.'/compta/localtax/index.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->transcountry("LT2",$mysoc->country_code).'</a>';											
									print '<ul class="dropdown-menu submenu-show submenu-hide">';								
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/fiche.php?'.$id.'=tax_vat&action=create">'.$langs->trans("NewPayment").'</a></li>';				
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/reglement.php?'.$id.'=tax_vat">'.$langs->trans("Payments").'</a></li>';
										print '<li><a href="'.DOL_URL_ROOT.'/compta/tva/clients.php?'.$id.'=tax_vat">'.$langs->trans("ReportByCustomers").'</a></li>';
									print '</ul>';
								print '</li>';
							}					
						}				
					}
						print '</ul>';			
				  print '</li>';
				  // Rapports
				  // Bilan, resultats
				   print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/resultat/index.php?'.$id.'=accountancy" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Reportings").'</a>';
						print '<ul class="dropdown-menu submenu-show submenu-hide">';
							print '<li class="dropdown submenu">';
							print '<a href="'.DOL_URL_ROOT.'/compta/resultat/index.php?'.$id.'=ca" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ReportInOut").'</a>';											
								print '<ul class="dropdown-menu submenu-show submenu-hide">';								
									print '<li><a href="'.DOL_URL_ROOT.'/compta/resultat/clientfourn.php?'.$id.'=tax_social&action=create">'.$langs->trans("ByCompanies").'</a></li>';				
								print '</ul>';
							print '</li>';	
							
								print '<li class="dropdown submenu">';
								print '<a href="'.DOL_URL_ROOT.'/compta/stats/index.php?'.$id.'=ca" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ReportTurnover").'</a>';											
									print '<ul class="dropdown-menu submenu-show submenu-hide">';								
										print '<li><a href="'.DOL_URL_ROOT.'/compta/stats/casoc.php?'.$id.'=ca">'.$langs->trans("ByCompanies").'</a></li>';				
										print '<li><a href="'.DOL_URL_ROOT.'/compta/stats/cabyuser.php?'.$id.'=ca">'.$langs->trans("ByUsers").'</a></li>';
									print '</ul>';
								print '</li>';
								print '<li><a href="'.DOL_URL_ROOT.'/compta/journal/sellsjournal.php?'.$id.'=ca">'.$langs->trans("SellsJournal").'</a></li>';	
								print '<li><a href="'.DOL_URL_ROOT.'/compta/journal/purchasesjournal.php?'.$id.'=ca">'.$langs->trans("PurchasesJournal").'</a></li>';
							print '</ul>';			
				  print '</li>';
			  print '</ul>';
		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print_text_menu_entry($langs->trans("MenuFinancial"));
				print '</a>';
				print_end_menu_entry();
			}
		}
	}

    // Bank
    if ($conf->banque->enabled || $conf->prelevement->enabled)
    {
        $langs->load("compta");
        $langs->load("banks");
		$langs->load("withdrawals");
		$langs->load("bills");

        $isactive="";
        if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "bank")
        {
            $isactive='active'; $_SESSION['idmenu']='';
        }
        else
        {
            $isactive = '';
        }
		
        $idsel='bank';
        if ($user->rights->banque->lire)
        {
			// Bank-Caisse
			 print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'"  href="'.DOL_URL_ROOT.'/compta/bank/index.php?'.$id.'=bank" onclick="javascript:func(this)" >';
			  print_text_menu_entry($langs->trans("MenuBankCash"));
			  print '<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/bank/index.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuBankCash").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/fiche.php?action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuNewFinancialAccount").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/categ.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Rubriques").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/search.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ListTransactions").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/budget.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ListTransactionsByCategory").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/virement.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("BankTransfers").'</a>';
                        print '</li>';
					print '</ul>';
                print '</li>';
				// Prelevements
				if (! empty($conf->prelevement->enabled))
				{
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/prelevement/index.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("StandingOrders").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/fiche.php?action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuNewFinancialAccount").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/categ.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Rubriques").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/search.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ListTransactions").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/budget.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("ListTransactionsByCategory").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/virement.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("BankTransfers").'</a>';
                        print '</li>';
					print '</ul>';

                print '</li>';
				}
				// Gestion cheques
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/compta/paiement/cheque/index.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuChequeDeposits").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/paiement/cheque/fiche.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewChequeDeposit").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/compta/paiement/cheque/liste.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
					print '</ul>';
                print '</li>';

				print '<li>';
                    print '<a href="'.DOL_URL_ROOT.'/compta/bank/index.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("BankAccounts").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/compta/bank/index.php?'.$id.'=bank" class="dropdown-toggle" data-toggle="dropdown" onclick="javascript:func(this)" >Level 4.1</a>';
							print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="#">Level 4.1.1</a></li>';
                            print '</ul>';
                        print '</li>';
					print '</ul>';
                print '</li>';

			print '</ul>';
        }
        else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
        {
            if (! $type_user)
            {
                print_start_menu_entry($idsel);
                print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
                print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
                print_text_menu_entry($langs->trans("MenuBankCash"));
                print '</a>';
                print_end_menu_entry();
            }
        }
    }

	// Projects
	if ($conf->projet->enabled)
	{
		$langs->load("projects");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "project")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}

		$idsel='project';
		if ($user->rights->projet->lire)
		{
			// Project affected to user
			print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'" href="'.DOL_URL_ROOT.'/projet/index.php?'.$id.'=project" onclick="javascript:func(this)">';
			  print_text_menu_entry($langs->trans("Projects"));
			  print '<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/projet/index.php?'.$id.'=project&mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MyProjects").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/fiche.php?'.$id.'=project&action=create&mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewProject").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/liste.php?'.$id.'=project&mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				// All project i have permission on
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/projet/index.php?'.$id.'=projects" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Projects").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/fiche.php?'.$id.'=projects&action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewProject").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/liste.php?'.$id.'=projects" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				// Project affected to user
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/projet/activity/index.php?mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MyActivities").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/tasks.php?action=create&mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewTask").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/tasks/index.php?mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/activity/list.php?mode=mine" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewTimeSpent").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				// All project i have permission on
				 print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/projet/activity/index.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Activities").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/tasks.php?action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewTask").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/tasks/index.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/projet/activity/list.php" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewTimeSpent").'</a>';
                        print '</li>';
					print '</ul>';			
				 print '</li>';
				
				print '</ul>';
		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print_text_menu_entry($langs->trans("Projects"));
				print '</a>';
				print_end_menu_entry();
			}
		}
	}

	// Tools
	if ($conf->mailing->enabled || $conf->export->enabled || $conf->import->enabled)
	{
		$langs->load("other");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "tools")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}

		$idsel='tools';
		if ($user->rights->mailing->lire || $user->rights->export->lire || $user->rights->import->run)
		{
			$langs->load("mails");
			print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'" href="'.DOL_URL_ROOT.'/core/tools.php?'.$id.'=tools" onclick="javascript:func(this)">';
			  print_text_menu_entry($langs->trans("Tools"));
			  print '<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/comm/mailing/index.php?'.$id.'=mailing" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("EMailings").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/comm/mailing/fiche.php?'.$id.'=mailing&amp;action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewMailing").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/comm/mailing/liste.php?'.$id.'=mailing" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
					
					$langs->load("exports");
				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/exports/index.php?'.$id.'=export" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("FormatedExport").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/exports/export.php?'.$id.'=export" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewExport").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';

				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/imports/index.php?'.$id.'=import" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("FormatedImport").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/imports/import.php?'.$id.'=import" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewImport").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
				  
			print '</ul>';	
		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled"  id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print_text_menu_entry($langs->trans("Tools"));
				print '</a>';
				print_end_menu_entry();
			}
		}
	}

	// OSCommerce 1
	if (! empty($conf->boutique->enabled))
	{
		$langs->load("shop");

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "shop")
		{
			$isactive='class="active"'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}

		$idsel='shop';
		print '<li '.$isactive.'>';
			print '<a href="'.DOL_URL_ROOT.'/boutique/index.php?mainmenu=shop&amp;leftmenu="'.($atarget?' target="'.$atarget.'"':'').'>';
			print_text_menu_entry($langs->trans("OSCommerce"));
			print '</a>';
		print '</li>';
	}

	// Members
	if ($conf->adherent->enabled)
	{
		// $langs->load("members"); Added in main file

		$isactive="";
		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "members")
		{
			$isactive='active'; $_SESSION['idmenu']='';
		}
		else
		{
			$isactive = '';
		}

		$idsel='members';
		if ($user->rights->adherent->lire)
		{
			$langs->load("members");
			print '<li class="dropdown '.$isactive.'">';
              print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'"  href="'.DOL_URL_ROOT.'/adherents/index.php?'.$id.'=members" onclick="javascript:func(this)" >';
			  print_text_menu_entry($langs->trans("MenuMembers"));
			  print '<b class="caret"></b></a>';
              print '<ul class="dropdown-menu">';
                print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/adherents/index.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Members").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/fiche.php?'.$id.'=members&amp;action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewMember").'</a>';
                        print '</li>';
						print '<li class="dropdown submenu">';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                            print '<ul class="dropdown-menu submenu-show submenu-hide">';
                                    print '<li><a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members&amp;statut=-1">'.$langs->trans("MenuMembersToValidate").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members&amp;statut=1">'.$langs->trans("MenuMembersValidated").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members&amp;statut=1&amp;filter=uptodate">'.$langs->trans("MenuMembersUpToDate").'</a></li>';
                                    print '<li><a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members&amp;statut=1&amp;filter=outofdate">'.$langs->trans("MenuMembersNotUpToDate").'</a></li>';
									print '<li><a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members&amp;statut=0">'.$langs->trans("MenuMembersResiliated").'</a></li>';
                            print '</ul>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/stats/geo.php?'.$id.'=members&mode=memberbycountry" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuMembersStats").'</a>';
                        print '</li>';						
					print '</ul>';
                print '</li>';

				print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/adherents/index.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Subscriptions").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/liste.php?'.$id.'=members&amp;statut=-1" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewSubscription").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/cotisations.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/stats/index.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MenuMembersStats").'</a>';
                        print '</li>';
					print '</ul>';			
				  print '</li>';
				if ($conf->categorie->enabled)
                {
				    print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/categories/index.php?'.$id.'=cat&amp;type=3" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Categories").'</a>';
					print '<ul class="dropdown-menu submenu-show submenu-hide">';
					if ($user->societe_id == 0)
                    {
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/categories/fiche.php?action=create&amp;type=3" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("NewCategory").'</a>';
                        print '</li>';
					}
					print '</ul>';			
					print '</li>';
				}

				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/adherents/index.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Exports").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/exports/index.php?'.$id.'=export" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Datas").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/htpasswd.php?'.$id.'=export" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("Filehtpasswd").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/cartes/carte.php?'.$id.'=export" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MembersCards").'</a>';
                        print '</li>';
					print '</ul>';
                print '</li>';
				// Type
				  print '<li class="dropdown submenu">';
                    print '<a href="'.DOL_URL_ROOT.'/adherents/type.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("MembersTypes").'</a>';
                    print '<ul class="dropdown-menu submenu-show submenu-hide">';
                        print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/type.php?'.$id.'=members&amp;action=create" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("New").'</a>';
                        print '</li>';
						print '<li>';
                            print '<a href="'.DOL_URL_ROOT.'/adherents/type.php?'.$id.'=members" class="dropdown-toggle" data-toggle="dropdown">'.$langs->trans("List").'</a>';
                        print '</li>';
					print '</ul>';
                print '</li>';

				print '</ul>';


		}
		else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
		{
			if (! $type_user)
			{
				print_start_menu_entry($idsel);
				print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
				print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
				print_text_menu_entry($langs->trans("MenuMembers"));
				print '</a>';
				print_end_menu_entry();
			}
		}
	}


	// Show personalized menus
	require_once(DOL_DOCUMENT_ROOT."/core/class/menubase.class.php");

    $tabMenu=array();
	$menuArbo = new Menubase($db,'bootstrap','top');
	$newTabMenu = $menuArbo->menuTopCharger('','',$type_user,'bootstrap',$tabMenu);

	$num = count($newTabMenu);
	for($i = 0; $i < $num; $i++)
	{
		if ($newTabMenu[$i]['enabled'] == true)
		{
			//var_dump($newTabMenu[$i]);

			$idsel=(empty($newTabMenu[$i]['mainmenu'])?'none':$newTabMenu[$i]['mainmenu']);
			if ($newTabMenu[$i]['perms'] == true)	// Is allowed
			{
				if (preg_match("/^(http:\/\/|https:\/\/)/i",$newTabMenu[$i]['url']))
				{
					$url = $newTabMenu[$i]['url'];
				}
				else
				{
					$url=dol_buildpath($newTabMenu[$i]['url'],1);
					if (! preg_match('/mainmenu/i',$url) || ! preg_match('/leftmenu/i',$url))
					{
                        if (! preg_match('/\?/',$url)) $url.='?';
                        else $url.='&';
					    $url.='mainmenu='.$newTabMenu[$i]['mainmenu'].'&leftmenu=';
					}
					//$url.="idmenu=".$newTabMenu[$i]['rowid'];    // Already done by menuLoad
				}
				$url=preg_replace('/__LOGIN__/',$user->login,$url);

				// Define the class (top menu selected or not)
				if (! empty($_SESSION['idmenu']) && $newTabMenu[$i]['rowid'] == $_SESSION['idmenu']) $isactive='active';
				else if (! empty($_SESSION["mainmenu"]) && $newTabMenu[$i]['mainmenu'] == $_SESSION["mainmenu"]) $isactive='active';
				else $isactive='';

				print '<li '.$isactive.'>';
					print '<a href="'.$url.'"'.($newTabMenu[$i]['target']?" target='".$newTabMenu[$i]['target']."'":($atarget?' target="'.$atarget.'"':'')).'>';
					print_text_menu_entry($newTabMenu[$i]['titre']);
					print '</a>';
				print '</li>';
			}
			else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
			{
				if (! $type_user)
				{
					print_start_menu_entry($idsel);
					print '<div class="'.$id.' '.$idsel.'"><span class="'.$id.' tmenuimage" id="mainmenuspan_'.$idsel.'"></span></div>';
					print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
					print_text_menu_entry($newTabMenu[$i]['titre']);
					print '</a>';
					print_end_menu_entry();
				}
			}
		}
	}
	print '</li>';
	print '</ul>';

	
		  print '<ul class="nav pull-right">';
		  print '<li id="fat-menu" class="dropdown '.$isactive.'">';
		  print '<a data-toggle="dropdown" class="dropdown-toggle '.$isactive.'"  href="'.DOL_URL_ROOT.'/user/fiche.php?id='.$user->id.'" onclick="javascript:func(this)" >'.$user->login.'<b class="caret"></b></a>';
			print '<ul class="dropdown-menu">';
								if (! empty($conf->multicompany->enabled))
								{
                                    print '<li><a href="'.dol_buildpath('/multicompany/admin/multicompany.php',1).'">'.$langs->trans("Multicompany").'</a></li>';
								}
									print '<li><a href="'.$_SERVER["PHP_SELF"].'?'.'optioncss=print">'.$langs->trans("PrintContentArea").'</a></li>';
									print '<li class="divider"/>';
									print '<li><a href="'.DOL_URL_ROOT.'/user/logout.php">'.$langs->trans("Logout").'</a></li>';
                    
			print '</ul>';
			print '</li>';
			print '</ul>';
        print '</div>'; //<!-- /.nav-collapse -->
	print '</div>';
  print '</div>';
print '</div>';

}

function printBootSearchForm()
	{
		global $conf, $langs, $user;
		
		$searchform=array();
		
		if ($conf->societe->enabled && $user->rights->societe->lire)
		{
			$langs->load("companies");
			
				$searchform['thirdparty'] = array(
						'url' => DOL_URL_ROOT.'/societe/societe.php',
						'title' => $langs->trans("ThirdParties"),
						'htmlname' => 'socname'
				);
				$searchform['contact'] = array(
						'url' => DOL_URL_ROOT.'/contact/list.php',
						'title' => $langs->trans("Contacts"),
						'htmlname' => 'contactname'
				);
				
		}
		if (($conf->product->enabled && $user->rights->produit->lire) || ($conf->service->enabled && $user->rights->service->lire)) 
		{
			$langs->load("products");
			$searchform['product'] = array(
					'url' => DOL_URL_ROOT.'/product/liste.php',
					'title' => $langs->trans("Products")."/".$langs->trans("Services"),
					'htmlname' => 'sall'
				);
		}
		if ($conf->adherent->enabled && $user->rights->adherent->lire)
		{
			$langs->load("members");
			$searchform['member'] = array(
					'url' => DOL_URL_ROOT.'/adherents/liste.php',
					'title' => $langs->trans("Members"),
					'htmlname' => 'sall'
			);
		}
		
		$ret='';
		
		$ret.= '<script type="text/javascript">';
		$ret.= '$(document).ready(function() {
					var searchform = $.parseJSON(\''.json_encode($searchform).'\');
					setFields(searchform);
					$(\'#bo_search_type\').change(function() {	
						setFields(searchform);
					});
					function setFields(searchform) {
						var select = $(\'#bo_search_type option:selected\').val();
						$.each(searchform, function(key, params) {
							if (key == select) {
								$("#multisearch").attr("action", params.url);
								$("#bo_query").attr("name", params.htmlname);
							}
						});
					}
				});';
		$ret.= '</script>';
		
		$ret.='<div id="header_search">';
		$ret.='<form id="multisearch" action="" method="post">';
		$ret.='<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$ret.='<input type="text" id="bo_query" placeholder="'.$langs->trans("SearchOf").'" name="">';
		
		$ret.='<select id="bo_search_type" name="bo_search_type">';
		foreach($searchform as $key => $params)
		{
			$ret.='<option value="'.$key.'">'.$params['title'].'</option>';
		}
		$ret.='</select>';
		$ret.='<button type="submit" id="bo_search_submit">'.$langs->trans("Search").'</button>';

		$ret.="</form>\n";
		$ret.='</div>';
		
		return $ret;
	}		

		  print printBootSearchForm();			    	  




function print_start_menu_array()
{
	global $conf;
	if (preg_match('/bluelagoon|eldy|freelug|rodolphe|yellow|dev/',$conf->css)) print '<table class="navbar" summary="topmenu"><tr class="navbar">';
	else print '<ul class="navbar">';
}

/**
 * Output start menu entry
 *
 * @param	string	$idsel		Text
 * @return	void
 */
function print_start_menu_entry($idsel)
{
	global $conf;
	if (preg_match('/bluelagoon|eldy|freelug|rodolphe|yellow|dev/',$conf->css)) print '<td class="navbar" id="mainmenutd_'.$idsel.'">';
	else print '<li class="navbar" id="mainmenutd_'.$idsel.'">';
}

/**
 * Output menu entry
 *
 * @param	string	$text		Text
 * @return	void
 */
function print_text_menu_entry($text)
{
	global $conf;
	print '<span>';
	print $text;
	print '</span>';
}

/**
 * Output end menu entry
 *
 * @return	void
 */
function print_end_menu_entry()
{
	global $conf;
	if (preg_match('/bluelagoon|eldy|freelug|rodolphe|yellow|dev/',$conf->css)) print '</td>';
	else print '</li>';
	print "\n";
}

/**
 * Output menu array
 *
 * @return	void
 */
function print_end_menu_array()
{
	global $conf;
	if (preg_match('/bluelagoon|eldy|freelug|rodolphe|yellow|dev/',$conf->css)) print '</tr></table>';
	else print '</ul>';
	print "\n";
}



/**
 * Core function to output left menu eldy
 *
 * @param	DoliDB		$db                  Database handler
 * @param 	array		$menu_array_before   Table of menu entries to show before entries of menu handler
 * @param   array		$menu_array_after    Table of menu entries to show after entries of menu handler
 * @return	void
 */
function print_left_bootstrap_menu($db,$menu_array_before,$menu_array_after)
{
    global $user,$conf,$langs,$dolibarr_main_db_name,$mysoc;

    // Read mainmenu and leftmenu that define which menu to show
    if (isset($_GET["mainmenu"]))
    {
        // On sauve en session le menu principal choisi
        $mainmenu=$_GET["mainmenu"];
        $_SESSION["mainmenu"]=$mainmenu;
        $_SESSION["leftmenuopened"]="";
    }
    else
    {
        // On va le chercher en session si non defini par le lien
        $mainmenu=isset($_SESSION["mainmenu"])?$_SESSION["mainmenu"]:'';
    }

    if (isset($_GET["leftmenu"]))
    {
        // On sauve en session le menu principal choisi
        $leftmenu=$_GET["leftmenu"];
        $_SESSION["leftmenu"]=$leftmenu;
        if ($_SESSION["leftmenuopened"]==$leftmenu)
        {
            //$leftmenu="";
            $_SESSION["leftmenuopened"]="";
        }
        else
        {
            $_SESSION["leftmenuopened"]=$leftmenu;
        }
    } else {
        // On va le chercher en session si non defini par le lien
        $leftmenu=isset($_SESSION["leftmenu"])?$_SESSION["leftmenu"]:'';
    }

    $newmenu = new Menu();

    // Show logo company
    if (! empty($conf->global->MAIN_SHOW_LOGO))
    {
        $mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
        if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))
        {
            $urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_mini);
            print "\n".'<!-- Show logo on menu -->'."\n";
            print '<div class="blockvmenuimpair">'."\n";
            print '<div class="menu_titre" id="menu_titre_logo"></div>';
            print '<div class="menu_top" id="menu_top_logo"></div>';
            print '<div class="menu_contenu" id="menu_contenu_logo">';
            print '<center><img title="" src="'.$urllogo.'"></center>'."\n";
            print '</div>';
            print '<div class="menu_end" id="menu_end_logo"></div>';
            print '</div>'."\n";
        }
    }

    /**
     * On definit newmenu en fonction de mainmenu et leftmenu
     * ------------------------------------------------------
     */
    if ($mainmenu)
    {

        /*
         * Menu HOME
         */
        if ($mainmenu == 'home')
        {
            $langs->load("users");

            if ($user->admin)
            {
                $langs->load("admin");
                $langs->load("help");

                // Setup
                $newmenu->add("/admin/index.php?mainmenu=home&leftmenu=setup", $langs->trans("Setup"), 0, 1, '', $mainmenu, 'setup');
                if ($leftmenu=="setup")
                {
                    $newmenu->add("/admin/company.php?mainmenu=home", $langs->trans("MenuCompanySetup"),1);
                    $newmenu->add("/admin/modules.php?mainmenu=home", $langs->trans("Modules"),1);
                    $newmenu->add("/admin/menus.php?mainmenu=home", $langs->trans("Menus"),1);
                    $newmenu->add("/admin/ihm.php?mainmenu=home", $langs->trans("GUISetup"),1);
                    if (! in_array($langs->defaultlang,array('en_US','en_GB','en_NZ','en_AU','fr_FR','fr_BE','es_ES','ca_ES')))
                    {
                        if ($leftmenu=="setup") $newmenu->add("/admin/translation.php", $langs->trans("Translation"),1);
                    }
                    $newmenu->add("/admin/boxes.php?mainmenu=home", $langs->trans("Boxes"),1);
                    $newmenu->add("/admin/delais.php?mainmenu=home",$langs->trans("Alerts"),1);
                    $newmenu->add("/admin/proxy.php?mainmenu=home", $langs->trans("Security"),1);
                    $newmenu->add("/admin/limits.php?mainmenu=home", $langs->trans("MenuLimits"),1);
                    $newmenu->add("/admin/pdf.php?mainmenu=home", $langs->trans("PDF"),1);
                    $newmenu->add("/admin/mails.php?mainmenu=home", $langs->trans("Emails"),1);
                    $newmenu->add("/admin/sms.php?mainmenu=home", $langs->trans("Sms"),1);
                    $newmenu->add("/admin/dict.php?mainmenu=home", $langs->trans("DictionnarySetup"),1);
                    $newmenu->add("/admin/const.php?mainmenu=home", $langs->trans("OtherSetup"),1);
                }

                // System info
                $newmenu->add("/admin/system/index.php?mainmenu=home&leftmenu=system", $langs->trans("SystemInfo"), 0, 1, '', $mainmenu, 'system');
                if ($leftmenu=="system")
                {
                    $newmenu->add("/admin/system/dolibarr.php?mainmenu=home", $langs->trans("Dolibarr"),1);
                    $newmenu->add("/admin/system/constall.php?mainmenu=home", $langs->trans("AllParameters"),2);
                    $newmenu->add("/admin/system/modules.php?mainmenu=home", $langs->trans("Modules"),2);
                    $newmenu->add("/admin/triggers.php?mainmenu=home", $langs->trans("Triggers"),2);
                    $newmenu->add("/admin/system/about.php?mainmenu=home", $langs->trans("About"),2);
                    $newmenu->add("/admin/system/os.php?mainmenu=home", $langs->trans("OS"),1);
                    $newmenu->add("/admin/system/web.php?mainmenu=home", $langs->trans("WebServer"),1);
                    $newmenu->add("/admin/system/phpinfo.php?mainmenu=home", $langs->trans("Php"),1);
                    //if (function_exists('xdebug_is_enabled')) $newmenu->add("/admin/system/xdebug.php", $langs->trans("XDebug"),1);
                    $newmenu->add("/admin/system/database.php?mainmenu=home", $langs->trans("Database"),1);
                    $newmenu->add("/admin/system/database-tables.php?mainmenu=home", $langs->trans("Tables"),2);
                    $newmenu->add("/admin/system/database-tables-contraintes.php?mainmenu=home", $langs->trans("Constraints"),2);
                }
                // System info
                $newmenu->add("/admin/tools/index.php?mainmenu=home&leftmenu=admintools", $langs->trans("SystemTools"), 0, 1, '', $mainmenu, 'admintools');
                if ($leftmenu=="admintools")
                {
                    $newmenu->add("/admin/tools/dolibarr_export.php?mainmenu=home", $langs->trans("Backup"),1);
                    $newmenu->add("/admin/tools/dolibarr_import.php?mainmenu=home", $langs->trans("Restore"),1);
                    $newmenu->add("/admin/tools/update.php?mainmenu=home", $langs->trans("MenuUpgrade"),1);
                    if (function_exists('eaccelerator_info')) $newmenu->add("/admin/tools/eaccelerator.php?mainmenu=home", $langs->trans("EAccelerator"),1);
                    $newmenu->add("/admin/tools/listevents.php?mainmenu=home", $langs->trans("Audit"),1);
                    $newmenu->add("/admin/tools/listsessions.php?mainmenu=home", $langs->trans("Sessions"),1);
                    $newmenu->add("/admin/tools/purge.php?mainmenu=home", $langs->trans("Purge"),1);
                    $newmenu->add("/support/index.php?mainmenu=home", $langs->trans("HelpCenter"),1,1,'targethelp');
                }
            }

            $newmenu->add("/user/home.php?leftmenu=users", $langs->trans("MenuUsersAndGroups"), 0, 1, '', $mainmenu, 'users');
            if ($leftmenu=="users")
            {
                $newmenu->add("/user/index.php", $langs->trans("Users"), 1, $user->rights->user->user->lire || $user->admin);
                $newmenu->add("/user/fiche.php?action=create", $langs->trans("NewUser"),2, $user->rights->user->user->creer || $user->admin);
                $newmenu->add("/user/group/index.php", $langs->trans("Groups"), 1, ($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->read:$user->rights->user->user->lire) || $user->admin);
                $newmenu->add("/user/group/fiche.php?action=create", $langs->trans("NewGroup"), 2, ($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->write:$user->rights->user->user->creer) || $user->admin);
            }
        }


        /*
         * Menu TIERS
         */
        if ($mainmenu == 'companies')
        {
            // Societes
            if ($conf->societe->enabled)
            {
                $langs->load("companies");
                $newmenu->add("/societe/index.php?leftmenu=thirdparties", $langs->trans("ThirdParty"), 0, $user->rights->societe->lire, '', $mainmenu, 'thirdparties');

                if ($user->rights->societe->creer)
                {
                    $newmenu->add("/societe/soc.php?action=create", $langs->trans("MenuNewThirdParty"),1);
                    if (! $conf->use_javascript_ajax) $newmenu->add("/societe/soc.php?action=create&amp;private=1",$langs->trans("MenuNewPrivateIndividual"),1);
                }

                // TODO Avoid doing dir scan
                if(is_dir("societe/groupe"))
                {
                    $newmenu->add("/societe/groupe/index.php", $langs->trans("MenuSocGroup"),1);
                }
            }

            // Prospects
            if ($conf->societe->enabled && empty($conf->global->SOCIETE_DISABLE_PROSPECTS))
            {
                $langs->load("commercial");
                $newmenu->add("/comm/prospect/list.php?leftmenu=prospects", $langs->trans("ListProspectsShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'prospects');

                if ($leftmenu=="prospects") $newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=-1", $langs->trans("LastProspectDoNotContact"), 2, $user->rights->societe->lire);
                if ($leftmenu=="prospects") $newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=0", $langs->trans("LastProspectNeverContacted"), 2, $user->rights->societe->lire);
                if ($leftmenu=="prospects") $newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=1", $langs->trans("LastProspectToContact"), 2, $user->rights->societe->lire);
                if ($leftmenu=="prospects") $newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=2", $langs->trans("LastProspectContactInProcess"), 2, $user->rights->societe->lire);
                if ($leftmenu=="prospects") $newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=3", $langs->trans("LastProspectContactDone"), 2, $user->rights->societe->lire);

                $newmenu->add("/societe/soc.php?leftmenu=prospects&amp;action=create&amp;type=p", $langs->trans("MenuNewProspect"), 2, $user->rights->societe->creer);
                //$newmenu->add("/contact/list.php?leftmenu=customers&amp;type=p", $langs->trans("Contacts"), 2, $user->rights->societe->contact->lire);
            }

            // Clients
            if ($conf->societe->enabled)
            {
                $langs->load("commercial");
                $newmenu->add("/comm/list.php?leftmenu=customers", $langs->trans("ListCustomersShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'customers');

                $newmenu->add("/societe/soc.php?leftmenu=customers&amp;action=create&amp;type=c", $langs->trans("MenuNewCustomer"), 2, $user->rights->societe->creer);
                //$newmenu->add("/contact/list.php?leftmenu=customers&amp;type=c", $langs->trans("Contacts"), 2, $user->rights->societe->contact->lire);
            }

            // Fournisseurs
            if ($conf->societe->enabled && $conf->fournisseur->enabled)
            {
                $langs->load("suppliers");
                $newmenu->add("/fourn/liste.php?leftmenu=suppliers", $langs->trans("ListSuppliersShort"), 1, $user->rights->societe->lire && $user->rights->fournisseur->lire, '', $mainmenu, 'suppliers');

                if ($user->societe_id == 0)
                {
                    $newmenu->add("/societe/soc.php?leftmenu=suppliers&amp;action=create&amp;type=f",$langs->trans("MenuNewSupplier"), 2, $user->rights->societe->creer && $user->rights->fournisseur->lire);
                }
                //$newmenu->add("/fourn/liste.php?leftmenu=suppliers", $langs->trans("List"), 2, $user->rights->societe->lire && $user->rights->fournisseur->lire);
                //$newmenu->add("/contact/list.php?leftmenu=suppliers&amp;type=f",$langs->trans("Contacts"), 2, $user->rights->societe->lire && $user->rights->fournisseur->lire && $user->rights->societe->contact->lire);
            }

            // Contacts
            $newmenu->add("/contact/list.php?leftmenu=contacts", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("Contacts") : $langs->trans("ContactsAddresses")), 0, $user->rights->societe->contact->lire, '', $mainmenu, 'contacts');
            $newmenu->add("/contact/fiche.php?leftmenu=contacts&amp;action=create", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("NewContact") : $langs->trans("NewContactAddress")), 1, $user->rights->societe->contact->creer);
            $newmenu->add("/contact/list.php?leftmenu=contacts", $langs->trans("List"), 1, $user->rights->societe->contact->lire);
            if (empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=p", $langs->trans("Prospects"), 2, $user->rights->societe->contact->lire);
            $newmenu->add("/contact/list.php?leftmenu=contacts&type=c", $langs->trans("Customers"), 2, $user->rights->societe->contact->lire);
            if ($conf->fournisseur->enabled) $newmenu->add("/contact/list.php?leftmenu=contacts&type=f", $langs->trans("Suppliers"), 2, $user->rights->societe->contact->lire);
            $newmenu->add("/contact/list.php?leftmenu=contacts&type=o", $langs->trans("Others"), 2, $user->rights->societe->contact->lire);
            //$newmenu->add("/contact/list.php?userid=$user->id", $langs->trans("MyContacts"), 1, $user->rights->societe->contact->lire);

            // Categories
            if ($conf->categorie->enabled)
            {
                $langs->load("categories");
                // Categories prospects/customers
                $newmenu->add("/categories/index.php?leftmenu=cat&amp;type=2", $langs->trans("CustomersProspectsCategoriesShort"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/categories/fiche.php?action=create&amp;type=2", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
                }
                // Categories suppliers
                if ($conf->fournisseur->enabled)
                {
                    $newmenu->add("/categories/index.php?leftmenu=cat&amp;type=1", $langs->trans("SuppliersCategoriesShort"), 0, $user->rights->categorie->lire);
                    if ($user->societe_id == 0)
                    {
                        $newmenu->add("/categories/fiche.php?action=create&amp;type=1", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
                    }
                }
                //if ($leftmenu=="cat") $newmenu->add("/categories/liste.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
            }

        }

        /*
         * Menu COMMERCIAL
         */
        if ($mainmenu == 'commercial')
        {
            $langs->load("companies");

            // Propal
            if (! empty($conf->propal->enabled))
            {
                $langs->load("propal");
                $newmenu->add("/comm/propal/index.php?leftmenu=propals", $langs->trans("Prop"), 0, $user->rights->propale->lire, '', $mainmenu, 'propals');
                $newmenu->add("/societe/societe.php?leftmenu=propals", $langs->trans("NewPropal"), 1, $user->rights->propale->creer);
                $newmenu->add("/comm/propal.php?leftmenu=propals", $langs->trans("List"), 1, $user->rights->propale->lire);
                if ($leftmenu=="propals") $newmenu->add("/comm/propal.php?leftmenu=propals&viewstatut=0", $langs->trans("PropalsDraft"), 2, $user->rights->propale->lire);
                if ($leftmenu=="propals") $newmenu->add("/comm/propal.php?leftmenu=propals&viewstatut=1", $langs->trans("PropalsOpened"), 2, $user->rights->propale->lire);
                if ($leftmenu=="propals") $newmenu->add("/comm/propal.php?leftmenu=propals&viewstatut=2", $langs->trans("PropalStatusSigned"), 2, $user->rights->propale->lire);
                if ($leftmenu=="propals") $newmenu->add("/comm/propal.php?leftmenu=propals&viewstatut=3", $langs->trans("PropalStatusNotSigned"), 2, $user->rights->propale->lire);
                if ($leftmenu=="propals") $newmenu->add("/comm/propal.php?leftmenu=propals&viewstatut=4", $langs->trans("PropalStatusBilled"), 2, $user->rights->propale->lire);
                //if ($leftmenu=="propals") $newmenu->add("/comm/propal.php?leftmenu=propals&viewstatut=2,3,4", $langs->trans("PropalStatusClosedShort"), 2, $user->rights->propale->lire);
                $newmenu->add("/comm/propal/stats/index.php?leftmenu=propals", $langs->trans("Statistics"), 1, $user->rights->propale->lire);
            }

            // Customers orders
            if (! empty($conf->commande->enabled))
            {
                $langs->load("orders");
                $newmenu->add("/commande/index.php?leftmenu=orders", $langs->trans("CustomersOrders"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
                $newmenu->add("/societe/societe.php?leftmenu=orders", $langs->trans("NewOrder"), 1, $user->rights->commande->creer);
                $newmenu->add("/commande/liste.php?leftmenu=orders", $langs->trans("List"), 1, $user->rights->commande->lire);
                if ($leftmenu=="orders") $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->commande->lire);
                if ($leftmenu=="orders") $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->commande->lire);
                if ($leftmenu=="orders") $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=2", $langs->trans("StatusOrderOnProcessShort"), 2, $user->rights->commande->lire);
                if ($leftmenu=="orders") $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=3", $langs->trans("StatusOrderToBill"), 2, $user->rights->commande->lire);
                if ($leftmenu=="orders") $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=4", $langs->trans("StatusOrderProcessed"), 2, $user->rights->commande->lire);
                if ($leftmenu=="orders") $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=-1", $langs->trans("StatusOrderCanceledShort"), 2, $user->rights->commande->lire);
                $newmenu->add("/commande/stats/index.php?leftmenu=orders", $langs->trans("Statistics"), 1, $user->rights->commande->lire);
            }

            // Suppliers orders
            if (! empty($conf->fournisseur->enabled))
            {
                $langs->load("orders");
                $newmenu->add("/fourn/commande/index.php?leftmenu=orders_suppliers",$langs->trans("SuppliersOrders"), 0, $user->rights->fournisseur->commande->lire, '', $mainmenu, 'orders_suppliers');
                $newmenu->add("/societe/societe.php?leftmenu=orders_suppliers", $langs->trans("NewOrder"), 1, $user->rights->fournisseur->commande->creer);
                $newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);
                $newmenu->add("/commande/stats/index.php?leftmenu=orders_suppliers&amp;mode=supplier", $langs->trans("Statistics"), 1, $user->rights->fournisseur->commande->lire);
            }

            // Contrat
            if (! empty($conf->contrat->enabled))
            {
                $langs->load("contracts");
                $newmenu->add("/contrat/index.php?leftmenu=contracts", $langs->trans("Contracts"), 0, $user->rights->contrat->lire, '', $mainmenu, 'contracts');
                $newmenu->add("/societe/societe.php?leftmenu=contracts", $langs->trans("NewContract"), 1, $user->rights->contrat->creer);
                $newmenu->add("/contrat/liste.php?leftmenu=contracts", $langs->trans("List"), 1, $user->rights->contrat->lire);
                $newmenu->add("/contrat/services.php?leftmenu=contracts", $langs->trans("MenuServices"), 1, $user->rights->contrat->lire);
                if ($leftmenu=="contracts") $newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=0", $langs->trans("MenuInactiveServices"), 2, $user->rights->contrat->lire);
                if ($leftmenu=="contracts") $newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=4", $langs->trans("MenuRunningServices"), 2, $user->rights->contrat->lire);
                if ($leftmenu=="contracts") $newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=4&amp;filter=expired", $langs->trans("MenuExpiredServices"), 2, $user->rights->contrat->lire);
                if ($leftmenu=="contracts") $newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=5", $langs->trans("MenuClosedServices"), 2, $user->rights->contrat->lire);
            }

            // Interventions
            if (! empty($conf->ficheinter->enabled))
            {
                $langs->load("interventions");
                $newmenu->add("/fichinter/list.php?leftmenu=ficheinter", $langs->trans("Interventions"), 0, $user->rights->ficheinter->lire, '', $mainmenu, 'ficheinter');
                $newmenu->add("/fichinter/fiche.php?action=create&leftmenu=ficheinter", $langs->trans("NewIntervention"), 1, $user->rights->ficheinter->creer);
                $newmenu->add("/fichinter/list.php?leftmenu=ficheinter", $langs->trans("List"), 1, $user->rights->ficheinter->lire);
            }

        }


        /*
         * Menu COMPTA-FINANCIAL
         */
        if ($mainmenu == 'accountancy')
        {
            $langs->load("companies");

            // Customers invoices
            if ($conf->facture->enabled)
            {
                $langs->load("bills");
                $newmenu->add("/compta/facture.php?leftmenu=customers_bills",$langs->trans("BillsCustomers"),0,$user->rights->facture->lire, '', $mainmenu, 'customers_bills');
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/compta/clients.php?action=facturer&amp;leftmenu=customers_bills",$langs->trans("NewBill"),1,$user->rights->facture->creer);
                }
                $newmenu->add("/compta/facture/fiche-rec.php?leftmenu=customers_bills",$langs->trans("Repeatables"),1,$user->rights->facture->lire);

                $newmenu->add("/compta/facture/impayees.php?leftmenu=customers_bills",$langs->trans("Unpaid"),1,$user->rights->facture->lire);

                $newmenu->add("/compta/paiement/liste.php?leftmenu=customers_bills_payments",$langs->trans("Payments"),1,$user->rights->facture->lire);

                if ($conf->global->BILL_ADD_PAYMENT_VALIDATION)
                {
                    $newmenu->add("/compta/paiement/avalider.php?leftmenu=customers_bills_payments",$langs->trans("MenuToValid"),2,$user->rights->facture->lire);
                }
                $newmenu->add("/compta/paiement/rapport.php?leftmenu=customers_bills_payments",$langs->trans("Reportings"),2,$user->rights->facture->lire);

                $newmenu->add("/compta/facture/stats/index.php?leftmenu=customers_bills", $langs->trans("Statistics"),1,$user->rights->facture->lire);
            }

            // Suppliers
            if ($conf->societe->enabled && $conf->fournisseur->enabled)
            {
                if ($conf->facture->enabled)
                {
                    $langs->load("bills");
                    $newmenu->add("/fourn/facture/index.php?leftmenu=suppliers_bills", $langs->trans("BillsSuppliers"),0,$user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills');
                    if ($user->societe_id == 0)
                    {
                        $newmenu->add("/fourn/facture/fiche.php?action=create",$langs->trans("NewBill"),1,$user->rights->fournisseur->facture->creer);
                    }
                    $newmenu->add("/fourn/facture/impayees.php", $langs->trans("Unpaid"),1,$user->rights->fournisseur->facture->lire);
                    $newmenu->add("/fourn/facture/paiement.php", $langs->trans("Payments"),1,$user->rights->fournisseur->facture->lire);

                    $newmenu->add("/compta/facture/stats/index.php?leftmenu=suppliers_bills&mode=supplier", $langs->trans("Statistics"),1,$user->rights->fournisseur->facture->lire);
                }
            }

            // Orders
            if ($conf->commande->enabled)
            {
                $langs->load("orders");
                if ($conf->facture->enabled) $newmenu->add("/commande/liste.php?leftmenu=orders&amp;viewstatut=3", $langs->trans("MenuOrdersToBill"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
                //                  if ($leftmenu=="orders") $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->rights->commande->lire);
            }

            // Donations
            if ($conf->don->enabled)
            {
                $langs->load("donations");
                $newmenu->add("/compta/dons/index.php?leftmenu=donations&amp;mainmenu=accountancy",$langs->trans("Donations"), 0, $user->rights->don->lire, '', $mainmenu, 'donations');
                if ($leftmenu=="donations") $newmenu->add("/compta/dons/fiche.php?action=create",$langs->trans("NewDonation"), 1, $user->rights->don->creer);
                if ($leftmenu=="donations") $newmenu->add("/compta/dons/liste.php",$langs->trans("List"), 1, $user->rights->don->lire);
                //if ($leftmenu=="donations") $newmenu->add("/compta/dons/stats.php",$langs->trans("Statistics"), 1, $user->rights->don->lire);
            }

            // Trips and expenses
            if ($conf->deplacement->enabled)
            {
                $langs->load("trips");
                $newmenu->add("/compta/deplacement/index.php?leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("TripsAndExpenses"), 0, $user->rights->deplacement->lire, '', $mainmenu, 'tripsandexpenses');
                if ($leftmenu=="tripsandexpenses") $newmenu->add("/compta/deplacement/fiche.php?action=create&amp;leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("New"), 1, $user->rights->deplacement->creer);
                if ($leftmenu=="tripsandexpenses") $newmenu->add("/compta/deplacement/list.php?leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("List"), 1, $user->rights->deplacement->lire);
                if ($leftmenu=="tripsandexpenses") $newmenu->add("/compta/deplacement/stats/index.php?leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("Statistics"), 1, $user->rights->deplacement->lire);
            }

            // Taxes and social contributions
            if ($conf->tax->enabled)
            {
                $newmenu->add("/compta/charges/index.php?leftmenu=tax&amp;mainmenu=accountancy",$langs->trans("MenuTaxAndDividends"), 0, $user->rights->tax->charges->lire, '', $mainmenu, 'tax');
                if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/sociales/index.php?leftmenu=tax_social",$langs->trans("MenuSocialContributions"),1,$user->rights->tax->charges->lire);
                if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/sociales/charges.php?leftmenu=tax_social&action=create",$langs->trans("MenuNewSocialContribution"), 2, $user->rights->tax->charges->creer);
                if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/charges/index.php?leftmenu=tax_social&amp;mainmenu=accountancy&amp;mode=sconly",$langs->trans("Payments"), 2, $user->rights->tax->charges->lire);
                // VAT
                if (empty($conf->global->TAX_DISABLE_VAT_MENUS))
                {
                    if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/tva/index.php?leftmenu=tax_vat&amp;mainmenu=accountancy",$langs->trans("VAT"),1,$user->rights->tax->charges->lire, '', $mainmenu, 'tax_vat');
                    if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/tva/fiche.php?leftmenu=tax_vat&action=create",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
                    if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/tva/reglement.php?leftmenu=tax_vat",$langs->trans("Payments"),2,$user->rights->tax->charges->lire);
                    if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/tva/clients.php?leftmenu=tax_vat", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
                    if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/tva/quadri_detail.php?leftmenu=tax_vat", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
                    global $mysoc;

                    //Local Taxes
                    if($mysoc->country_code=='ES' && $mysoc->localtax2_assuj=="1")
                    {
                    	if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/localtax/index.php?leftmenu=tax_vat&amp;mainmenu=accountancy",$langs->transcountry("LT2",$mysoc->country_code),1,$user->rights->tax->charges->lire);
                    	if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/localtax/fiche.php?leftmenu=tax_vat&action=create",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
                    	if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/localtax/reglement.php?leftmenu=tax_vat",$langs->trans("Payments"),2,$user->rights->tax->charges->lire);
                    	if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/localtax/clients.php?leftmenu=tax_vat", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
                    	//if (preg_match('/^tax/i',$leftmenu)) $newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_vat", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
                    }

                }

            }

            // Compta simple
            if ($conf->comptabilite->enabled && $conf->global->FACTURE_VENTILATION)
            {
                $newmenu->add("/compta/ventilation/index.php?leftmenu=ventil",$langs->trans("Dispatch"),0,$user->rights->compta->ventilation->lire, '', $mainmenu, 'ventil');
                if ($leftmenu=="ventil") $newmenu->add("/compta/ventilation/liste.php",$langs->trans("ToDispatch"),1,$user->rights->compta->ventilation->lire);
                if ($leftmenu=="ventil") $newmenu->add("/compta/ventilation/lignes.php",$langs->trans("Dispatched"),1,$user->rights->compta->ventilation->lire);
                if ($leftmenu=="ventil") $newmenu->add("/compta/param/",$langs->trans("Setup"),1,$user->rights->compta->ventilation->parametrer);
                if ($leftmenu=="ventil") $newmenu->add("/compta/param/comptes/fiche.php?action=create",$langs->trans("New"),2,$user->rights->compta->ventilation->parametrer);
                if ($leftmenu=="ventil") $newmenu->add("/compta/param/comptes/liste.php",$langs->trans("List"),2,$user->rights->compta->ventilation->parametrer);
                if ($leftmenu=="ventil") $newmenu->add("/compta/export/",$langs->trans("Export"),1,$user->rights->compta->ventilation->lire);
                if ($leftmenu=="ventil") $newmenu->add("/compta/export/index.php?action=export",$langs->trans("New"),2,$user->rights->compta->ventilation->lire);
                if ($leftmenu=="ventil") $newmenu->add("/compta/export/liste.php",$langs->trans("List"),2,$user->rights->compta->ventilation->lire);
            }

            // Compta expert
            if ($conf->accounting->enabled)
            {

            }

            // Rapports
            if ($conf->comptabilite->enabled || $conf->accounting->enabled)
            {
                $langs->load("compta");

                // Bilan, resultats
                $newmenu->add("/compta/resultat/index.php?leftmenu=ca&amp;mainmenu=accountancy",$langs->trans("Reportings"),0,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire, '', $mainmenu, 'ca');

                if ($leftmenu=="ca") $newmenu->add("/compta/resultat/index.php?leftmenu=ca",$langs->trans("ReportInOut"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
                if ($leftmenu=="ca") $newmenu->add("/compta/resultat/clientfourn.php?leftmenu=ca",$langs->trans("ByCompanies"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
                /* On verra ca avec module compabilite expert
                 if ($leftmenu=="ca") $newmenu->add("/compta/resultat/compteres.php?leftmenu=ca","Compte de resultat",2,$user->rights->compta->resultat->lire);
                 if ($leftmenu=="ca") $newmenu->add("/compta/resultat/bilan.php?leftmenu=ca","Bilan",2,$user->rights->compta->resultat->lire);
                 */
                if ($leftmenu=="ca") $newmenu->add("/compta/stats/index.php?leftmenu=ca",$langs->trans("ReportTurnover"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);

                /*
                 if ($leftmenu=="ca") $newmenu->add("/compta/stats/cumul.php?leftmenu=ca","Cumule",2,$user->rights->compta->resultat->lire);
                 if ($conf->propal->enabled) {
                 if ($leftmenu=="ca") $newmenu->add("/compta/stats/prev.php?leftmenu=ca","Previsionnel",2,$user->rights->compta->resultat->lire);
                 if ($leftmenu=="ca") $newmenu->add("/compta/stats/comp.php?leftmenu=ca","Transforme",2,$user->rights->compta->resultat->lire);
                 }
                 */
                if ($leftmenu=="ca") $newmenu->add("/compta/stats/casoc.php?leftmenu=ca",$langs->trans("ByCompanies"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
                if ($leftmenu=="ca") $newmenu->add("/compta/stats/cabyuser.php?leftmenu=ca",$langs->trans("ByUsers"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);

                // Journaux
 				//if ($leftmenu=="ca") $newmenu->add("/compta/journaux/index.php?leftmenu=ca",$langs->trans("Journaux"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
                //journaux
                if ($leftmenu=="ca") $newmenu->add("/compta/journal/sellsjournal.php?leftmenu=ca",$langs->trans("SellsJournal"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
                if ($leftmenu=="ca") $newmenu->add("/compta/journal/purchasesjournal.php?leftmenu=ca",$langs->trans("PurchasesJournal"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
            }
        }


        /*
         * Menu BANK
         */
        if ($mainmenu == 'bank')
        {
            $langs->load("withdrawals");
            $langs->load("banks");
            $langs->load("bills");

            // Bank-Caisse
            if ($conf->banque->enabled)
            {
                $newmenu->add("/compta/bank/index.php?leftmenu=bank&amp;mainmenu=bank",$langs->trans("MenuBankCash"),0,$user->rights->banque->lire, '', $mainmenu, 'bank');

                $newmenu->add("/compta/bank/fiche.php?action=create",$langs->trans("MenuNewFinancialAccount"),1,$user->rights->banque->configurer);
                $newmenu->add("/compta/bank/categ.php",$langs->trans("Rubriques"),1,$user->rights->banque->configurer);

                $newmenu->add("/compta/bank/search.php",$langs->trans("ListTransactions"),1,$user->rights->banque->lire);
                $newmenu->add("/compta/bank/budget.php",$langs->trans("ListTransactionsByCategory"),1,$user->rights->banque->lire);

                $newmenu->add("/compta/bank/virement.php",$langs->trans("BankTransfers"),1,$user->rights->banque->transfer);
            }

            // Prelevements
            if ($conf->prelevement->enabled)
            {
                $newmenu->add("/compta/prelevement/index.php?leftmenu=withdraw&amp;mainmenu=bank",$langs->trans("StandingOrders"),0,$user->rights->prelevement->bons->lire, '', $mainmenu, 'withdraw');

                //if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/demandes.php?status=0&amp;mainmenu=bank",$langs->trans("StandingOrderToProcess"),1,$user->rights->prelevement->bons->lire);

                if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/create.php?mainmenu=bank",$langs->trans("NewStandingOrder"),1,$user->rights->prelevement->bons->creer);


                if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/bons.php?mainmenu=bank",$langs->trans("WithdrawalsReceipts"),1,$user->rights->prelevement->bons->lire);
                if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/liste.php?mainmenu=bank",$langs->trans("WithdrawalsLines"),1,$user->rights->prelevement->bons->lire);
                if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/rejets.php?mainmenu=bank",$langs->trans("Rejects"),1,$user->rights->prelevement->bons->lire);
                if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/stats.php?mainmenu=bank",$langs->trans("Statistics"),1,$user->rights->prelevement->bons->lire);

                //if ($leftmenu=="withdraw") $newmenu->add("/compta/prelevement/config.php",$langs->trans("Setup"),1,$user->rights->prelevement->bons->configurer);
            }

            // Gestion cheques
            if ($conf->facture->enabled && $conf->banque->enabled)
            {
                $newmenu->add("/compta/paiement/cheque/index.php?leftmenu=checks&amp;mainmenu=bank",$langs->trans("MenuChequeDeposits"),0,$user->rights->banque->cheque, '', $mainmenu, 'checks');
                $newmenu->add("/compta/paiement/cheque/fiche.php?leftmenu=checks&amp;action=new&amp;mainmenu=bank",$langs->trans("NewChequeDeposit"),1,$user->rights->banque->cheque);
                $newmenu->add("/compta/paiement/cheque/liste.php?leftmenu=checks&amp;mainmenu=bank",$langs->trans("List"),1,$user->rights->banque->cheque);
            }

       }

        /*
         * Menu PRODUCTS-SERVICES
         */
        if ($mainmenu == 'products')
        {
            // Products
            if ($conf->product->enabled)
            {
                $newmenu->add("/product/index.php?leftmenu=product&amp;type=0", $langs->trans("Products"), 0, $user->rights->produit->lire, '', $mainmenu, 'product');
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/product/fiche.php?leftmenu=product&amp;action=create&amp;type=0", $langs->trans("NewProduct"), 1, $user->rights->produit->creer);
                    $newmenu->add("/product/liste.php?leftmenu=product&amp;type=0", $langs->trans("List"), 1, $user->rights->produit->lire);
                }
                if ($conf->propal->enabled)
                {
                    $newmenu->add("/product/popuprop.php?leftmenu=stats&amp;type=0", $langs->trans("Statistics"), 1, $user->rights->produit->lire && $user->rights->propale->lire);
                }
                if ($conf->stock->enabled)
                {
                    $newmenu->add("/product/reassort.php?type=0", $langs->trans("Stocks"), 1, $user->rights->produit->lire && $user->rights->stock->lire);
                }
            }

            // Services
            if ($conf->service->enabled)
            {
                $newmenu->add("/product/index.php?leftmenu=service&amp;type=1", $langs->trans("Services"), 0, $user->rights->service->lire, '', $mainmenu, 'service');
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/product/fiche.php?leftmenu=service&amp;action=create&amp;type=1", $langs->trans("NewService"), 1, $user->rights->service->creer);
                }
                $newmenu->add("/product/liste.php?leftmenu=service&amp;type=1", $langs->trans("List"), 1, $user->rights->service->lire);
                if ($conf->propal->enabled)
                {
                    $newmenu->add("/product/popuprop.php?leftmenu=stats&amp;type=1", $langs->trans("Statistics"), 1, $user->rights->service->lire && $user->rights->propale->lire);
                }
            }

            // Categories
            if ($conf->categorie->enabled)
            {
                $langs->load("categories");
                $newmenu->add("/categories/index.php?leftmenu=cat&amp;type=0", $langs->trans("Categories"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/categories/fiche.php?action=create&amp;type=0", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
                }
                //if ($leftmenu=="cat") $newmenu->add("/categories/liste.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
            }

            // Stocks
            if ($conf->stock->enabled)
            {
                $langs->load("stocks");
                $newmenu->add("/product/stock/index.php?leftmenu=stock", $langs->trans("Stocks"), 0, $user->rights->stock->lire, '', $mainmenu, 'stock');
                if ($leftmenu=="stock") $newmenu->add("/product/stock/fiche.php?action=create", $langs->trans("MenuNewWarehouse"), 1, $user->rights->stock->creer);
                if ($leftmenu=="stock") $newmenu->add("/product/stock/liste.php", $langs->trans("List"), 1, $user->rights->stock->lire);
                if ($leftmenu=="stock") $newmenu->add("/product/stock/valo.php", $langs->trans("EnhancedValue"), 1, $user->rights->stock->lire);
                if ($leftmenu=="stock") $newmenu->add("/product/stock/mouvement.php", $langs->trans("Movements"), 1, $user->rights->stock->mouvement->lire);
            }

            // Expeditions
            if ($conf->expedition->enabled)
            {
                $langs->load("sendings");
                $newmenu->add("/expedition/index.php?leftmenu=sendings", $langs->trans("Shipments"), 0, $user->rights->expedition->lire, '', $mainmenu, 'sendings');
                if ($leftmenu=="sendings") $newmenu->add("/expedition/fiche.php?action=create2&leftmenu=sendings", $langs->trans("NewSending"), 1, $user->rights->expedition->creer);
                if ($leftmenu=="sendings") $newmenu->add("/expedition/liste.php?leftmenu=sendings", $langs->trans("List"), 1, $user->rights->expedition->lire);
                if ($leftmenu=="sendings") $newmenu->add("/expedition/stats/index.php?leftmenu=sendings", $langs->trans("Statistics"), 1, $user->rights->expedition->lire);
            }

        }


        /*
         * Menu SUPPLIERS
         */
        if ($mainmenu == 'suppliers')
        {
            $langs->load("suppliers");

            if ($conf->societe->enabled && $conf->fournisseur->enabled)
            {
                $newmenu->add("/fourn/index.php?leftmenu=suppliers", $langs->trans("Suppliers"), 0, $user->rights->societe->lire && $user->rights->fournisseur->lire, '', $mainmenu, 'suppliers');

                // Security check
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/societe/soc.php?leftmenu=suppliers&amp;action=create&amp;type=f",$langs->trans("NewSupplier"), 1, $user->rights->societe->creer && $user->rights->fournisseur->lire);
                }
                $newmenu->add("/fourn/liste.php",$langs->trans("List"), 1, $user->rights->societe->lire && $user->rights->fournisseur->lire);
                $newmenu->add("/contact/list.php?leftmenu=suppliers&amp;type=f",$langs->trans("Contacts"), 1, $user->rights->societe->contact->lire && $user->rights->fournisseur->lire);
                $newmenu->add("/fourn/stats.php",$langs->trans("Statistics"), 1, $user->rights->societe->lire && $user->rights->fournisseur->lire);
            }

            if ($conf->facture->enabled)
            {
                $langs->load("bills");
                $newmenu->add("/fourn/facture/index.php?leftmenu=orders", $langs->trans("Bills"), 0, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'orders');

                if ($user->societe_id == 0)
                {
                    $newmenu->add("/fourn/facture/fiche.php?action=create",$langs->trans("NewBill"), 1, $user->rights->fournisseur->facture->creer);
                }

                $newmenu->add("/fourn/facture/paiement.php", $langs->trans("Payments"), 1, $user->rights->fournisseur->facture->lire);
            }

            if ($conf->fournisseur->enabled)
            {
                $langs->load("orders");
                $newmenu->add("/fourn/commande/index.php?leftmenu=suppliers",$langs->trans("Orders"), 0, $user->rights->fournisseur->commande->lire, '', $mainmenu, 'suppliers');
                $newmenu->add("/societe/societe.php?leftmenu=supplier", $langs->trans("NewOrder"), 1, $user->rights->fournisseur->commande->creer);
                $newmenu->add("/fourn/commande/liste.php?leftmenu=suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);
            }

            if ($conf->categorie->enabled)
            {
                $langs->load("categories");
                $newmenu->add("/categories/index.php?leftmenu=cat&amp;type=1", $langs->trans("Categories"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
                if ($user->societe_id == 0)
                {
                    $newmenu->add("/categories/fiche.php?action=create&amp;type=1", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
                }
                //if ($leftmenu=="cat") $newmenu->add("/categories/liste.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
            }

        }

        /*
         * Menu PROJECTS
         */
        if ($mainmenu == 'project')
        {
            if ($conf->projet->enabled)
            {
                $langs->load("projects");

                // Project affected to user
                $newmenu->add("/projet/index.php?leftmenu=projects&mode=mine", $langs->trans("MyProjects"), 0, $user->rights->projet->lire, '', $mainmenu, 'projects');
                $newmenu->add("/projet/fiche.php?leftmenu=projects&action=create&mode=mine", $langs->trans("NewProject"), 1, $user->rights->projet->creer);
                $newmenu->add("/projet/liste.php?leftmenu=projects&mode=mine", $langs->trans("List"), 1, $user->rights->projet->lire);

                // All project i have permission on
                $newmenu->add("/projet/index.php?leftmenu=projects", $langs->trans("Projects"), 0, $user->rights->projet->lire && $user->rights->projet->lire);
                $newmenu->add("/projet/fiche.php?leftmenu=projects&action=create", $langs->trans("NewProject"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
                $newmenu->add("/projet/liste.php?leftmenu=projects", $langs->trans("List"), 1, $user->rights->projet->lire && $user->rights->projet->lire);

                // Project affected to user
                $newmenu->add("/projet/activity/index.php?mode=mine", $langs->trans("MyActivities"), 0, $user->rights->projet->lire);
                $newmenu->add("/projet/tasks.php?action=create&mode=mine", $langs->trans("NewTask"), 1, $user->rights->projet->creer);
                $newmenu->add("/projet/tasks/index.php?mode=mine", $langs->trans("List"), 1, $user->rights->projet->lire);
                $newmenu->add("/projet/activity/list.php?mode=mine", $langs->trans("NewTimeSpent"), 1, $user->rights->projet->creer);

                // All project i have permission on
                $newmenu->add("/projet/activity/index.php", $langs->trans("Activities"), 0, $user->rights->projet->lire && $user->rights->projet->lire);
                $newmenu->add("/projet/tasks.php?action=create", $langs->trans("NewTask"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
                $newmenu->add("/projet/tasks/index.php", $langs->trans("List"), 1, $user->rights->projet->lire && $user->rights->projet->lire);
                $newmenu->add("/projet/activity/list.php", $langs->trans("NewTimeSpent"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
            }
        }


        /*
         * Menu TOOLS
         */
        if ($mainmenu == 'tools')
        {

            if (! empty($conf->mailing->enabled))
            {
                $langs->load("mails");

                $newmenu->add("/comm/mailing/index.php?leftmenu=mailing", $langs->trans("EMailings"), 0, $user->rights->mailing->lire, '', $mainmenu, 'mailing');
                $newmenu->add("/comm/mailing/fiche.php?leftmenu=mailing&amp;action=create", $langs->trans("NewMailing"), 1, $user->rights->mailing->creer);
                $newmenu->add("/comm/mailing/liste.php?leftmenu=mailing", $langs->trans("List"), 1, $user->rights->mailing->lire);
            }

            if (! empty($conf->export->enabled))
            {
                $langs->load("exports");
                $newmenu->add("/exports/index.php?leftmenu=export",$langs->trans("FormatedExport"),0, $user->rights->export->lire, '', $mainmenu, 'export');
                $newmenu->add("/exports/export.php?leftmenu=export",$langs->trans("NewExport"),1, $user->rights->export->creer);
                //$newmenu->add("/exports/export.php?leftmenu=export",$langs->trans("List"),1, $user->rights->export->lire);
            }

            if (! empty($conf->import->enabled))
            {
                $langs->load("exports");
                $newmenu->add("/imports/index.php?leftmenu=import",$langs->trans("FormatedImport"),0, $user->rights->import->run, '', $mainmenu, 'import');
                $newmenu->add("/imports/import.php?leftmenu=import",$langs->trans("NewImport"),1, $user->rights->import->run);
            }
        }

        /*
         * Menu MEMBERS
         */
        if ($mainmenu == 'members')
        {
            if ($conf->adherent->enabled)
            {
                $langs->load("members");
                $langs->load("compta");

                $newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members",$langs->trans("Members"),0,$user->rights->adherent->lire, '', $mainmenu, 'members');
                $newmenu->add("/adherents/fiche.php?leftmenu=members&amp;action=create",$langs->trans("NewMember"),1,$user->rights->adherent->creer);
                $newmenu->add("/adherents/liste.php?leftmenu=members",$langs->trans("List"),1,$user->rights->adherent->lire);
                $newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=-1",$langs->trans("MenuMembersToValidate"),2,$user->rights->adherent->lire);
                $newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=1",$langs->trans("MenuMembersValidated"),2,$user->rights->adherent->lire);
                $newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=1&amp;filter=uptodate",$langs->trans("MenuMembersUpToDate"),2,$user->rights->adherent->lire);
                $newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=1&amp;filter=outofdate",$langs->trans("MenuMembersNotUpToDate"),2,$user->rights->adherent->lire);
                $newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=0",$langs->trans("MenuMembersResiliated"),2,$user->rights->adherent->lire);
                $newmenu->add("/adherents/stats/geo.php?leftmenu=members&mode=memberbycountry",$langs->trans("MenuMembersStats"),1,$user->rights->adherent->lire);

                $newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members",$langs->trans("Subscriptions"),0,$user->rights->adherent->cotisation->lire);
                $newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=-1,1&amp;mainmenu=members",$langs->trans("NewSubscription"),1,$user->rights->adherent->cotisation->creer);
                $newmenu->add("/adherents/cotisations.php?leftmenu=members",$langs->trans("List"),1,$user->rights->adherent->cotisation->lire);
                $newmenu->add("/adherents/stats/index.php?leftmenu=members",$langs->trans("MenuMembersStats"),1,$user->rights->adherent->lire);


                if ($conf->categorie->enabled)
                {
                    $langs->load("categories");
                    $newmenu->add("/categories/index.php?leftmenu=cat&amp;type=3", $langs->trans("Categories"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
                    if ($user->societe_id == 0)
                    {
                        $newmenu->add("/categories/fiche.php?action=create&amp;type=3", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
                    }
                    //if ($leftmenu=="cat") $newmenu->add("/categories/liste.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
                }

                $newmenu->add("/adherents/index.php?leftmenu=export&amp;mainmenu=members",$langs->trans("Exports"),0,$user->rights->adherent->export, '', $mainmenu, 'export');
                if ($conf->export->enabled && $leftmenu=="export") $newmenu->add("/exports/index.php?leftmenu=export",$langs->trans("Datas"),1,$user->rights->adherent->export);
                if ($leftmenu=="export") $newmenu->add("/adherents/htpasswd.php?leftmenu=export",$langs->trans("Filehtpasswd"),1,$user->rights->adherent->export);
                if ($leftmenu=="export") $newmenu->add("/adherents/cartes/carte.php?leftmenu=export",$langs->trans("MembersCards"),1,$user->rights->adherent->export);

                // Type
                $newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members",$langs->trans("MembersTypes"),0,$user->rights->adherent->configurer, '', $mainmenu, 'setup');
                $newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members&amp;action=create",$langs->trans("New"),1,$user->rights->adherent->configurer);
                $newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members",$langs->trans("List"),1,$user->rights->adherent->configurer);
            }

        }

        // Add personalized menus and modules menus
        require_once(DOL_DOCUMENT_ROOT."/core/class/menubase.class.php");

        $tabMenu=array();
        $menuArbo = new Menubase($db,'eldy','left');
        $newmenu = $menuArbo->menuLeftCharger($newmenu,$mainmenu,$leftmenu,($user->societe_id?1:0),'eldy',$tabMenu);
    }


    //var_dump($menu_array_before);exit;
    //var_dump($menu_array_after);exit;
    $menu_array=$newmenu->liste;
    if (is_array($menu_array_before)) $menu_array=array_merge($menu_array_before, $menu_array);
    if (is_array($menu_array_after))  $menu_array=array_merge($menu_array, $menu_array_after);
    //var_dump($menu_array);exit;

    // Show menu
    $alt=0;
    if (is_array($menu_array))
    {
        $num=count($menu_array);
    	for ($i = 0; $i < $num; $i++)
        {
            $alt++;
            if (empty($menu_array[$i]['level']))
            {
                if (($alt%2==0))
                {
                    print '<div class="blockvmenuimpair">'."\n";
                }
                else
                {
                    print '<div class="blockvmenupair">'."\n";
                }
            }

            // Place tabulation
            $tabstring='';
            $tabul=($menu_array[$i]['level'] - 1);
            if ($tabul > 0)
            {
                for ($j=0; $j < $tabul; $j++)
                {
                    $tabstring.='&nbsp; &nbsp;';
                }
            }

            // For external modules
            $url = dol_buildpath($menu_array[$i]['url'], 1);

            print '<!-- Add menu entry with mainmenu='.$menu_array[$i]['mainmenu'].', leftmenu='.$menu_array[$i]['leftmenu'].', level='.$menu_array[$i]['level'].' -->'."\n";

            // Menu niveau 0
            if ($menu_array[$i]['level'] == 0)
            {
                if ($menu_array[$i]['enabled'])
                {
                    print '<div class="menu_titre">'.$tabstring.'<a class="vmenu" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>'.$menu_array[$i]['titre'].'</a></div>'."\n";
                }
                else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
                {
                    print '<div class="menu_titre">'.$tabstring.'<font class="vmenudisabled">'.$menu_array[$i]['titre'].'</font></div>'."\n";
                }
                print '<div class="menu_top"></div>'."\n";
            }
            // Menu niveau > 0
            if ($menu_array[$i]['level'] > 0)
            {
                if ($menu_array[$i]['enabled'])
                {
                	print '<div class="menu_contenu">'.$tabstring;
                    if ($menu_array[$i]['url']) print '<a class="vsmenu" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>';
                    print $menu_array[$i]['titre'];
                    if ($menu_array[$i]['url']) print '</a>';
                    // If title is not pure text and contains a table, no carriage return added
                    if (! strstr($menu_array[$i]['titre'],'<table')) print '<br>';
                    print '</div>'."\n";
                }
                else if (empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))
                {
                	print '<div class="menu_contenu">'.$tabstring.'<font class="vsmenudisabled">'.$menu_array[$i]['titre'].'</font><br></div>'."\n";
                }
            }

            // If next is a new block or end
            if (empty($menu_array[$i+1]['level']))
            {
                print '<div class="menu_end"></div>'."\n";
                print "</div>\n";
            }
        }
    }

    return count($menu_array);
}


?>
