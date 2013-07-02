<?php
/* Copyright (C) 2011 Juanjo Menent           <jmenent@2byte.es>
 * Copyright (C) 2011 Jorge Donet
 * Copyright (C) 2012 Ferran Marcet           <fmarcet@2byte.es> 
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU  *General Public License as published by
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
 *	\file       htdocs/pos/ajax_pos.php
 *	\ingroup    ticket
 *	\brief      Tickets home page
 *	\version    $Id: ajax_pos.php,v 1.2 2011-06-30 11:00:41 jdonet Exp $
*/
$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory

require_once(DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT ."/core/class/notify.class.php");
dol_include_once('/pos/backend/class/pos.class.php');

//if (!$user->rights->pos->lire) accessforbidden();
$data = file_get_contents('php://input');
$data = json_decode($data, true);
$langs->load("pos@pos");
$html = '';
$action = GETPOST('action');
$category = GETPOST('category');
//$parentcategory = GETPOST('parentcategory');
$product_id = GETPOST('product');
if($action=='getProducts')
{
		$products = POS::getProductsbyCategory($category,0);
		echo json_encode($products);
}
else if($action=='getMoreProducts')
{
	$pag = intval(GETPOST('pag','int'));
	$categories = POS::getProductsbyCategory($category,$pag);
	echo json_encode($categories);
}
else if($action=='getCategories')
{
	//$parentcategory = intval($data['data']);
	$parentcategory = intval(GETPOST('parentcategory','int'));
	$categories = POS::getCategories($parentcategory);
	echo json_encode($categories);	
}
elseif($action=='newTicket')
{
		//$html.=	POS::CreateTicket();
		//$jorge = $html;
}
elseif($action=='getProduct')
{
	if(isset($data['data']))
	{
		$product_id = intval($data['data']['product']);
		$customer_id = intval($data['data']['customer']);
		$product = POS::getProductbyId($product_id, $customer_id);
		echo json_encode($product);
	}
}
elseif($action=='getTicket')
{
	if(sizeof($data))
	{
		$ticketId = $data['data'];
		$ticket = POS::getTicket($ticketId);
		echo json_encode($ticket);
	}
}
elseif($action=='getFacture')
{
	if(sizeof($data))
	{
		$ticketId = $data['data'];
		$ticket = POS::getFacture($ticketId);
		echo json_encode($ticket);
	}
}
elseif($action=='getHistory')
{
	$searchValue = '';
	if(sizeof($data))
	{
		$searchValue = $data['data']['search'];
		$stat = $data['data']['stat'];
	}
	$history = POS::getHistoric($searchValue,$stat);
	echo json_encode($history);
}
elseif($action=='getHistoryFac')
{
	$searchValue = '';
	if(sizeof($data))
	{
		$searchValue = $data['data']['search'];
		$stat = $data['data']['stat'];
	}
	$history = POS::getHistoricFac($searchValue,$stat);
	echo json_encode($history);
}
elseif($action=='countHistory')
{
	$history = POS::countHistoric();
	echo json_encode($history);
}
elseif($action=='countHistoryFac')
{
	$history = POS::countHistoricFac();
	echo json_encode($history);
}
elseif($action=='getParking')
{
	$history = POS::getHistoric();
	echo json_encode($history);
}
elseif($action=='saveTicket')
{
	$result = POS::SetTicket($data);
	echo json_encode($result);
}
elseif($action=='searchProducts')
{
	if(sizeof($data))
	{
		$searchValue = $data['data']['search'];
		$warehouse = $data['data']['warehouse'];
		$result = POS::SearchProduct($searchValue, false, $warehouse,1);
		echo json_encode($result);
		
	}
}
elseif($action=='countProduct')
{
	$warehouseId = $data['data'];
	$stock = POS::countProduct($warehouseId);
	echo json_encode($stock);
}
elseif($action=='searchStocks')
{
	if(sizeof($data))
	{
		$searchValue = $data['data']['search'];
		$mode = $data['data']['mode'];
		$warehouse = $data['data']['warehouse'];
		
		$result = POS::SearchProduct($searchValue,true,$warehouse,$mode);
		echo json_encode($result);
		
	}
}
elseif($action=='searchCustomer')
{
	if(sizeof($data))
	{
		$searchValue = $data['data'];
		$result = POS::SearchCustomer($searchValue,false);
		echo json_encode($result);
		
	}
}
elseif($action=='addCustomer')
{
	if(sizeof($data))
	{
		$customer = $data['data'];
		$result = POS::SetCustomer($customer,false);
		echo json_encode($result);
		
	}
}
elseif($action=='addNewProduct')
{
	if(sizeof($data))
	{
		$product = $data['data'];
		$result = POS::SetProduct($product,false);
		echo json_encode($result);
		
	}
}
elseif($action=='getMoneyCash')
{
	$result = POS::getMoneyCash();
	echo json_encode($result);
}
elseif($action=='getConfig')
{
	$result = POS::getConfig();
	echo json_encode($result);
}
elseif($action=='closeCash')
{
	if(sizeof($data))
	{
		$cash = $data['data'];
		$result = POS::setControlCash($cash);
		echo json_encode($result);
		
	}
}
elseif($action=='getPlaces')
{
	$places = POS::getPlaces();
	echo json_encode($places);
	
}
elseif($action=='SendMail')
{
	$email = $data['data'];
	$result = POS::sendMail($email);
	echo json_encode($result);

}
elseif($action=='deleteTicket')
{
	$idticket = $data['data'];
	$result = POS::Delete_Ticket($idticket);
	echo json_encode($result);

}
elseif($action=='Translate')
{
	if(sizeof($data))
	{
		echo json_encode($langs->trans($data['data']));
	}
}
elseif($action=='calculePrice')
{
	if(sizeof($data))
	{
		$product = $data['data'];
		$result = POS::calculePrice($product);
		echo json_encode($result);
	}
}
elseif($action=='getLocalTax')
{
	if(sizeof($data))
	{
		$data = $data['data'];
		$result = POS::getLocalTax($data);
		echo json_encode($result);
	}
}
elseif($action=='getNotes')
{
	$mode = $data['data'];
	$result = POS::getNotes($mode);
	echo json_encode($result);
}
elseif($action=='getWarehouse')
{
	$result = POS::getWarehouse();
	echo json_encode($result);
}

echo $html;


?>