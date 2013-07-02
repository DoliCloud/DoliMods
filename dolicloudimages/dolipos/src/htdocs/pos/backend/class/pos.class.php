<?php
/* Copyright (C) 2011 Juanjo Menent           <jmenent@2byte.es>
 * Copyright (C) 2012-2013 Ferran Marcet      <fmarcet@2byte.es>
 * Copyright (C) 2013 Iván Casco              <admin@gestionintegraltn.com>
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

require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
dol_include_once('/pos/backend/class/ticket.class.php');
dol_include_once('/pos/backend/class/payment.class.php');
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");
dol_include_once('/pos/backend/class/cash.class.php');
dol_include_once('/pos/backend/lib/errors.lib.php');
dol_include_once('/pos/backend/class/place.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once (DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
dol_include_once('/pos/backend/class/facturesim.class.php');
dol_include_once('/rewards/class/rewards.class.php');
require_once (DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php');

/**
 *	\class      POS
 *	\brief      Class for POS gestion
 */
class POS extends CommonObject
{
    /*function __construct($idTicket)
    {
       $this->id=$idTicket;
    } */

	/**
 	*  Return Categories list
 	*  @param		int		$idCat		Id of Category, if 0 return Principal cats
 	*  @return      array				Array with categories
 	*/
	public static function getCategories($idCat=0)
	{
		global $db;
		switch($idCat)
		{
			case 0: //devolver las categorias con nivel 1
				$objCat = new Categorie($db);
				//$cats=$objCat->get_full_arbo(0);
				$cats = $objCat->get_full_arbo($idCat);
								
				if (sizeof ($cats) > 0)
				{
					$retarray=array();
					foreach($cats as $key => $val)
					{
						if ($val['level'] < 2)
						{
							$val['image']=self::getImageCategory($val['id']);
							$val['thumb']=self::getImageCategory($val['id']);
							$retarray[]=$val;
						}	
					}
					return $retarray;
				}
				break;
	
			case ($idCat>0):
				$objCat = new Categorie($db);
			
				$result=$objCat->fetch($idCat);
				if($result > 0)
				{
					$cats = $objCat->get_filles($idCat);
					//$cats = self::get_filles($idCat);
					if (sizeof ($cats) > 0)
					{
						$retarray=array();
						foreach($cats as $val)
						{
							$cat['id']=$val->id;
							$cat['label']=$val->label;
							$cat['fulllabel']=$val->label;
							$cat['fullpath']='_'.$val->id;
							$cat['image']=self::getImageCategory($val->id);
							$cat['thumb']=self::getImageCategory($val->id);
							$retarray[]=$cat;
						}
						return $retarray;
					}
				}
				
				break;
				
			default:
				return -1;
				break;
		}
	}
	
	/**
 	*  Return path of a catergory image 
 	*  @param 		int 	$idCat		Id of Category
 	*  @param		bool	$thumb		If enabled use thumb
 	*  @return      string				Image path
 	*/
	public static function getImageCategory($idCat)
	{	
		global $conf, $db;
		
		$extName="_small";
		$extImgTarget=".jpg";
		$outDir="pos";
		$maxWidth =90;
		$maxHeight=90;
		$quality=50;
			
		if($idCat>0)
		{
			$objCat = new Categorie($db);
			$objCat->fetch($idCat);
			
			$pdir = get_exdir($idCat,2) . $idCat ."/photos/";
			$dir = $conf->categorie->multidir_output[$objCat->entity].'/'.$pdir;
			foreach ($objCat->liste_photos($dir,1) as $key => $obj)
			{
				$filename = $dir.$obj['photo'];
				$filethumbs= $dir.$outDir.'/'.$obj['photo'];
				
				$fileName = preg_replace('/(\.gif|\.jpeg|\.jpg|\.png|\.bmp)$/i','',$filethumbs);
				$fileName = basename($fileName);
				$imgThumbName = $dir.$outDir.'/'.$fileName.$extName.$extImgTarget;
				
				$file_osencoded=$imgThumbName;
				if(!file_exists($file_osencoded))
				{
					$file_osencoded=dol_osencode($filename);
					if (file_exists($file_osencoded))
					{
						require_once(DOL_DOCUMENT_ROOT ."/core/lib/images.lib.php");
						vignette($filename,$maxWidth,$maxHeight,$extName,$quality,$outDir,2);			
					}
				}

				$filename=$outDir.'/'.$fileName.$extName.$extImgTarget;
				$realpath = DOL_URL_ROOT.'/viewimage.php?modulepart=category&entity='.$objCat->entity.'&file='.urlencode($pdir.$filename) ;
				
			}
			if(!$realpath)
			{
				$realpath = DOL_URL_ROOT.'/viewimage.php?modulepart=product&file='.urlencode('noimage.jpg');
			}
			return $realpath;
		}

	}

	/**
 	*  Return products by a category
 	*  @param 		int		$idCat		Id of Category
 	*  @return      array				List of products
 	*/
	public static function getProductsbyCategory($idCat,$more)
	{
		global $db,$conf;
		
		if($idCat) //Productos de la categoría
		{
			$object = new Categorie($db);
			$result=$object->fetch($idCat);
			if ($result > 0)
			{
				if ($object->type == 0)
				{
					//$prods = $object->get_type("product","Product");
					$prods = self::get_prod($idCat,$more);
					if (sizeof ($prods) > 0)
					{
						$ret=array();
						
						foreach ( $prods as $prod )
						{
							$showing2 = 0;
							if($conf->global->POS_STOCK)
							{
								$showing2=1;
							}
							else if($prod["type"] == 1)
							{
								$showing2=1;
							}
							else
							{
								$cashid = $_SESSION['TERMINAL_ID'];
								$cash = new Cash($db);
								$cash->fetch($cashid);
								$warehouse = $cash->fk_warehouse;
								
								$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'product_stock';
								$sql .= ' WHERE fk_product = '.$prod["id"].' AND fk_entrepot='.$warehouse.' AND reel > 0';
								$resql = $db->query($sql);
								
								if($resql)
								{
									$num = $db->num_rows($resql);
									if($num > 0)
									{
										$showing2=1;
									}
								}
							} 
							$showing = ($conf->global->POS_SERVICES?1:($prod["type"]==0?1:0));
							if ( $showing && $showing2)
							{
								$ret[$prod["id"]]["id"] = $prod["id"];
								$ret[$prod["id"]]["ref"] = $prod["ref"];
								$ret[$prod["id"]]["label"] = $prod["label"];
								$ret[$prod["id"]]["description"] = $prod["description"];
																
								$ret[$prod["id"]]["image"] = self::getImageProduct($prod["id"], false);
								$ret[$prod["id"]]["thumb"] = self::getImageProduct($prod["id"], true);
							}
						}
						return $ret;
					}	
				}	
			}
		}
		else //Productos sin categorías
		{
			
			$sql ="SELECT rowid as id, ref, label, description,";
			$sql .=" fk_product_type";
			$sql .=" FROM ".MAIN_DB_PREFIX."product ";
			$sql .=" WHERE rowid NOT IN ";
			$sql .=" (SELECT fk_product FROM ".MAIN_DB_PREFIX."categorie_product)";
			$sql .=" AND tosell=1";
			$sql.= " AND entity IN (".getEntity("product", 1).")";
			if($more >= 0)
				$sql.=" LIMIT ".$more.",10 ";
			
			
			$res = $db->query($sql);
			
			if ($res)
			{
				$num = $db->num_rows($res);
				$i = 0;
				
				while ($i < $num)
				{
					$objp = $db->fetch_object($res);
					
					$showing2 = 0;
					if($conf->global->POS_STOCK)
					{
						$showing2=1;
					}
					else if($objp->fk_product_type==1)
					{
						$showing2=1;
					}
					else
					{
						$cashid = $_SESSION['TERMINAL_ID'];
						$cash = new Cash($db);
						$cash->fetch($cashid);
						$warehouse = $cash->fk_warehouse;
					
						$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'product_stock';
						$sql .= ' WHERE fk_product = '.$objp->id.' AND fk_entrepot='.$warehouse.' AND reel > 0';
						$resql = $db->query($sql);
					
						if($resql)
						{
							$numrows = $db->num_rows($resql);
							if($numrows > 0)
							{
								$showing2=1;
							}
						}
					}
					
					
					$showing = ($conf->global->POS_SERVICES?1:($objp->fk_product_type==0?1:0));
					if( $showing && $showing2)
					{
						$ret[$objp->id]["id"] = $objp->id;
						$ret[$objp->id]["ref"] = $objp->ref;
						$ret[$objp->id]["label"] = $objp->label;
						$ret[$objp->id]["description"] = $objp->description;
												
						$ret[$objp->id]["image"] = self::getImageProduct($objp->id, false);
						$ret[$objp->id]["thumb"] = self::getImageProduct($objp->id, true);
					}
					$i++;
								
				}
				return $ret;
			}
			else 
			{
				return -1;
			}
		}	
		return -1;
	}
	
	/**
 	*  Return a catergory 
 	*  @param 		int		$idCat		Id of Category
 	*  @return      array				Category info
 	*/
	public static function getCategorybyId($idCat)
	{
		global $db;
		$objCat = new Categorie($db);
		$result=$objCat->fetch($idCat);
		if($result > 0)	
		{
			return $objCat;
		}
		return -1;
	}
	
	/**
 	*  Return product info
 	*  @param 		int		$idProd		Id of Product
 	*  @return      array				Product info
 	*/
	public static function getProductbyId($idProd, $idCust)
	{
		global $db, $conf;
		if($conf->global->PRODUIT_MULTIPRICES){
			$sql = "SELECT price_level";
			$sql .= " FROM ".MAIN_DB_PREFIX."societe";
			$sql .= " WHERE rowid = ".$idCust;
			$res=$db->query ($sql);
			if ($res){
				$obj = $db->fetch_object($res);
				if($obj->price_level == NULL){
					$pricelevel= 1;
				}
				else{
					$pricelevel= $obj->price_level;
				}
			}
		}
		else{
			$pricelevel= 1;
		}
		
		$function="getProductbyId";
		
		/*$i=0;
		$sql = "SELECT p.rowid as id, p.ref, p.label, p.description, r.tva_tx";
		$sql.= ", r.price, r.price_ttc, r.localtax1_tx, r.localtax2_tx, r.price_min, r.price_min_ttc, r.price_base_type, fk_product_type";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p, ".MAIN_DB_PREFIX."product_price r ";
		$sql.= " WHERE p.tosell = 1";
		$sql.= " AND p.entity=".$conf->entity;
		if(!$conf->global->POS_SERVICES) 
		{
			$sql.= " AND p.fk_product_type = 0";
		}

		$sql.= " AND p.rowid=".$idProd;
		$sql.= " AND r.fk_product=".$idProd;
		$sql.= " AND r.price_level=".$pricelevel;
		$sql.= " ORDER BY r.date_price DESC";
		
		$resql=$db->query ($sql);*/
		$objp = new Product($db);
		$objp->fetch($idProd);
		
		$ret[0]["id"] = $objp->id;
		$ret[0]["ref"] = $objp->ref;
		$ret[0]["label"] = $objp->label;
		$ret[0]["description"] = $objp->description;
		$ret[0]["fk_product_type"] = $objp->type;
		$ret[0]["diff_price"] = 0;
		if(!empty( $objp->multiprices[$pricelevel]) && $objp->multiprices[$pricelevel] > 0 ){
			$ret[0]["tva_tx"] = $objp->multiprices_tva_tx[$pricelevel];
			$ret[0]["price_base_type"] = $objp->multiprices_base_type[$pricelevel];
			$ret[0]["price"] = $objp->multiprices[$pricelevel];
			$ret[0]["price_ttc"] = $objp->multiprices_ttc[$pricelevel];
			$ret[0]["price_min"] = $objp->multiprices_min[$pricelevel];
			$ret[0]["price_min_ttc"] = $objp->multiprices_min_ttc[$pricelevel];
		}
		else{
			$ret[0]["tva_tx"] = $objp->tva_tx;
			$ret[0]["price_base_type"] = $objp->price_base_type;
			$ret[0]["price"] = $objp->price;
			$ret[0]["price_ttc"] = $objp->price_ttc;
			$ret[0]["price_min"] = $objp->price_min;
			$ret[0]["price_min_ttc"] = $objp->price_min_ttc;
			if($conf->global->PRODUIT_MULTIPRICES){
				$ret[0]["diff_price"] = 1;
			}
		}
		$ret[0]["localtax1_tx"] = $objp->localtax1_tx;
		$ret[0]["localtax2_tx"] = $objp->localtax2_tx;
					
		$ret[0]["image"] = self::getImageProduct($objp->id, false);
		$ret[0]["thumb"] = self::getImageProduct($objp->id, true);
		
		/*if ($resql)
		{
			unset($ret);
			$num = $db->num_rows($resql);
			$i = 0;
				
			
			$objp = $db->fetch_object($resql);
			
			$ret[$i]["id"] = $objp->id;
			$ret[$i]["ref"] = $objp->ref;
			$ret[$i]["label"] = $objp->label;
			$ret[$i]["description"] = $objp->description;
			$ret[$i]["tva_tx"] = $objp->tva_tx;
			$ret[$i]["price_base_type"] = $objp->price_base_type;
			$ret[$i]["fk_product_type"] = $objp->fk_product_type;
			$ret[$i]["price"] = $objp->price;
			$ret[$i]["price_ttc"] = $objp->price_ttc;
			$ret[$i]["price_min"] = $objp->price_min;
			$ret[$i]["price_min_ttc"] = $objp->price_min_ttc;
			$ret[$i]["localtax1_tx"] = $objp->localtax1_tx;
			$ret[$i]["localtax2_tx"] = $objp->localtax2_tx;
			
			$ret[$i]["image"] = self::getImageProduct($objp->id, false);
			$ret[$i]["thumb"] = self::getImageProduct($objp->id, true);
			$i++;						
			
		}*/
		return Errorcontrol($ret,$function);
	}
	
	/**
 	*  Return product info
 	*  
 	*  @param 		string	$idSearch		Part of code, label or barcode
 	*  @param		boolean	$stock			Return stocks of products into info
 	*  @param		int $warehouse			Warehouse id
 	*  @param		int mode				Mode of search
 	*  @return      array					Product info
 	*/
	public static function SearchProduct($idSearch,$stock=false, $warehouse,$mode=0)
	{
		global $db, $conf;
		
		$i=0;
		
		$ret=-1;
		$function="getProductbyId";
		
		
		if($mode>=0){
			if ($stock)
			{
				$sql ="SELECT distinct p.rowid, p.ref, p.label ,";
				$sql .="(select w.reel from ".MAIN_DB_PREFIX."product_stock w left join ".MAIN_DB_PREFIX."entrepot e on w.fk_entrepot = e.rowid";
				$sql .=" where w.fk_product = p.rowid and e.rowid=ep.rowid) as stock"; 
				$sql .=" , ep.label as warehouse, ep.rowid as warehouseId";
				$sql .=" FROM ".MAIN_DB_PREFIX."product p, ".MAIN_DB_PREFIX."entrepot ep ";
				
			}
			else 
			{
				$sql = "SELECT p.rowid, p.ref, p.label, ep.rowid as warehouseId";
				$sql.= " FROM ".MAIN_DB_PREFIX."product as p, ".MAIN_DB_PREFIX."product_stock as w, ".MAIN_DB_PREFIX."entrepot as ep ";
			}
			
			$sql.= " WHERE p.tosell = 1 AND ep.statut = 1";			
			$sql.= " AND p.entity IN (".getEntity("product", 1).")";
			if($warehouse>0) $sql.=" AND ep.rowid = ".$warehouse;			
			if(!$stock)
			{
				$sql.= " AND w.fk_product = p.rowid AND ep.rowid=w.fk_entrepot ";
				if(!$conf->global->POS_STOCK){
					$sql.= " AND w.reel > 0";
				}
			}
			
			if(!$conf->global->POS_SERVICES)
			{
				$sql.= " AND p.fk_product_type = 0";
			}
			
			
			
			$sql.= " AND (p.ref LIKE '%".$idSearch."%' OR p.label LIKE '%".$idSearch."%' ";
			
			if ($conf->barcode->enabled) $sql.= " OR p.barcode='".$idSearch."')";
			else $sql.= ")";
			
			if(!$stock && $conf->global->POS_SERVICES)
			{
			$sql = "SELECT p.rowid, p.ref, p.label, e.rowid as warehouseId";
			$sql.= " FROM ".MAIN_DB_PREFIX."product as p left join ".MAIN_DB_PREFIX."product_stock as w on w.fk_product = p.rowid, ".MAIN_DB_PREFIX."entrepot as e ";
			$sql.= " WHERE (p.tosell = 1 AND  p.entity IN (".getEntity("product", 1).") AND e.rowid=w.fk_entrepot ";
			$sql.= " AND (p.ref LIKE '%".$idSearch."%' OR p.label LIKE '%".$idSearch."%' ";
			if ($conf->barcode->enabled) $sql.= " OR p.barcode='".$idSearch."')";
			else $sql.= ")";
			if(!$conf->global->POS_STOCK){
			$sql.=" AND e.rowid = ".$warehouse. " AND w.reel > 0";}		
	
			$sql.=") OR (p.tosell = 1 AND p.entity IN (".getEntity("product", 1).") AND p.fk_product_type = 1";
			$sql.= " AND (p.ref LIKE '%".$idSearch."%' OR p.label LIKE '%".$idSearch."%' ";
			if ($conf->barcode->enabled) $sql.= " OR p.barcode='".$idSearch."')";
			else $sql.= ")";
			if(!$conf->global->POS_STOCK){
			$sql.=" AND e.rowid = ".$warehouse;}
			$sql.=")";
			}
	
			if(!$stock && $conf->global->POS_STOCK)
			{
				$sql.= " GROUP BY p.label";
			}
			else{
				$sql.= " GROUP BY p.rowid, ep.label";
				$sql.= " ORDER BY p.label, ep.rowid";
			}
			$sql.=" LIMIT 100";
		}
		else 
		{
		$sql= "SELECT distinct p.rowid, p.ref, p.label , w.reel as stock, w.fk_entrepot as warehouseId, e.label as warehouse ";
				$sql.= " FROM ".MAIN_DB_PREFIX."product p INNER JOIN ".MAIN_DB_PREFIX."product_stock w ON w.fk_product=p.rowid ";
				$sql.= " INNER JOIN ".MAIN_DB_PREFIX."entrepot e ON e.rowid=w.fk_entrepot";
				$sql.= " WHERE p.entity IN (".getEntity("product", 1).")";
				$sql.= " AND w.fk_entrepot=".$warehouse;
				if(!$conf->global->POS_SERVICES)
				{
					$sql.= " AND p.fk_product_type = 0";
				}
				$sql.= " AND (p.ref LIKE '%".$idSearch."%' OR p.label LIKE '%".$idSearch."%' ";
				if ($conf->barcode->enabled) $sql.= " OR p.barcode='".$idSearch."')";
				else $sql.= ")";
			if($mode == -1){//no sell
				$sql.= " AND p.tosell = 0";
				$sql.= " LIMIT 100";
			}
			if($mode == -2){//sell
				$sql.= " AND p.tosell = 1";
				$sql.= " LIMIT 100";				
			}
			if($mode == -3){//with stock
				$sql.= " AND w.reel > 0";
				$sql.= " LIMIT 100";
			}
			if($mode == -4){//no stock
				$sql.= " AND w.reel <= 0";
				$sql.= " LIMIT 100";
			}
			if($mode == -5){//best sell
				$sql= "SELECT SUM(fd.qty) as qty, pr.rowid, pr.ref, pr.label, ";
				$sql.="	(select w.reel from ".MAIN_DB_PREFIX."product_stock w left join ".MAIN_DB_PREFIX."entrepot e on w.fk_entrepot = e.rowid";
				$sql.=" where w.fk_product = pr.rowid and e.rowid=ep.rowid) as stock, ep.label as warehouse, ep.rowid as warehouseId";
				$sql.=" FROM ".MAIN_DB_PREFIX."facturedet as fd, ".MAIN_DB_PREFIX."facture as f, ".MAIN_DB_PREFIX."product as pr,";
				$sql.= " ".MAIN_DB_PREFIX."entrepot as ep, ".MAIN_DB_PREFIX."pos_facture as pf ";
				$sql.=" WHERE ep.rowid = ".$warehouse." and pr.tosell = 1 AND f.rowid = fd.fk_facture AND f.entity = ".$conf->entity." and pr.rowid = fd.fk_product";
				$sql.= " AND (pr.ref LIKE '%".$idSearch."%' OR pr.label LIKE '%".$idSearch."%' ";
				if ($conf->barcode->enabled) $sql.= " OR pr.barcode='".$idSearch."')";
				else $sql.= ")";
				if(!$conf->global->POS_SERVICES)
				{
					$sql.= " AND pr.fk_product_type = 0";
				}
				$sql.=" and pf.fk_facture = f.rowid GROUP BY fd.fk_product ORDER BY qty DESC limit 10";
			}
			if ($mode == -6){//worst sell
				/*$sql= "SELECT SUM(fd.qty) as qty, pr.rowid, pr.ref, pr.label, ";
				$sql.="	(select w.reel from llx_product_stock w left join llx_entrepot e on w.fk_entrepot = e.rowid";
				$sql.=" where w.fk_product = pr.rowid and e.rowid=ep.rowid) as stock, ep.label as warehouse, ep.rowid as warehouseId";
				$sql.=" FROM ".MAIN_DB_PREFIX."facturedet as fd, ".MAIN_DB_PREFIX."facture as f, ".MAIN_DB_PREFIX."product as pr,";
				$sql.= " ".MAIN_DB_PREFIX."entrepot as ep, ".MAIN_DB_PREFIX."pos_facture as pf ";
				$sql.=" WHERE pr.tosell = 1 AND f.rowid = fd.fk_facture AND f.entity = ".$conf->entity." and pr.rowid = fd.fk_product";
				$sql.= " AND (pr.ref LIKE '%".$idSearch."%' OR pr.label LIKE '%".$idSearch."%' ";
				if ($conf->barcode->enabled) $sql.= " OR pr.barcode='".$idSearch."')";
				else $sql.= ")";
				$sql.=" and pf.fk_facture = f.rowid GROUP BY fd.fk_product ORDER BY qty ASC limit 10";
				*/
				
				$sql= "SELECT 0 as qty, pr.rowid, pr.ref, pr.label, (select w.reel";
				$sql.= " from ".MAIN_DB_PREFIX."product_stock w left join ".MAIN_DB_PREFIX."entrepot e on w.fk_entrepot = e.rowid";
				$sql.= " where w.fk_product = pr.rowid and e.rowid=ep.rowid) as stock,";
				$sql.= " ep.label as warehouse, ep.rowid as warehouseId";
				$sql.= " from ".MAIN_DB_PREFIX."product as pr, ".MAIN_DB_PREFIX."entrepot as ep";
				$sql.= " where pr.rowid not in ( SELECT p.rowid";
				$sql.= " FROM ".MAIN_DB_PREFIX."facturedet as fd, ".MAIN_DB_PREFIX."facture as f, ".MAIN_DB_PREFIX."product as p, ".MAIN_DB_PREFIX."pos_facture as pf";
				$sql.= " WHERE p.tosell = 1 AND f.rowid = fd.fk_facture AND f.entity = ".$conf->entity." and p.rowid = fd.fk_product";
				$sql.= " AND (pr.ref LIKE '%".$idSearch."%' OR pr.label LIKE '%".$idSearch."%' ";
				if ($conf->barcode->enabled) $sql.= " OR pr.barcode='".$idSearch."')";
				else $sql.= ")";
				if(!$conf->global->POS_SERVICES)
				{
					$sql.= " AND p.fk_product_type = 0";
				}
				$sql.= " and pf.fk_facture = f.rowid group by fd.fk_product ) AND ep.rowid = ".$warehouse;
				if(!$conf->global->POS_SERVICES)
				{
					$sql.= " AND pr.fk_product_type = 0";
				}
				$sql.= " ORDER BY qty ASC limit 10";
			}
		}
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			
			unset($ret);
			
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);
				
				$ret[$i]["id"] = $objp->rowid;
				$ret[$i]["ref"] = $objp->ref;
				$ret[$i]["label"] = $objp->label;
				$ret[$i]["warehouseId"] = $objp->warehouseId;
				
				if ($stock)
				{
					$ret[$i]["warehouse"] = $objp->warehouse;
					//$ret[$i]["warehouseId"] = $objp->warehouseId;
					if($objp->stock)
					{
						$ret[$i]["stock"] = $objp->stock;
					}
					else 
					{
						$ret[$i]["stock"] = 0;
					}
					$ret[$i]["flag"] = $conf->global->POS_STOCK;	
				}
				$i++;
							
			}
			if($mode == -6 && $num < 10){
				$resto = 10 - $num;
				$sql= "SELECT SUM(facd.qty) as qty, p.rowid, p.ref, p.label, (select wa.reel";
				$sql.= " from ".MAIN_DB_PREFIX."product_stock wa left join ".MAIN_DB_PREFIX."entrepot entr on wa.fk_entrepot = entr.rowid";
				$sql.= " where wa.fk_product = p.rowid and entr.rowid=en.rowid) as stock,";
				$sql.= " en.label as warehouse, en.rowid as warehouseId";
				$sql.= " FROM ".MAIN_DB_PREFIX."facturedet as facd, ".MAIN_DB_PREFIX."facture as fac, ".MAIN_DB_PREFIX."product as p, ".MAIN_DB_PREFIX."entrepot as en, ".MAIN_DB_PREFIX."pos_facture as pfac";
				$sql.= " WHERE p.tosell = 1 AND fac.rowid = facd.fk_facture AND fac.entity = ".$conf->entity;
				$sql.= "AND facd.fk_product != 'NULL'and p.rowid = facd.fk_product AND pfac.fk_facture = fac.rowid and en.rowid = ".$warehouse;
				$sql.= " group by facd.fk_product";
				$sql.= " order by qty ASC limit ".$resto;
				
				$resql=$db->query($sql);
				if ($resql)
				{
					$num2 = $db->num_rows($resql);
					$i = $num;
						
					while ($i < $num2)
					{
						$objp = $db->fetch_object($resql);
				
						$ret[$i]["id"] = $objp->rowid;
						$ret[$i]["ref"] = $objp->ref;
						$ret[$i]["label"] = $objp->label;
						$ret[$i]["warehouseId"] = $objp->warehouseId;
				
						if ($stock)
						{
							$ret[$i]["warehouse"] = $objp->warehouse;
							//$ret[$i]["warehouseId"] = $objp->warehouseId;
							if($objp->stock)
							{
								$ret[$i]["stock"] = $objp->stock;
							}
							else
							{
								$ret[$i]["stock"] = 0;
							}
							$ret[$i]["flag"] = $conf->global->POS_STOCK;
						}
						$i++;
							
					}
				}
			}		
		}
		
		return ErrorControl($ret,$function);
	}
	
	public static function CountProduct($warehouseId)
	{
		global $db, $conf;
		
		$i=0;
		
		$ret=-1;
		$function="getProductbyId";
		
		$sql = "select(select count(p.rowid) from ".MAIN_DB_PREFIX."product p, ".MAIN_DB_PREFIX."product_stock ps where p.tosell = 0 and p.fk_product_type = 0 and ps.fk_entrepot = ".$warehouseId." and ps.fk_product = p.rowid) as no_venta, ";
		$sql.= "(select count(p.rowid) from ".MAIN_DB_PREFIX."product p, ".MAIN_DB_PREFIX."product_stock ps where p.tosell = 1 and p.fk_product_type = 0 and ps.fk_entrepot = ".$warehouseId." and ps.fk_product = p.rowid) as en_venta, ";
		$sql.= "(select count(p.rowid) from ".MAIN_DB_PREFIX."product p, ".MAIN_DB_PREFIX."product_stock ps where p.fk_product_type = 0 ";
		$sql.= "and ps.fk_entrepot = ".$warehouseId." and ps.reel > 0 and ps.fk_product = p.rowid) as con_stock, ";
		$sql.= "(select count(p.rowid) from ".MAIN_DB_PREFIX."product p, ".MAIN_DB_PREFIX."product_stock ps where p.fk_product_type = 0 ";
		$sql.= " and ps.fk_entrepot = ".$warehouseId." and ps.reel <= 0 and ps.fk_product = p.rowid) as sin_stock";
		
		$res=$db->query($sql);
		
		if ($res)
		{
			$obj = $db->fetch_object($res);
		
			$result["no_sell"] = $obj->no_venta;
			$result["sell"] = $obj->en_venta;
			$result["stock"] = $obj->con_stock;
			$result["no_stock"] = $obj->sin_stock;
			$result["best_sell"] = 10;
			$result["worst_sell"] = 10;
					
			return ErrorControl($result,$function);
		}
		else
		{
			return ErrorControl($ret, $function);
		}
		
		
	}
	
	
	/**
 	*  Return customer info
 	*  
 	*  @param 		string	$idSearch		Part of code, name, firstname, idprof1
 	*  @param		boolean	$extended		Return more info
 	*  @return      array					Customer info
 	*/
	public static function SearchCustomer($idSearch,$extended=false)
	{
		global $db, $conf;
		
		$ret=-1;
		$function="SearchCustomer";
		
		$i=0;
		
		$sql = "SELECT c.rowid, c.nom, c.code_client, c.siren, c.remise_client";
		$sql.= " FROM ".MAIN_DB_PREFIX."societe as c";
		$sql.= " WHERE c.client = 1";
		$sql.= " AND c.entity = ".$conf->entity;	
		$sql.= " AND (c.nom LIKE '%".$idSearch."%' OR c.code_client LIKE '%".$idSearch."%' OR c.siren LIKE '%".$idSearch."%' ";	
		$sql.= ")";
		$sql.= " ORDER BY c.nom";

		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			
			unset($ret);
			
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);
				$ret[$i]['points'] = null;
				if($conf->global->REWARDS_POS && ! empty($conf->rewards->enabled)){
					$rew= new Rewards($db);
					$res = $rew->getCustomerReward($objp->rowid);
					if($res){
						$ret[$i]['points'] = $rew->getCustomerPoints($objp->rowid);
					}
				}
				$ret[$i]["id"] = $objp->rowid;
				$ret[$i]["nom"] = $objp->nom;
				$ret[$i]["profid1"] = $objp->siren;
				$ret[$i]["remise"] = $objp->remise_client;
				$i++;
							
			}		
		}
		return ErrorControl($ret,$function);
	}
	
	/**
 	*  Return path of a catergory image 
 	*  
 	*  @param 		int		$idCat		Id of Category
 	*  @return      string				Image path
 	*/
	public static function getImageProduct($idProd, $thumb=false)
	{	
		global $conf, $db;
		
		$extName="_small";
		$extImgTarget=".jpg";
		$outDir="pos";
		$maxWidth =90;
		$maxHeight=90;
		$quality=50;
		
		if($idProd>0)
		{
			$objProd = new Product($db);
			$objProd->fetch($idProd);
			$pdir = get_exdir($idProd,2) . $idProd ."/photos/";
			$dir = $conf->product->multidir_output[$objProd->entity].'/'.$pdir;
			
			foreach ($objProd->liste_photos($dir,1) as $key => $obj)
			{
				$filename = $dir.$obj['photo'];
				$filethumbs= $dir.$outDir.'/'.$obj['photo'];
				
				$fileName = preg_replace('/(\.gif|\.jpeg|\.jpg|\.png|\.bmp)$/i','',$filethumbs);
				$fileName = basename($fileName);
				$imgThumbName = $dir.$outDir.'/'.$fileName.$extName.$extImgTarget;
				
				$file_osencoded=$imgThumbName;
				if(!file_exists($file_osencoded))
				{
					$file_osencoded=dol_osencode($filename);
					if (file_exists($file_osencoded))
					{
						require_once(DOL_DOCUMENT_ROOT ."/core/lib/images.lib.php");
						vignette($filename,$maxWidth,$maxHeight,$extName,$quality,$outDir,2);			
					}
				}
				
				if (! $thumb)
				{
					$filename=$obj['photo'];
				}
				else 
				{
					$filename=$outDir.'/'.$fileName.$extName.$extImgTarget;
				}

				$realpath = DOL_URL_ROOT.'/viewimage.php?modulepart=product&entity='.$objProd->entity.'&file='.urlencode($pdir.$filename) ;
				
			}
			if(!$realpath)
			{
				$realpath = DOL_URL_ROOT.'/viewimage.php?modulepart=product&file='.urlencode('noimage.jpg');
			}
			return $realpath;
		}

	}

	/**
 	*  Returns type of disponible docs
 	*  @param		string 	$selected	Code of Doc for select
 	*  @param   	string 	$htmlname	name for object
 	*  @return      array				Disponible docs
 	*/
 	public static function select_Type_Docs($selected='',$htmlname='doctype')
	{
		global $langs, $conf;
		$r=0;
		$langs->load('pos@pos');
		
		if ($conf->facture->enabled)
		{
			$docs[$r]['code'] ='FA';
			$docs[$r]['label']= $langs->trans('Facture');
			$r++;
		}
		if ($conf->commande->enabled)
		{
			$docs[$r]['code'] ='CO';
			$docs[$r]['label']= $langs->trans('Commande');
			$r++;
		}	
		if ($conf->propal->enabled)
		{
			$docs[$r]['code'] ='PR';
			$docs[$r]['label']= $langs->trans('Propale');
			$r++;
		}			
		
		if ($conf->pos->enabled)
		{
			$docs[$r]['code'] ='TK';
			$docs[$r]['label']= $langs->trans('Pos');
			$r++;
		}
		
		return $docs;
	}
	
	/**
 	*  Returns internal users of Dolibarr
 	*  @param 		string	$selected		RowId of user for select
 	*  @param    	string	$htmlname		name for object
 	*  @return      array					Dolibarr internal users
 	*/
	public static function select_Users($selected='',$htmlname='users')
	{
		global $db,$conf;
		
		$sql = "SELECT rowid, name, firstname, fk_societe, email";
		$sql.= " FROM ".MAIN_DB_PREFIX."user";
		$sql.= " WHERE entity IN (0,".$conf->entity.")";
	
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$var = true;
			$i = 0;
			
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$var=!$var;
				if (!$obj->fk_societe)
				{
					$userstatic=new User($db);
					$userstatic->fetch($obj->rowid); 
					$userstatic->getrights();
					$dir=$conf->user->dir_output;
					$file='';
					
					if($userstatic->rights->pos->frontend)
					{
						$username = $obj->firstname.' '.$obj->name;
						$internalusers[$i]['code'] = $obj->rowid;
						$internalusers[$i]['label'] = $username;
						
						if ($userstatic->photo) $file=get_exdir($userstatic->id,2).$userstatic->photo;
						if ($file && file_exists($dir."/".$file)){
							$internalusers[$i]['photo'] = DOL_URL_ROOT.'/viewimage.php?modulepart=userphoto&entity='.$userstatic->entity.'&file='.urlencode($file);
						}
						else{
							$internalusers[$i]['photo'] = DOL_URL_ROOT.'/theme/common/nophoto.jpg';
						}
					}
				}
				
				$i++;
			}
			$db->free($resql);
		}
		
		return $internalusers;
	}
	
	/**
	 * Returns the type payments
	 * 
	 * @return		array					type of payments
	 */
	public static function select_Type_Payments()
	{
		global $db,$conf,$langs;
		
		$cash = new Cash($db);
        	
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
		
		$sql = "SELECT id, code, libelle, type";
        $sql.= " FROM ".MAIN_DB_PREFIX."c_paiement";
        $sql.= " WHERE active > 0 and (id = ".$cash->fk_modepaycash." or id =".$cash->fk_modepaybank.")";
        $sql.= " ORDER BY id";

        $resql = $db->query($sql);
        
        if ($resql)
        {
        	$langs->load("bills");
            $num = $db->num_rows($resql);
            $i = 0;
            while ($i < $num)
            {
                $obj = $db->fetch_object($resql);

                $libelle=($langs->trans("PaymentTypeShort".$obj->code)!=("PaymentTypeShort".$obj->code)?$langs->trans("PaymentTypeShort".$obj->code):($obj->libelle!='-'?$obj->libelle:''));
                $payments[$i]['id'] =$obj->id;
                $payments[$i]['code'] =$obj->code;
                $payments[$i]['label']=$libelle;
                $payments[$i]['type'] =$obj->type;
                $i++;
            }
            $db->free($resql);
        }
        
 		
		return $payments;
		
	}
	
	 /**
     *	Get object and lines from database
     * 	@param		int $idTicket	Id of ticket
     *	@return    	Object 			object if OK, <0 if KO
     */
    function fetch($idTicket)
    {
		global $db;
    	$object= new Ticket($db);
    	$res= $object->fetch($idTicket);
    	if($res)
    		return $object;
    	else
    		return -1;
    }
    
	/**
	 * 
	 * Set Ticket into DB
	 * 
	 * @param		array 	$aryTicket 	Ticket object
	 * @return		array	$result		Result
	 */
	public static function SetTicket($aryTicket)
	{	
		$function="SetTicket";
		$res = 0 ;
		
		$data = $aryTicket['data'];
		$lines = $data['lines'];
		
		if(sizeof($data)>0)
		{
			if($data['mode']==0){
				if($data['id'])
				{
					$res = self::UpdateTicket($aryTicket);
				}
				else 
				{
					$res = self::CreateTicket($aryTicket);
				}
			}
			else
			{
				$res = self::CreateFacture($aryTicket);
			}
			
		}
		
		return ErrorControl($res,$function);
	}
	
	/**
	 * 
	 * Get Ticket from DB
	 * 
	 * @param 	int		$id		Id Ticket to load
	 * @return	array			Array with data	
	 */
	public static function GetTicket($id)
	{
		$function="GetTicket";
		$res = 0 ;
		
		if($id)
		{
			$ret=self::LoadTicket($id);		
			return ErrorControl($ret, $function);
		}
		else
		{
			return ErrorControl($res,$function);		
		}
	}
	
	/**
	 *
	 * Get Facture from DB
	 *
	 * @param 	int		$id		Id Ticket to load
	 * @return	array			Array with data
	 */
	public static function GetFacture($id)
	{
		$function="GetTicket";
		$res = 0 ;
	
		if($id)
		{
			$ret=self::LoadFacture($id);
			return ErrorControl($ret, $function);
		}
		else
		{
			return ErrorControl($res,$function);
		}
	}
	
	/**
	 * 
	 * Load Ticket from DB
	 * 
	 * @param 	int 	$id		Id of ticket
	 * @return	array			Array with ticket data
	 */
	Private function LoadTicket($id)
	{
		global $db;
		$dataticket = array();
		
		$data = array();
		
		$object = new Ticket($db);
		$res=$object->fetch($id);
		
		if($res)
		{
			$data['id'] = $object->id;
			$data['type'] = $object->type;
			$data['customerId'] = $object->socid;
			$data['state'] = $object->statut;
			$data['discount_percent'] = $object->remise_percent;
			$data['discount_qty'] = $object->remise_absolut;
			$data['payment_type'] =$object->mode_reglement_id;
			$data['customerpay'] =$object->customer_pay;
			$data['difpayment'] = $object->diff_payment;
			$data['total_ttc'] = $object->total_ttc;
			$data['id_place'] = $object->fk_place;
			$data['note'] = $object->note;
			$data['lines'] = self::LoadTicketLines($object->lines);
		
			$dataticket['data']= $data;
			return $dataticket;	
		}
		else
		{
			return $res;
		}
	}
	
	/**
	 *
	 * Load Facture from DB
	 *
	 * @param 	int 	$id		Id of facture
	 * @return	array			Array with ticket data
	 */
	Private function LoadFacture($id)
	{
		global $db;
		$dataticket = array();
	
		$data = array();
	
		$object = new Facture($db);
		$res=$object->fetch($id);
	
		if($res)
		{
			$data['id'] = $object->id;
			$data['type'] = $object->type;
			$data['customerId'] = $object->socid;
			$data['state'] = $object->statut;
			$data['discount_percent'] = $object->remise_percent;
			$data['discount_qty'] = $object->remise_absolue;
			$data['payment_type'] =$object->mode_reglement_id;
			$data['total_ttc'] = $object->total_ttc;
			$data['lines'] = self::LoadFactureLines($object->lines);
	
			$dataticket['data']= $data;
			return $dataticket;
		}
		else
		{
			return $res;
		}
	}
	
	/**
	 * 
	 * Load lines of a ticket.
	 * 
	 * @param 	array 	$lines		Lines into database
	 * @return	array				Lines for front end
	 */
	private function LoadTicketLines($lines)
	{
		global $db;
		$aryLines = array();
		$prod = new Product($db);
		$i=0;
		foreach ( $lines as $line )
		{
			if(sizeof($line)>0)
			{	
				$prod->fetch($line->fk_product);
				//$price_ttx = $line->price+($line->price*$line->tva_tx/100);				
				$aryLines[$i]['id'] = $line->rowid;
				$aryLines[$i]['label'] = $prod->label;
				$aryLines[$i]['price'] = $line->subprice;
				$aryLines[$i]['cant'] = $line->qty;
				$aryLines[$i]['tva_tx'] = $line->tva_tx;
				$aryLines[$i]['localtax1_tx'] = $line->localtax1_tx;
				$aryLines[$i]['localtax2_tx'] = $line->localtax2_tx;
				$aryLines[$i]['idProduct'] = $line->fk_product;
				$aryLines[$i]['discount'] = $line->remise_percent;
				$aryLines[$i]['total_ttc'] = $line->total_ttc;
				$aryLines[$i]['remise'] = $line->remise;
				$aryLines[$i]['fk_product_type'] = $line->fk_product_type;
				if($line->note != 'null')$aryLines[$i]['note'] = $line->note;
				else $aryLines[$i]['note'] = '';
				
												
				$i++;	
			}
		}
		return $aryLines;
	}
	
	/**
	 *
	 * Load lines of a facture.
	 *
	 * @param 	array 	$lines		Lines into database
	 * @return	array				Lines for front end
	 */
	private function LoadFactureLines($lines)
	{
		global $db;
		$aryLines = array();
		$prod = new Product($db);
		$i=0;
		foreach ( $lines as $line )
		{
			if(sizeof($line)>0)
			{
				$prod->fetch($line->fk_product);
				$aryLines[$i]['id'] = $line->rowid;
				$aryLines[$i]['label'] = $prod->label;
				$aryLines[$i]['price'] = $line->subprice;
				$aryLines[$i]['cant'] = $line->qty;
				$aryLines[$i]['tva_tx'] = $line->tva_tx;
				$aryLines[$i]['localtax1_tx'] = $line->localtax1_tx;
				$aryLines[$i]['localtax2_tx'] = $line->localtax2_tx;
				$aryLines[$i]['idProduct'] = $line->fk_product;
				$aryLines[$i]['discount'] = $line->remise_percent;
				$aryLines[$i]['total_ttc'] = $line->total_ttc;
	
				$i++;
			}
		}
		return $aryLines;
	}
	
	/**
	 * 
	 * Create ticket into Database
	 * 
	 * @param	array	$aryTicket		Ticket object
	 */
	Private function CreateTicket($aryTicket)
	{
		global $db,$user,$conf;
		
		$function="CreateTicket";
		$idTicket = -1 ;
		
		$data = $aryTicket['data'];
		$lines = $data['lines'];
        
		if($data['idsource']>0)
		{
			$prods_returned=self::testSource($aryTicket);
			
			if(sizeof($prods_returned)>0)
			{
				return -6;
			}
			$vater=self::fetch($data['idsource']);
			
			$data['payment_type']=$vater->mode_reglement_id;
		}
		
		$cash = new Cash($db);
		 
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
		
        if(! $data['customerId'])
        {
        	
        	$socid=$cash->fk_soc;
        	$data['customerId']=$socid;

        }
        else 
        {
        	$socid=$data['customerId'];
        }
        
		if(! $data['employeeId'])
        {
        	$employee=$_SESSION['uid'];

        }
        else 
        {
        	$employee=$data['employeeId'];
        }
        
		$object = new Ticket($db);
		$object->type=$data['type'];
		$object->socid= $socid;
		$object->statut = $data['state'];
		$object->fk_cash = $_SESSION['TERMINAL_ID'];
		$object->remise_percent = $data['discount_percent'];
		$object->remise_absolut = $data['discount_qty'];
		$object->mode_reglement_id = $data['payment_type'];
		$object->fk_place = $data['id_place'];  
		$object->note = $data['note'];  
				
		if($data['payment_type']!=$cash->fk_modepaycash)
		{
			if($data['points'] > 0)
				$object->customer_pay = $data['total_with_points'];
			else
				$object->customer_pay = $data['total'];
		}
		else
		{
			$object->customer_pay = $data['customerpay'];
		}
		$data['customerpay'] = $object->customer_pay;
		$object->diff_payment = $data['difpayment'];
		$object->id_source = $data['idsource'];
		
		//$object->note=$note;
		//$object->notepublic=$notepublic;
		
		$db->begin;
		
		$idTicket=$object->create($employee,1,0);
		$data['ref'] = $object->ref;
		
		if($idTicket<0) 
		{
			$db->rollback();
			return -1;
		}
		else 
		{
			//Adding lines
			$data['id']=$idTicket;
			if($data['id_place'])
			{
				$place = new Place($db);
				$place->fetch($data['id_place']);
				$place->fk_ticket = $idTicket;
				$place->set_place($idTicket);
			}
			$idLines = self::addTicketLines($lines,$idTicket,($object->type==1 ? true:false));
						
			if($idLines<0) 
			{
				$db->rollback();
				return -2;
			}
			else 
			{
				if($object->fk_place)
				{
					$place = new Place($db);
					$place->fetch($object->fk_place);
				}
				
				if($object->statut!=0)
				{
					//Adding Payments
					$payment=self::addPayment($data);
					if(!$payment)
					{
						$db->rollback();
						return -3;
					}
					else
					{
						if($object->diff_payment <= 0)
						{
							$object->set_paid($user);
						}
					}
					//Decrease stock
					
					$stock=self::quitSotck($lines,($object->type==1 ? true:false));
					
					if($stock)
					{
						$db->rollback();
						return -4;
					}
					
					// TODO liberar puesto
					if($place)
					{
						$place->free_place();
					}					
				}
				else
				{
					// TODO usar puesto
					if($place)
					{
						$place->set_place($idTicket);
					}	
				}
			}
		}
	
		
		$db->commit;
		
		return $idTicket;
	}
	
	/**
	 *
	 * Create facture into Database
	 *
	 * @param	array	$aryTicket		Ticket object
	 */
	Private function CreateFacture($aryTicket)
	{
		global $db,$user,$conf;
		
		$function="CreateFacture";
		$idFacture = -1 ;
		
		$data = $aryTicket['data'];
		$lines = $data['lines'];
        $idTicket = $data["id"];
		
		if($data['idsource']>0)
		{
			$prods_returned=self::testSourceFac($aryTicket);
			
			if(sizeof($prods_returned)>0)
			{
				return -6;
			}
			$vater = new Facture($db);
			$vater->fetch($data['idsource']);
			
			$data['payment_type']=$vater->mode_reglement_id;
		}
		
		$cash = new Cash($db);
		 
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
		
        if(! $data['customerId'])
        {
        	
        	$socid=$cash->fk_soc;
        	$data['customerId']=$socid;

        }
        else 
        {
        	$socid=$data['customerId'];
        }
        
		if(! $data['employeeId'])
        {
        	$employee=$_SESSION['uid'];

        }
        else 
        {
        	$employee=$data['employeeId'];
        }
        if($data['mode']==1){
			$object = new Facturesim($db);
        }
        else{
        	$object = new Facture($db);
        }
		$object->type=($data['type']==0?0:2);
		$object->socid= $socid;
		$object->statut = $data['state'];
		$object->fk_cash = $_SESSION['TERMINAL_ID'];
		$object->remise_percent = $data['discount_percent'];
		$object->remise_absolue = $data['discount_qty'];
		$object->mode_reglement_id = $data['payment_type'];
		$object->fk_place = $data['id_place'];  
		$object->note_private = $data['note'];  
				
		if($data['payment_type']!=$cash->fk_modepaycash)
		{
			if($data['points'] > 0)
				$object->customer_pay = $data['total_with_points'];
			else
				$object->customer_pay = $data['total'];
		}
		else
		{
			$object->customer_pay = $data['customerpay'];
		}
		
		$data['customerpay'] = $object->customer_pay;
		$object->diff_payment = $data['difpayment'];
		$object->fk_facture_source = $data['idsource'];
		
		//$object->note=$note;
		//$object->notepublic=$notepublic;
		$employ = new User($db);
		$employ->fetch($employee);
		$now = dol_now();
		$object->date = $now;
				
		$db->begin;
		
		$idFacture=$object->create($employ);
		if ($object->statut==1 || $object->type==2)
		{
			$res = $object->validate($employ);
			if($res < 0){
				$num = $object->getNextNumRef($socid);
				// Validate
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'facture';
				$sql.= " SET facnumber='".$num."', fk_statut = 1, fk_user_valid = ".$employ->id.", date_valid = '".$db->idate($now)."'";
				if (! empty($conf->global->FAC_FORCE_DATE_VALIDATION))	// If option enabled, we force invoice date
				{
					$sql.= ', datef='.$db->idate($now);
					$sql.= ', date_lim_reglement='.$db->idate($now);
				}
				$sql.= ' WHERE rowid = '.$object->id;
				
				dol_syslog(get_class($this)."::validate sql=".$sql);
				$resql=$db->query($sql);
				$object->ref = $num;
			}
			
		}
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'pos_facture (fk_cash, fk_place,fk_facture) VALUES ('.$object->fk_cash.','.($object->fk_place ? $object->fk_place: 'null').','.$idFacture.')';
		 
		dol_syslog("pos_facture::update sql=".$sql);
		$resql=$db->query($sql);
		if (! $resql)
		{
			$this->db->rollback();
			return -1;
		}
		$data['ref'] = $object->ref;
		
		if($idFacture<0) 
		{
			$db->rollback();
			return -1;
		}
		else 
		{
			//Adding lines
			$data['id']=$idFacture;
			
			$idLines = self::addFactureLines($lines,$idFacture,($object->type==1 ? true:false));
						
			if($idLines<0) 
			{
				$db->rollback();
				return -2;
			}
			else 
			{
				//Adding Payments
				$payment=self::addPaymentFac($data);
				if(!$payment)
				{
					$db->rollback();
					return -3;
				}
				else
				{
					if($object->diff_payment <= 0)
					{
						$object->set_paid($user);
					}
				}
				//Decrease stock
				
				$stock=self::quitSotck($lines,($object->type==2 ? true:false));
				
				if($stock)
				{
					$db->rollback();
					return -4;
				}
			}
		}
		$pay = $object->diff_payment < 0 ? $object->customer_pay+$object->diff_payment:$object->customer_pay;
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiement_facture (fk_facture, fk_paiement, amount)';
		$sql .= ' VALUES ('.$idFacture.', '. $payment.', \''.$pay.'\')';
			
		dol_syslog("Ticket-Facture pay::insert sql=".$sql);
		$resql=$db->query($sql);
		
		$db->commit;
		
		if($idTicket){
			$ticket= new Ticket($db);
			$ticket->fetch($idTicket);
			$ticket->delete_ticket();
		}
		
		return $idFacture;
	}
	
	
	/**
	 * 
	 * Update Ticket into Database
	 * @param	array 		$aryTicket		Ticket object	
	 */
	private function UpdateTicket($aryTicket)
	{
		global $db, $conf;
		
		$function="UpdateTicket";
		$idTicket = -1 ;
		
		$data = $aryTicket['data'];
		$lines = $data['lines'];
        
        $idTicket = $data['id'];
        $statut = 0;
        
        if(! $data['customerId'])
        {
        	$cash = new Cash($db);
        	
        	$terminal = $_SESSION['TERMINAL_ID'];
        	$cash->fetch($terminal);
        	$socid=$cash->fk_soc;

        }
        else 
        {
        	$socid=$data['customerId'];
        }
        
		if(! $data['employeeId'])
        {
        	$employee=$_SESSION['uid'];

        }
        else 
        {
        	$employee=$data['employeeId'];
        }
        
		$object = new Ticket($db);
		$object->fetch($idTicket);
		
		$object->type=$data['type'];
		$object->socid= $socid;
		$object->statut = $data['state'];
		$object->fk_cash = $_SESSION['TERMINAL_ID'];
		$object->remise_percent = $data['discount_percent'];
		$object->remise_absolut = $data['discount_qty'];
		$object->mode_reglement_id = $data['payment_type'];
		$object->fk_place = $data['id_place'];
		$object->note = $data['note'];		
		
		$cash=new Cash($db);
		$cash->fetch($_SESSION['TERMINAL_ID']);
				
		if($data['payment_type']!=$cash->fk_modepaycash)
		{
			if($data['points'] > 0)
				$object->customer_pay = $data['total_with_points'];
			else
				$object->customer_pay = $data['total'];
		}
		else
		{
			$object->customer_pay = $data['customerpay'];
		}
		$data['customerpay'] = $object->customer_pay;
		$object->diff_payment = $data['difpayment'];
		$object->id_source = $data['idsource'];
		
		$userstatic=new User($db);
		$userstatic->fetch($employee); 
		
		$db->begin;
		
		$res=$object->update($userstatic->id);
		$data['ref'] = $object->ref;
		if($res<0) 
		{
			$db->rollback();
			return -5;
		}
		else 
		{
			//Adding lines
			$idLines = self::addTicketLines($lines,$idTicket);
			if($idLines<0) 
			{
				$db->rollback();
				return -2;
			}
			else 
			{
				$place = new Place($db);
				$place->fetch($object->fk_place);
				
				if($object->statut!=0)
				{
					//Adding Payments
					$payment=self::addPayment($data);
					if(!$payment)
					{
						$db->rollback();
						return -3;
					}
					else 
					{
						if($object->diff_payment <= 0)
						{
							$object->set_paid($user);
						}
					}
					//Decrease stock
					$stock=self::quitSotck($lines);
					if($stock)
					{
						$db->rollback();
						return -4;
					}
					
					//TODO: liberar puesto
					$place->free_place();
					
				}
				else 
				{
					//TODO: usar puesto
					$place->set_place($idTicket);
					
				}
			}
		}
		
		$db->commit;
		return $idTicket;
		
	}
	
	/**
     *	Delete ticket
     *	@param     	int		$idTicket    Id of ticket to delete
     *	@return		int					<0 if KO, >0 if OK
     */
	public static function DeleteTicket($idTicket=0)
	{
		global $db;
		
		$object= new Ticket($db);
		$db->begin;
		$res=$object->delete($idTicket);
		
		if ($res==1)
		{
			$reslines=DeleteTicketLines($id);
			if($reslines==1)
			{
				$db->commit();		
			}
			else 
			{
				$db->rollback();
				$res=-1;
			}
		}
		else 
		{
			$db->rollback;
		}
		
		return $res;
	}
	
	/**
     * 		Add ticket line into database (linked to product/service or not)
     * 		@param    	array	$lines           	Ticket Lines
     *    	@return    	array             			Result of adding
     */
    private function addTicketLines($lines, $idTicket,$isreturn=false)
    {
    	global $db;
    	
		$res=0;
		
		self::deleteTicketLines($idTicket);
		
		$object= new Ticket($db);
		$object->fetch($idTicket);
		
    	if (sizeof ($lines) > 0)
		{
			foreach ( $lines as $line )
			{
				if(sizeof($line)>0)
				{
					if ($line['idProduct']>0)
		    		{
		    			$product_static=new Product($db);
						$product_static->id = $line['idProduct'];
						$product_static->load_stock();
		
						if ($product_static->stock_reel < 1 ||$product_static->stock_reel<$line['cant']) 
						{
							$res=-4;
						}

						
						if(!$isreturn)
						{					
							$qty=$line['cant'];
						}
						else 
						{
							$qty=$line['cant']*-1;
						}
						$line['discount'] = $line['discount']+ $object->remise_percent;
						$line['description']= $line['description']." ".$line['note'];
						$res=$object->addline($idTicket, $line['description'], $line['price'], $qty, $line['tva_tx'], $line['localtax1_tx'], $line['localtax2_tx'], $line['idProduct'], $line['discount'], $line['note'], $line['fk_product_type'], $line['price_ttc'], $line['price_base_type']);
													
		    		}
				}
				else 
				{
					$res = -1;    	
				}
			
			}	
		}
		return $res;
		
    }
    
    /**
     * 		Add ticket line into database (linked to product/service or not)
     * 		@param    	array	$lines           	Ticket Lines
     *    	@return    	array             			Result of adding
     */
    private function addFactureLines($lines, $idTicket,$isreturn=false)
    {
    	global $db;
    	 
    	$res=0;
    
    	$object= new Facture($db);
    	$object->fetch($idTicket);
		$object->brouillon=1;
    
    	if (sizeof ($lines) > 0)
    	{
    		foreach ( $lines as $line )
    		{
    			if(sizeof($line)>0)
    			{
    				if ($line['idProduct']>0)
    				{
    					$product_static=new Product($db);
    					$product_static->id = $line['idProduct'];
    					$product_static->load_stock();
    
    					if ($product_static->stock_reel < 1 ||$product_static->stock_reel<$line['cant'])
    					{
    						$res=-4;
    					}
    
    
    					if(!$isreturn)
    					{
    						$qty=$line['cant'];
    					}
    					else
    					{
    						$qty=$line['cant']*-1;
    					}
    					$object->brouillon=1;
    					$line['discount'] = $line['discount']+ $object->remise_percent;
    					$line['description']= $line['description']." ".$line['note'];
    					$res=$object->addline($idTicket, $line['description'], $line['price'], $qty, $line['tva_tx'], $line['localtax1_tx'], $line['localtax2_tx'], $line['idProduct'], $line['discount'], '','',0,0,'', $line['price_base_type'], $line['price_ttc'], $line['fk_product_type']);
    						
    				}
    			}
    			else
    			{
    				$res = -1;
    			}
    				
    		}
    	}
    	return $res;
    
    }
    
     /**
     *	Update a detail line
     *	@param    	array	$line	Line Ticket
     *	@return    	array           Result of update
     */
    public static function updateTicketLine($line)
    {
    	global $db;
		$object= new Ticket($db);
		if(sizeof($line)>0)
			$res=$object->updateline($line->$idTicketLine,$line->desc, $line->pu, $line->qty, $line->remise_percent, '', '', $line->txtva, $line->txlocaltax1, $line->txlocaltax2,$line->price_base_type);
		else
			$res=-1;
		return $res;
    }
    
 	/**
     *	Delete line in database
     *	@param		int		$idTicket 	Id Ticket to delete lines
     *	@return		int						<0 if KO, >0 if OK
     */
	public static function deleteTicketLines($idTicket)
    {
    	global $db, $conf;
    	
    	$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX."pos_ticketdet";
		$sql.= " WHERE  fk_ticket= ".$idTicket;

   		$resql = $db->query ($sql);
   		
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$object= new Ticket($db);	
			
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);
				$res=$object->deleteline($objp->rowid);
				if ($res!=1)
				{
					return -1;
				}
				
				$i++;
			}
			
		}
		return 1;
    }
    
    /**
     * 
     * Returns terminals of POS
     */
	public static function select_Terminals()
    {
    	global $db, $conf;
    	
    	$sql = "SELECT rowid, code, name, fk_device, is_used, fk_user_u, tactil";
		$sql.= " FROM ".MAIN_DB_PREFIX."pos_cash";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND is_used = 0 OR (is_used=1 AND is_closed=1)";

   		$res = $db->query ($sql);
   		
		if ($res)
		{
			//require_once(DOL_DOCUMENT_ROOT ."/pos/backend/class/cash.class.php");
			$terms = array ();
			$i=0;
			while ($record = $db->fetch_array ($res))
			{
				foreach ( $record as $cle => $valeur )
				{
					$terms[$i][$cle] = $valeur;
				}
				$i++;
				
				
				/*$term = new Cash($db);
				$term->fetch($record['rowid']);
				$terms[$record['id']] = $term;*/
			}
			return $terms;
		}
		else
		{
			return -1;
		}
    }
    
	/**
	 * 
	 * Return Ticket history
	 * 
	 * @param 	string	$ticketnumber	ticket number for filter
	 * @param 	int		$stat			status of ticket
	 * @param  int     $mode			0, count rows; 1, get rows
	 * @param 	string	$terminal		terminal for filter
	 * @param 	string	$seller			seller user for filter
	 * @param 	string	$client			client for filter
	 * @param 	float	$amount			amount for filter
	 * @param 	int		$month			month for filter
	 * @param 	int		$year			year for filter
	 */
	public static function getHistoric($ticketnumber='',$stat, $terminal='',$seller='',$client='',$amount='',$months=0,$years=0)
	{
		global $db, $conf;
		
		$ret=-1;
		$function="GetHistoric";
		
		$sql= ' SELECT ';
		
		$sql.= ' f.rowid as ticketid, f.ticketnumber, f.total_ttc,';
		$sql.= ' f.date_closed, f.fk_user_close, f.date_creation as datec,';
		$sql.= ' f.fk_statut, f.customer_pay, f.difpayment, f.fk_place, ';
		$sql.= ' s.nom, s.rowid as socid,';
		$sql.= ' u.firstname, u.name,';
		$sql.= ' t.name, f.fk_cash, f.type';
		
		$sql.= ' FROM '.MAIN_DB_PREFIX.'societe as s';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_cash as t';
		$sql.= ', '.MAIN_DB_PREFIX.'user as u';
		$sql.= ' WHERE f.fk_soc = s.rowid';
		$sql.= " AND f.entity = ".$conf->entity;
		$sql.= " AND f.fk_cash = t.rowid";
		if($stat >= 0 && $stat !=4 && $stat <= 99){
			$sql.= " AND f.fk_statut = ".$stat;
			$sql.= " AND f.type = 0";
		}
		if($stat == 4){
			$sql.= " AND f.type = 1";
		}
			
		if ($socid) $sql.= ' AND s.rowid = '.$socid;
		
		if ($ticketnumber)
		{
			$sql.= ' AND f.ticketnumber LIKE \'%'.$db->escape(trim($ticketnumber)).'%\'';
		}
		if ($months > 0)
		{
			if ($years > 0)
				$sql.= " AND f.date_ticket BETWEEN '".$db->idate(dol_get_first_day($years,$months,false))."' AND '".$db->idate(dol_get_last_day($years,$months,false))."'";
			else
				$sql.= " AND date_format(f.date_ticket, '%m') = '".$months."'";
		}
		else if ($years > 0)
		{
			$sql.= " AND f.date_ticket BETWEEN '".$db->idate(dol_get_first_day($years,1,false))."' AND '".$db->idate(dol_get_last_day($years,12,false))."'";
		}
		$now = dol_now();
		$time = dol_getdate($now);
		$day = $time['mday'];
		$month = $time['mon'];
		$year = $time['year'];
		
		if($stat == 100)	{//Today
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 101)	{//Yesterday
			$time = dol_get_prev_day($day, $month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 102)	{//This week
			$time = dol_get_first_day_week($day,$month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['first_day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 103)	{//Last week
			$time = dol_get_first_day_week($day, $month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['prev_year'],$time['prev_month'],$time['prev_day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$time['first_day']-1==0?$time['prev_month']:$month,$time['first_day']-1==0?$time['prev_day']+6:$time['first_day']-1,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 104)	{//Two weeks ago
			$time = dol_get_prev_week($day,'', $month, $year);
			$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 105)	{//Three weeks ago
			$time = dol_get_prev_week($day,'', $month, $year);
			$time = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 106)	{//This month
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,01,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 107)	{//One month ago
			$time = dol_get_prev_month($month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$day,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 108)	{//Last month
			$time = dol_get_prev_month($month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],01,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],31,0,0,0);
			$sql.= " AND f.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if ($terminal)
		{
			$sql.= ' AND t.name LIKE \'%'.$db->escape(trim($terminal)).'%\'';
		}
		if ($seller)
		{
			$sql.= ' AND (u.firstname LIKE \'%'.$db->escape(trim($seller)).'%\'';
			$sql.= ' OR u.name LIKE \'%'.$db->escape(trim($seller)).'%\')';
		}
		if ($client)
		{
			$sql.= ' AND s.nom LIKE \'%'.$db->escape(trim($client)).'%\'';
		}
		
		if ($amount)
		{
			$sql.= ' AND f.total_ttc = \''.$db->escape(trim($amount)).'\'';
		}
				
		$sql.= ' GROUP BY f.rowid';
		
		$sql.= ' ORDER BY ';
		$sql.= ' datec DESC ';
		$sql.= 'LIMIT 0,50';
		
		$res = $db->query($sql);
   		
		if ($res)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$ticketstatic=new Ticket($db);	
			while ($i < $num)
			{
				$obj = $db->fetch_object($res);
				
				$tickets[$i]["id"] = $obj->ticketid;
				$tickets[$i]["type"] = $obj->type;
				$tickets[$i]["ticketnumber"] = $obj->ticketnumber;
				$tickets[$i]["date_creation"] = dol_print_date($db->jdate($obj->datec),'dayhour');
				$tickets[$i]["date_close"] = dol_print_date($db->jdate($obj->date_closed),'dayhour');
				$tickets[$i]["fk_place"] = $obj->fk_place;
								
				$cash=new Cash($db);
				$cash->fetch($obj->fk_cash);
				$tickets[$i]["terminal"] = $cash->name;
				
				$userstatic=new User($db);
	        	$userstatic->fetch($obj->fk_user_close);
				$tickets[$i]["seller"]=$userstatic->getFullName($langs);
				
				$tickets[$i]["client"] = $obj->nom;
				$tickets[$i]["amount"] = $obj->total_ttc;
				$tickets[$i]["customer_pay"] = $obj->customer_pay;
				$tickets[$i]["statut"] = $obj->fk_statut;					
				$tickets[$i]["statutlabel"] = $ticketstatic->LibStatut($obj->fk_statut,0);

				$i++;
			}
			return ErrorControl($tickets,$function);
			
				
		}
		else
		{
			return ErrorControl($ret, $function);
		}
		
	}
	
	/**
	 *
	 * Return Facture history
	 *
	 * @param 	string	$ticketnumber	ticket number for filter
	 * @param 	int		$stat			status of ticket
	 * @param  int     $mode			0, count rows; 1, get rows
	 * @param 	string	$terminal		terminal for filter
	 * @param 	string	$seller			seller user for filter
	 * @param 	string	$client			client for filter
	 * @param 	float	$amount			amount for filter
	 * @param 	int		$month			month for filter
	 * @param 	int		$year			year for filter
	 */
	public static function getHistoricFac($ticketnumber='',$stat, $terminal='',$seller='',$client='',$amount='',$months=0,$years=0)
	{
		global $db, $conf;
	
		$ret=-1;
		$function="GetHistoric";
	
		$sql= ' SELECT ';
	
		$sql.= ' f.rowid as ticketid, f.facnumber, f.total_ttc,';
		$sql.= ' f.fk_user_valid, f.datec as datec,';
		$sql.= ' f.fk_statut, pf.fk_place, ';
		$sql.= ' s.nom, s.rowid as socid,';
		$sql.= ' u.firstname, u.name,';
		$sql.= ' t.name, pf.fk_cash, f.type';
	
		$sql.= ' FROM '.MAIN_DB_PREFIX.'societe as s';
		$sql.= ', '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_cash as t';
		$sql.= ', '.MAIN_DB_PREFIX.'user as u';
		$sql.= ' WHERE f.fk_soc = s.rowid';
		$sql.= " AND f.entity = ".$conf->entity;
		$sql.= " AND pf.fk_cash = t.rowid";
		$sql.= " AND pf.fk_facture = f.rowid";
		$sql.= " AND u.rowid = f.fk_user_valid";
		if($stat >= 0 && $stat !=4 && $stat <= 99){
			$sql.= " AND f.fk_statut = ".$stat;
			$sql.= " AND f.type = 0";
		}
		if($stat == 4){
			$sql.= " AND f.type = 2";
		}
			
		if ($socid) $sql.= ' AND s.rowid = '.$socid;
	
		if ($ticketnumber)
		{
			$sql.= ' AND f.facnumber LIKE \'%'.$db->escape(trim($ticketnumber)).'%\'';
		}
		if ($months > 0)
		{
			if ($years > 0)
				$sql.= " AND f.datec BETWEEN '".$db->idate(dol_get_first_day($years,$months,false))."' AND '".$db->idate(dol_get_last_day($years,$months,false))."'";
			else
				$sql.= " AND date_format(f.datec, '%m') = '".$months."'";
		}
		else if ($years > 0)
		{
			$sql.= " AND f.datec BETWEEN '".$db->idate(dol_get_first_day($years,1,false))."' AND '".$db->idate(dol_get_last_day($years,12,false))."'";
		}
		$now = dol_now();
		$time = dol_getdate($now);
		$day = $time['mday'];
		$month = $time['mon'];
		$year = $time['year'];
	
		if($stat == 100)	{//Today
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 101)	{//Yesterday
			$time = dol_get_prev_day($day, $month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 102)	{//This week
			$time = dol_get_first_day_week($day,$month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['first_day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 103)	{//Last week
			$time = dol_get_first_day_week($day, $month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['prev_year'],$time['prev_month'],$time['prev_day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$time['first_day']-1==0?$time['prev_month']:$month,$time['first_day']-1==0?$time['prev_day']+6:$time['first_day']-1,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 104)	{//Two weeks ago
			$time = dol_get_prev_week($day,'', $month, $year);
			$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 105)	{//Three weeks ago
			$time = dol_get_prev_week($day,'', $month, $year);
			$time = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 106)	{//This month
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,01,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 107)	{//One month ago
			$time = dol_get_prev_month($month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$day,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 108)	{//Last month
			$time = dol_get_prev_month($month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],01,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],31,0,0,0);
			$sql.= " AND f.datec BETWEEN '".$ini."' AND '".$fin."'";
		}
		if ($terminal)
		{
			$sql.= ' AND t.name LIKE \'%'.$db->escape(trim($terminal)).'%\'';
		}
		if ($seller)
		{
			$sql.= ' AND (u.firstname LIKE \'%'.$db->escape(trim($seller)).'%\'';
			$sql.= ' OR u.name LIKE \'%'.$db->escape(trim($seller)).'%\')';
		}
		if ($client)
		{
			$sql.= ' AND s.nom LIKE \'%'.$db->escape(trim($client)).'%\'';
		}
	
		if ($amount)
		{
			$sql.= ' AND f.total_ttc = \''.$db->escape(trim($amount)).'\'';
		}
	
		$sql.= ' GROUP BY f.rowid';
		
		$sql.= ' UNION SELECT ';
		
		$sql.= ' p.rowid as ticketid, p.ticketnumber, p.total_ttc,';
		$sql.= ' p.fk_user_close, p.date_creation as datec,';
		$sql.= ' p.fk_statut, p.fk_place, ';
		$sql.= ' s.nom, s.rowid as socid,';
		$sql.= ' u.firstname, u.name,';
		$sql.= ' t.name, p.fk_cash, p.type';
		
		$sql.= ' FROM '.MAIN_DB_PREFIX.'societe as s';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_ticket as p';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_cash as t';
		$sql.= ', '.MAIN_DB_PREFIX.'user as u';
		$sql.= ' WHERE p.fk_soc = s.rowid';
		$sql.= " AND p.entity = ".$conf->entity;
		$sql.= " AND p.fk_cash = t.rowid";
		$sql.= " AND p.fk_statut = 0";
		$sql.= " AND u.rowid = p.fk_user_author";
		if($stat >= 0 && $stat !=4 && $stat <= 99){
			$sql.= " AND p.fk_statut = ".$stat;
		}
		if($stat == 4){
			$sql.= " AND p.type = 1";
		}
			
		if ($socid) $sql.= ' AND s.rowid = '.$socid;
		
		if ($ticketnumber)
		{
			$sql.= ' AND p.ticketnumber LIKE \'%'.$db->escape(trim($ticketnumber)).'%\'';
		}
		if ($months > 0)
		{
			if ($years > 0)
				$sql.= " AND p.date_ticket BETWEEN '".$db->idate(dol_get_first_day($years,$months,false))."' AND '".$db->idate(dol_get_last_day($years,$months,false))."'";
			else
				$sql.= " AND date_format(p.date_ticket, '%m') = '".$months."'";
		}
		else if ($years > 0)
		{
			$sql.= " AND p.date_ticket BETWEEN '".$db->idate(dol_get_first_day($years,1,false))."' AND '".$db->idate(dol_get_last_day($years,12,false))."'";
		}
		$now = dol_now();
		$time = dol_getdate($now);
		$day = $time['mday'];
		$month = $time['mon'];
		$year = $time['year'];
		
		if($stat == 100)	{//Today
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 101)	{//Yesterday
			$time = dol_get_prev_day($day, $month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 102)	{//This week
			$time = dol_get_first_day_week($day,$month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['first_day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 103)	{//Last week
			$time = dol_get_first_day_week($day, $month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['prev_year'],$time['prev_month'],$time['prev_day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$time['first_day']-1==0?$time['prev_month']:$month,$time['first_day']-1==0?$time['prev_day']+6:$time['first_day']-1,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 104)	{//Two weeks ago
			$time = dol_get_prev_week($day,'', $month, $year);
			$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 105)	{//Three weeks ago
			$time = dol_get_prev_week($day,'', $month, $year);
			$time = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 106)	{//This month
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,01,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 107)	{//One month ago
			$time = dol_get_prev_month($month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$day,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if($stat == 108)	{//Last month
			$time = dol_get_prev_month($month, $year);
			$ini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],01,0,0,0);
			$fin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],31,0,0,0);
			$sql.= " AND p.date_ticket BETWEEN '".$ini."' AND '".$fin."'";
		}
		if ($terminal)
		{
			$sql.= ' AND t.name LIKE \'%'.$db->escape(trim($terminal)).'%\'';
		}
		if ($seller)
		{
			$sql.= ' AND (u.firstname LIKE \'%'.$db->escape(trim($seller)).'%\'';
			$sql.= ' OR u.name LIKE \'%'.$db->escape(trim($seller)).'%\')';
		}
		if ($client)
		{
			$sql.= ' AND s.nom LIKE \'%'.$db->escape(trim($client)).'%\'';
		}
		
		if ($amount)
		{
			$sql.= ' AND p.total_ttc = \''.$db->escape(trim($amount)).'\'';
		}
		
		$sql.= ' GROUP BY p.rowid';
	
		$sql.= ' ORDER BY ';
		$sql.= ' datec DESC ';
		$sql.= 'LIMIT 0,50';
	
		$res = $db->query($sql);
		 
		if ($res)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$ticketstatic=new Ticket($db);
			while ($i < $num)
			{
				$obj = $db->fetch_object($res);
	
				$tickets[$i]["id"] = $obj->ticketid;
				$tickets[$i]["type"] = ($obj->type==2?1:$obj->type);
				$tickets[$i]["ticketnumber"] = $obj->facnumber;
				$tickets[$i]["date_creation"] = dol_print_date($db->jdate($obj->datec),'dayhour');
				$tickets[$i]["date_close"] = dol_print_date($db->jdate($obj->date_closed),'dayhour');
				$tickets[$i]["fk_place"] = $obj->fk_place;
	
				$cash=new Cash($db);
				$cash->fetch($obj->fk_cash);
				$tickets[$i]["terminal"] = $cash->name;
	
				$userstatic=new User($db);
				$userstatic->fetch($obj->fk_user_valid);
				$tickets[$i]["seller"]=$userstatic->getFullName($langs);
	
				$tickets[$i]["client"] = $obj->nom;
				$tickets[$i]["amount"] = $obj->total_ttc;
				$tickets[$i]["customer_pay"] = $obj->customer_pay;
				$tickets[$i]["statut"] = $obj->fk_statut;
				$tickets[$i]["statutlabel"] = $ticketstatic->LibStatut($obj->fk_statut,0);
	
				$i++;
			}
			return ErrorControl($tickets,$function);
				
	
		}
		else
		{
			return ErrorControl($ret, $function);
		}
	
	}
	
	/**
	 *
	 * Count Ticket history
	 *
	 */
	public static function countHistoric()
	{
		global $db, $conf;
	
		$ret=-1;
		$function="GetHistoric";
	
		$sql = 'SELECT (SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
				
		$now = dol_now();
		$time = dol_getdate($now);
		$day = $time['mday'];
		$month = $time['mon'];
		$year = $time['year'];
	
		//Today
		$todayini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,0,0,0);
		$todayfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$todayini."' AND '".$todayfin."' ) as today, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
				
		//Yesterday
		$time = dol_get_prev_day($day, $month, $year);
		$yestini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],0,0,0);
		$yestfin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$yestini."' AND '".$yestfin."' ) as yesterday, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
		
		
		//This week
		$time = dol_get_first_day_week($day,$month, $year);
		$weekini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['first_day'],0,0,0);
		$weekfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$weekini."' AND '".$weekfin."' ) as thisweek, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
		
		
		//Last week
		$time = dol_get_first_day_week($day, $month, $year);
		$lweekini= sprintf("%04d%02d%02d%02d%02d%02d",$time['prev_year'],$time['prev_month'],$time['prev_day'],0,0,0);
		$lweekfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$time['first_day']-1==0?$time['prev_month']:$month,$time['first_day']-1==0?$time['prev_day']+6:$time['first_day']-1,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$lweekini."' AND '".$lweekfin."' ) as lastweek, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
		
		
		//Two weeks ago
		$time = dol_get_prev_week($day,'', $month, $year);
		$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
		$ini2week= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
		$fin2week= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$ini2week."' AND '".$fin2week."' ) as twoweek, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
		
		
		//Three weeks ago
		$time = dol_get_prev_week($day,'', $month, $year);
		$time = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
		$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
		$ini3week= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
		$fin3week= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$ini3week."' AND '".$fin3week."' ) as threeweek, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
		
		
		//This month
		$monthini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,01,0,0,0);
		$monthfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$monthini."' AND '".$monthfin."' ) as thismonth, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
				
		
		//One month ago
		$time = dol_get_prev_month($month, $year);
		$monthagoini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$day,0,0,0);
		$monthagofin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.date_ticket BETWEEN '".$monthagoini."' AND '".$monthagofin."' ) as monthago, ";
		
		$sql.= '(SELECT COUNT(rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		
				
		//Last month
		$time = dol_get_prev_month($month, $year);
		$lmonthini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],01,0,0,0);
		$lmonthfin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],31,0,0,0);
		$sql.= " AND f.date_ticket BETWEEN '".$lmonthini."' AND '".$lmonthfin."' ) as lastmonth";
		
		$res = $db->query($sql);
		 
		if ($res)
		{
			$obj = $db->fetch_object($res);
	
			$result["today"] = $obj->today;
			$result["yesterday"] = $obj->yesterday;
			$result["thisweek"] = $obj->thisweek;
			$result["lastweek"] = $obj->lastweek;
			$result["twoweek"] = $obj->twoweek;
			$result["threeweek"] = $obj->threeweek;
			$result["thismonth"] = $obj->thismonth;
			$result["monthago"] = $obj->monthago;
			$result["lastmonth"] = $obj->lastmonth;
				
			return ErrorControl($result,$function);
		}
		else
		{
			return ErrorControl($ret, $function);
		}
	
	}
	
	/**
	 *
	 * Count Facture history
	 *
	 */
	public static function countHistoricFac()
	{
		global $db, $conf;
	
		$ret=-1;
		$function="GetHistoric";
	
		$sql = 'SELECT (SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		$now = dol_now();
		$time = dol_getdate($now);
		$day = $time['mday'];
		$month = $time['mon'];
		$year = $time['year'];
	
		//Today
		$todayini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,0,0,0);
		$todayfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$todayini."' AND '".$todayfin."' ) as today, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		//Yesterday
		$time = dol_get_prev_day($day, $month, $year);
		$yestini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],0,0,0);
		$yestfin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['day'],23,59,59);
		$sql.= " AND f.datec BETWEEN '".$yestini."' AND '".$yestfin."' ) as yesterday, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
	
		//This week
		$time = dol_get_first_day_week($day,$month, $year);
		$weekini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$time['first_day'],0,0,0);
		$weekfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$weekini."' AND '".$weekfin."' ) as thisweek, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		//Last week
		$time = dol_get_first_day_week($day, $month, $year);
		$lweekini= sprintf("%04d%02d%02d%02d%02d%02d",$time['prev_year'],$time['prev_month'],$time['prev_day'],0,0,0);
		$lweekfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$time['first_day']-1==0?$time['prev_month']:$month,$time['first_day']-1==0?$time['prev_day']+6:$time['first_day']-1,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$lweekini."' AND '".$lweekfin."' ) as lastweek, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		//Two weeks ago
		$time = dol_get_prev_week($day,'', $month, $year);
		$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
		$ini2week= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
		$fin2week= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$ini2week."' AND '".$fin2week."' ) as twoweek, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		//Three weeks ago
		$time = dol_get_prev_week($day,'', $month, $year);
		$time = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
		$time2 = dol_get_prev_week($time['day'], '', $time['month'], $time['year']);
		$ini3week= sprintf("%04d%02d%02d%02d%02d%02d",$time2['year'],$time2['month'],$time2['day'],0,0,0);
		$fin3week= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['day']-1==0?$time2['month']:$time['month'],$time['day']-1==0?$time2['day']+6:$time['day']-1,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$ini3week."' AND '".$fin3week."' ) as threeweek, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		//This month
		$monthini= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,01,0,0,0);
		$monthfin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$monthini."' AND '".$monthfin."' ) as thismonth, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
	
		//One month ago
		$time = dol_get_prev_month($month, $year);
		$monthagoini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],$day,0,0,0);
		$monthagofin= sprintf("%04d%02d%02d%02d%02d%02d",$year,$month,$day,23,59,59);
		$sql.= " AND f.datec BETWEEN '".$monthagoini."' AND '".$monthagofin."' ) as monthago, ";
	
		$sql.= '(SELECT COUNT(f.rowid)';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_facture as pf';
		$sql.= ' WHERE f.entity = '.$conf->entity;
		$sql.= ' AND pf.fk_facture = f.rowid';
	
		//Last month
		$time = dol_get_prev_month($month, $year);
		$lmonthini= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],01,0,0,0);
		$lmonthfin= sprintf("%04d%02d%02d%02d%02d%02d",$time['year'],$time['month'],31,0,0,0);
		$sql.= " AND f.datec BETWEEN '".$lmonthini."' AND '".$lmonthfin."' ) as lastmonth";
	
		$res = $db->query($sql);
			
		if ($res)
		{
			$obj = $db->fetch_object($res);
	
			$result["today"] = $obj->today;
			$result["yesterday"] = $obj->yesterday;
			$result["thisweek"] = $obj->thisweek;
			$result["lastweek"] = $obj->lastweek;
			$result["twoweek"] = $obj->twoweek;
			$result["threeweek"] = $obj->threeweek;
			$result["thismonth"] = $obj->thismonth;
			$result["monthago"] = $obj->monthago;
			$result["lastmonth"] = $obj->lastmonth;
	
			return ErrorControl($result,$function);
		}
		else
		{
			return ErrorControl($ret, $function);
		}
	
	}
	
	/*public static function set_payment($idTicket, $idPayment)
	{
		global $db, $user;
		
		$object= new Ticket($db);
		
		$db->begin;
		
		$res = $object->validate($user);
		
		if ($res==1)
		{
			$terminal = new Cash($db);
			$terminal->fetch($_SESSION['TERMINAL_ID']);
		
			if ($idPayment==4)
				$fkaccount=$terminal->fk_paycash;
			else
				$fkaccount=$terminal->fk_paybank;
			
			$payment=new Paiement($db);
			$payment->datepaye=$now;
			$payment->bank_account=$fkaccount;
			$payment->amounts[$invoice->id]=$object>prix_total_ttc();
			$payment->note=$langs->trans("Payment").' '.$langs->trans("Ticket").' '.$object->ref();
			$payment->paiementid=$idPayment;
			$payment->num_paiement='';
			
			$paiement_id = $payment->create($user);
			if ($paiement_id > 0)
			{
				$res=$payment->addPaymentToBank($user,'payment','(CustomerTicketPayment)',$fkaccount,'','');
				if (! $result > 0) $error++;			                    
				if (! $error) $res=$object->set_paid($user);							                    
			}
			else 
			{
				$error++;
			}
		}
		else 
		{
			$error++;
		}
		
		
		if($error) 
		{
			$db->rollback;
			$error = self::ErrorControl($error,"SetPayment");
		}
		else $db->commit;
		
		$retarray[0] = $res;
		$retarray[1] = $error;
		return $retarray;
	}*/
	

	
	/**
	 * 
	 * Add ticket Payment
	 * 
	 * @param array $aryTicket	Ticket data array
	 */
	private function addPayment($aryTicket)
	{
		global $db, $langs;
		
		//require_once(DOL_DOCUMENT_ROOT.'/pos/backend/class/payment.class.php');
		$now=dol_now();
		$userstatic=new User($db);
		$userstatic->fetch($_SESSION['uid']); 
		
		if($aryTicket['type']==1)
		{
			$aryTicket['total']=$aryTicket['total']*-1;
			$aryTicket['customerpay']=$aryTicket['customerpay']*-1;
		}
		
		$cash = new Cash($db);
        	
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
        	
		if ($aryTicket['payment_type']==$cash->fk_modepaycash)
        {
        	$bankaccountid = $cash->fk_paycash;
        	
        	if($aryTicket['total']>$aryTicket['customerpay'])
        	{
        		$amount = $aryTicket['customerpay'];
        	}
        	else
        	{
        		$amount = $aryTicket['total'];
        	}
        	        	
        }
        else
        {
        	$bankaccountid = $cash->fk_paybank ;
        	$amount = $aryTicket['customerpay'];
        }

        $payment=new Payment($db);
		$payment->datepaye=$now;
		$payment->bank_account=$bankaccountid;
		$payment->amounts[$aryTicket['id']]=$amount;
		$payment->note=$langs->trans("Payment").' '.$langs->trans("Ticket").' '.$aryTicket['ref'] ;
		$payment->paiementid=$aryTicket['payment_type'];
		$payment->num_paiement='';

		$paiement_id = $payment->create($userstatic);
		if ($paiement_id > 0)
		{
			$result=$payment->addPaymentToBank($userstatic,'payment','(CustomerTicketPayment)',$bankaccountid,$aryTicket['customerId'],'','');
            if (! $result > 0)
			{
				return -1;
			}
			else 
			{
				return $paiement_id;
			}
		}
		else
		{
			return -1;
		}
	}
	
	/**
	 *
	 * Add facture Payment
	 *
	 * @param array $aryTicket	Ticket data array
	 */
	private function addPaymentFac($aryTicket)
	{
		global $db, $langs;
	
		require_once(DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php');
		$now=dol_now();
		$userstatic=new User($db);
		$userstatic->fetch($_SESSION['uid']);
	
		if($aryTicket['type']==1)
		{
			$aryTicket['total']=$aryTicket['total']*-1;
			$aryTicket['customerpay']=$aryTicket['customerpay']*-1;
		}
	
		$cash = new Cash($db);
		 
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
		 
		if ($aryTicket['payment_type']==$cash->fk_modepaycash)
		{
			$bankaccountid = $cash->fk_paycash;
			 
			if($aryTicket['total']>$aryTicket['customerpay'])
			{
				$amount = $aryTicket['customerpay'];
			}
			else
			{
				$amount = $aryTicket['total'];
			}
			if ($aryTicket['points'] > 0 && $aryTicket['difpayment'] < 0){
				$amount = $aryTicket['total_with_points'];
			}
		}
		else
		{
			$bankaccountid = $cash->fk_paybank ;
			$amount = $aryTicket['customerpay'];
		}
		//Añadir el posible pago de puntos
		if ($aryTicket['points'] > 0)
		{
			dol_include_once('/rewards/class/rewards.class.php');
			$reward = new Rewards($db);
			$facture = new Facture($db);
			$facture->fetch($aryTicket['id']);
			$res=$reward->usePoints($facture, $aryTicket['points']);
		}
		
		$payment=new Paiement($db);
		$payment->datepaye=$now;
		$payment->bank_account=$bankaccountid;
		$payment->amounts[$aryTicket['id']]=$amount;
		$payment->note=$langs->trans("Payment").' '.$langs->trans("Facture").' '.$aryTicket['ref'] ;
		$payment->paiementid=$aryTicket['payment_type'];
		$payment->num_paiement='';
	
		$paiement_id = $payment->create($userstatic);
		if ($paiement_id > 0)
		{
			$result=$payment->addPaymentToBank($userstatic,'payment','(CustomerFacturePayment)',$bankaccountid,$aryTicket['customerId'],'','');
			if (! $result > 0)
			{
				return -1;
			}
			else
			{
				return $paiement_id;
			}
		}
		else
		{
			return -1;
		}
	}
	
	private function quitSotck($lines,$isreturn=false)
	{
		global $db,$langs;
		require_once(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");
		
		$userstatic=new User($db);
		$userstatic->fetch($_SESSION['uid']); 
		
		$error=0;
		$cash = new Cash($db);	
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
		$warehouse=$cash->fk_warehouse;
		
		foreach ( $lines as $line )
		{
			if(sizeof($line)>0)
			{
				if($line['idProduct'])
				{
					$mouvP = new MouvementStock($db);
					// We decrease stock for product
					
					if(!$isreturn)
					{
						$result=$mouvP->livraison($userstatic, $line['idProduct'], $warehouse, $line['cant'], $line['price'], $langs->trans("TicketCreatedInDolibarr"));
					}
					else 
					{
						$result=$mouvP->reception($userstatic, $line['idProduct'], $warehouse, $line['cant'], $line['price'], $langs->trans("TicketCreatedInDolibarr"));
					}
					if ($result < 0) { $error++; }
				}
			
			}
		}
		return $error;	
	}
	
	/**
	 * 
	 * Get user POS
	 * 
	 * @return	array	User and terminal name
	 */
	public static function getLogin()
	{
		global $db, $conf;
		
		$error=0;
		$function="getLogin";
		
		$userstatic=new User($db);
		if ($userstatic->fetch($_SESSION['uid'])!=0) $error++;
			
		$cash = new Cash($db);
		$terminal = $_SESSION['TERMINAL_ID'];
		if ($cash->fetch($terminal)<0) $error++;

		if(!$error)
		{
			$ret['User'] = $userstatic->getFullName($langs);
			$ret['Terminal'] = $cash->name;
			return ErrorControl($ret, $function);
		}
		else 
		{
			$error=$error*-1;
			return ErrorControl($error,$function);
		}
	}
	
	/**
	 * 
	 * Create Customer into DB
	 * 
	 * @param		array 	$aryCustomer 	Customer object
	 * @return		array	$result		Result
	 */
	public static function SetCustomer($aryCustomer)
	{	
		require_once(DOL_DOCUMENT_ROOT ."/societe/class/societe.class.php");
		
		global $conf, $db;
		$function="SetCustomer";
		$res = -1 ;
		
		//We use code creation
        $module=$conf->global->SOCIETE_CODECLIENT_ADDON;
        //if (! $module) dolibarr_error('',$langs->trans("ErrorModuleThirdPartyCodeInCompanyModuleNotDefined"));
        if (substr($module, 0, 15) == 'mod_codeclient_' && substr($module, -3) == 'php')
        {
            $module = substr($module, 0, dol_strlen($module)-4);
        }
        require_once(DOL_DOCUMENT_ROOT ."/core/modules/societe/".$module.".php");
        $modCodeClient = new $module;
        if ($modCodeClient->code_auto) $tmpcode=$modCodeClient->getNextValue($object,0);
		
        
		$object = new Societe($db);
	
		$object->particulier = 1;
		$object->code_client = $tmpcode;
		$object->typent_id             	= 8; // TODO predict another method if the field "special" change of rowid
		$object->client                	= 1;
        $object->fournisseur           	= 0; 
		$object->tva_assuj = 1;
        $object->status= 1;
		
		$object->name                  	= empty($conf->global->MAIN_FIRSTNAME_NAME_POSITION)?trim($aryCustomer['prenom'].' '.$aryCustomer["nom"]):trim($aryCustomer["nom"].' '.$aryCustomer["prenom"]);
		$object->nom                   	= $object->name;     // TODO obsolete
		$object->nom_particulier       	= $aryCustomer["nom"];
		$object->prenom                	= $aryCustomer["prenom"];
		$object->siren                 	= $aryCustomer["idprof1"]; 
        $object->tel					= $aryCustomer["tel"] ;     
        $object->email					= $aryCustomer["email"] ; 
        
		$res=$object->create();

		return ErrorControl($res, $function);
		
	}
	
	/**
	 * 
	 * Create product into DB
	 * 
	 * @param		array 	$aryProduct 	Product object
	 * @return		array	$result		Result
	 */
	public static function SetProduct($aryProduct)
	{	
		require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
		
		global $conf, $db, $mysoc;
		
		$code_pays="'".$mysoc->pays_code."'";
		
		$function="SetProduct";
		$res = -1 ;

		$sql  = "SELECT DISTINCT t.taux";
        $sql.= " FROM ".MAIN_DB_PREFIX."c_tva as t, ".MAIN_DB_PREFIX."c_pays as p";
        $sql.= " WHERE t.fk_pays = p.rowid";
        $sql.= " AND t.active = 1";
        $sql.= " AND t.rowid = ". $aryProduct['tax'];
        $sql.= " AND p.code in (".$code_pays.")";
        $sql.= " ORDER BY t.taux DESC";

        $resql=$db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            if ($num)
            {
                for ($i = 0; $i < $num; $i++)
                {
                    $obj = $db->fetch_object($resql);
				
                }
            }         
        }
        
        $myproduct=new Product($db);

		$myproduct->ref                	= $aryProduct['ref'];
		$myproduct->libelle            	= $aryProduct['label'];
		$myproduct->price_ttc          	= $aryProduct['price_ttc'];
		$myproduct->price_base_type    	= 'TTC';
        $myproduct->tva_tx 				= $obj->taux;
		$myproduct->type            	= 0;
		$myproduct->status             	= 1;
			
		$userstatic=new User($db);
		$userstatic->fetch($_SESSION['uid']); 
		
		$res = $myproduct->create($userstatic);
		
		return ErrorControl($res,$function);
	}
	
	/**
	 * 
	 * Return the VAT list
	 * 
	 * @return		array		Applicable VAT
	 */
	public static function select_VAT()
	{
		global $db,$conf,$langs, $mysoc;
		
		$code_pays="'".$mysoc->pays_code."'";
		
		$sql  = "SELECT DISTINCT t.rowid, t.taux";
        $sql.= " FROM ".MAIN_DB_PREFIX."c_tva as t, ".MAIN_DB_PREFIX."c_pays as p";
        $sql.= " WHERE t.fk_pays = p.rowid";
        $sql.= " AND t.active = 1";
        $sql.= " AND p.code in (".$code_pays.")";
        $sql.= " ORDER BY t.taux DESC";

        $resql=$db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            if ($num)
            {
                for ($i = 0; $i < $num; $i++)
                {
                    $obj = $db->fetch_object($resql);
                    $vat[$i]['id']  = $obj->rowid;
                    $vat[$i]['label'] = $obj->taux.'%';
                }
            }         
        }
        
        return $vat;		
	}

	/**
	 * 
	 * Return the money in cash
	 * 
	 * @return		array		Applicable VAT
	 */
	public static function getMoneyCash($open=false)
	{
		global $db;
		
		$terminal = $_SESSION['TERMINAL_ID'];
		
		$cash = new ControlCash($db,$terminal);
	
		return $cash->getMoneyCash($open);
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $aryClose
	 */
	public static function setControlCash($aryClose)
	{
		global $db,$user;
		
		$function = "closeCash";
		$error=0;
		
		$terminalid = $_SESSION['TERMINAL_ID'];
		$userpos = new User($db);
		$userpos->fetch($aryClose['employeeId']);
		$userpos->getrights('pos');
		if($userpos->rights->pos->closecash || !$aryClose['type']){
		
		
			$cash = new ControlCash($db,$terminalid);
			
			//$aryClose['moneyincash'];
	  		//$aryClose['type'];
			
			$data['userid'] 		= $aryClose['employeeId'];
			$data['amount_reel'] 	= $aryClose['moneyincash'];
			$data['amount_teoric'] 	= $cash->getMoneyCash();
			$data['amount_diff'] 	= $data['amount_reel'] - $data['amount_teoric'];
			$data['type_control'] 	= $aryClose['type'];
			$data['print']			= $aryClose['print'];
	  				
			$res = $cash->create($data);
						
			if ($res>0) 
			{
				$terminal = new Cash($db);
				$userstatic=new User($db);
				$userstatic->fetch($id);
				$terminal->fetch($terminalid);
				
				if($aryClose['type']==1)
				{
					if(!$terminal->set_closed($userstatic)) 
						$error++;
				}
				elseif($aryClose['type']==2)
				{
					if (!$terminal->set_open($userstatic)) 
						$error++;
				}
			}
			else
			{
				$error++;
			}
		}
		else
		{
			$error = 2;
		} 
		/*if(!$error && $data['print'])
		{
			//TODO: Print
		}*/
		if ($error==0)
			$error=$res;
		else
			$error=$error*-1;
		
		return ErrorControl($error,$function);
		
	}
	
	/**
	 * 
	 * Return the Employee data
	 * 
	 * @param		int		$id		Employed ID
	 * 
	 * @return		array			Applicable VAT
	 */
	public static function getEmployee($id)
	{
		global $db,$conf,$langs;
		
		$userstatic=new User($db);
		$res = $userstatic->fetch($id);

		if($res==0)
		{
			
		}
		
	}
	
	/**
	 * 
	 * Return POS Config
	 * 
	 * @return	array		Array with config
	 */
	public static function getConfig()
	{
		global $db,$conf,$langs;
	
		$cash = new Cash($db);
        	
		$terminal = $_SESSION['TERMINAL_ID'];
		$cash->fetch($terminal);
		
		$userstatic=new User($db);
		$userstatic->fetch($_SESSION['uid']); 
		
		$soc = new Societe($db, $cash->fk_soc);
		$soc->fetch($cash->fk_soc);
		$name=$soc->name?$soc->name:$soc->nom;
		
		$ret['error']['value'] = 0;
		$ret['error']['desc'] = '';
		
		$ret['data']['terminal']['id'] = $cash->id;
		$ret['data']['terminal']['name'] = $cash->name;
		$ret['data']['terminal']['tactil'] = $cash->tactil;
		$ret['data']['terminal']['warehouse'] = $cash->fk_warehouse;
		$ret['data']['terminal']['barcode'] = $cash->barcode;
		$ret['data']['terminal']['mode_info']= 0;
		$ret['data']['terminal']['faclimit']= $conf->global->POS_MAX_TTC;
		
		$ret['data']['module']['places']= $conf->global->POS_PLACES;
		$ret['data']['module']['print']= $conf->global->POS_PRINT;
		$ret['data']['module']['mail']= $conf->global->POS_MAIL;
		$ret['data']['module']['points']= $conf->global->REWARDS_DISCOUNT;

		$ret['data']['user']['id'] = $userstatic->id;
		$ret['data']['user']['name'] = $userstatic->getFullName($langs);
		$dir=$conf->user->dir_output;
		if ($userstatic->photo) $file=get_exdir($userstatic->id,2).$userstatic->photo;
		if ($file && file_exists($dir."/".$file)){
		$ret['data']['user']['photo'] = DOL_URL_ROOT.'/viewimage.php?modulepart=userphoto&entity='.$userstatic->entity.'&file='.urlencode($file);
		}
		else{
			$ret['data']['user']['photo'] = DOL_URL_ROOT.'/theme/common/nophoto.jpg';
		}
		$ret['data']['customer']['id'] = $soc->id;
		$ret['data']['customer']['name'] = $name;
		$ret['data']['customer']['remise'] = $soc->remise_percent;
		$ret['data']['customer']['points'] = null;
		if($conf->global->REWARDS_POS && ! empty($conf->rewards->enabled)){
			$rew= new Rewards($db);
			$res = $rew->getCustomerReward($soc->id);
			if($res){
				$ret['data']['customer']['points'] = $rew->getCustomerPoints($soc->id);
			}
		}
		
		$ret['data']['decrange']['unit'] = $conf->global->MAIN_MAX_DECIMALS_UNIT;
		$ret['data']['decrange']['tot'] = $conf->global->MAIN_MAX_DECIMALS_TOT;
		$ret['data']['decrange']['maxshow'] = $conf->global->MAIN_MAX_DECIMALS_SHOWN;
		
		return $ret;
	}
	
	public static function testSource($aryTicket)
	{
		global $db,$conf;
		
		$data = $aryTicket['data'];
		$lines = $data['lines'];
     
        //Compare
        $i=0;
        foreach ( $lines as $line )
		{
			if(sizeof($line)>0)
			{
				if ($line['idProduct']>0)
	    		{
	    		 	//Returned products for Source ticket
					$sql  = "SELECT sum(td.qty) as qty from ".MAIN_DB_PREFIX."pos_ticketdet td";
					$sql .= " INNER JOIN ".MAIN_DB_PREFIX."pos_ticket t";
					$sql .= " WHERE td.fk_ticket = t.rowid" ;
					$sql .= " AND t.fk_ticket_source= ".$data['idsource'];
					$sql .= " AND td.fk_product = ".$line['idProduct'];
		
					$resql=$db->query($sql);
					
        			if ($resql)
        			{
        				//Compare quantity returned
            			if ($db->num_rows($resql))
            			{
               			 	$obj = $db->fetch_object($result);
               			 	if (abs($obj->qty) >= $line['cant'])
               			 	{
               			 		$prods_returns[$i] = $line['idProduct'];
               			 		$i++;
               			 	}
            			}
            			
            			/*
            			//Compare quantity selled
						else 
						{
							$sql = "SELECT td.qty from ".MAIN_DB_PREFIX."pos_ticketdet td";
							$sql .=" INNER JOIN ".MAIN_DB_PREFIX."pos_ticket t";
							$sql .=" WHERE td.fk_ticket = t.rowid" ;
							$sql .=" AND t.rowid= ".$data['idsource'];
							$sql .= " AND td.fk_product = ".$line['idProduct'];
		
							$resql=$db->query($sql);
        					if ($resql)
        					{
        						
        					//Compare quantity returned
            				if ($db->num_rows($resql))
            				{
               			 		$obj = $db->fetch_object($result);
               			 		if ($obj->qty<$line['cant'])
               			 		{
               			 			$prods_returns[$line['idProduct']]['id'] = $line['idProduct'];
               			 		}        
							}	
	                		    
       				 	}*/
					}
						
	    		}			
			}
		}	
        
        
        return $prods_returns;
	}
	
	public static function testSourceFac($aryTicket)
	{
		global $db,$conf;
	
		$data = $aryTicket['data'];
		$lines = $data['lines'];
		 
		//Compare
		$i=0;
		foreach ( $lines as $line )
		{
			if(sizeof($line)>0)
			{
				if ($line['idProduct']>0)
				{
					//Returned products for Source ticket
					$sql  = "SELECT sum(fd.qty) as qty from ".MAIN_DB_PREFIX."facturedet fd";
					$sql .= " INNER JOIN ".MAIN_DB_PREFIX."facture f";
					$sql .= " WHERE fd.fk_facture = f.rowid" ;
					$sql .= " AND f.fk_facture_source= ".$data['idsource'];
					$sql .= " AND fd.fk_product = ".$line['idProduct'];
	
					$resql=$db->query($sql);
						
					if ($resql)
					{
						//Compare quantity returned
						if ($db->num_rows($resql))
						{
							$obj = $db->fetch_object($resql);
							if (abs($obj->qty) >= $line['cant'])
							{
								$prods_returns[$i] = $line['idProduct'];
								$i++;
							}
						}
					}
	
				}
			}
		}
	
	
		return $prods_returns;
	}
	
	/**
	 * Return the places of the company
	 * 
	 * @return array		return <0 if KO; array of places
	 */
	public static function getPlaces()
	{
		global $db,$conf,$langs;
		
		$sql  = 'SELECT rowid,';
		$sql .= 'name, ';
		$sql .= 'description, ';
		$sql .= 'status, ';
		$sql .= 'fk_ticket ';
		$sql .= 'From '.MAIN_DB_PREFIX.'pos_places p';
		$sql .= ' WHERE p.entity ='.$conf->entity;
		
		$resql=$db->query($sql);
		
		if ($resql)
			
		{
			$places = array();
			$num = $db->num_rows($resql);
			$i=0;
			
			while($i < $num)
			{
				$obj = $db->fetch_object($resql);
				
				$places[$i]["id"]= $obj->rowid;
				$places[$i]["name"]= $obj->name;
				$places[$i]["description"]= $obj->description;
				$places[$i]["fk_ticket"]= $obj->fk_ticket;
				$places[$i]["status"]= $obj->status;
				
				$i++;			
			}
		}
		return $places;	
	}
	
	/**
	 * Fill the body of email's message with a ticket
	 * 
	 * @param int $id
	 * 
	 * @return string		String with ticket data
	 */
	public static function fillMailTicketBody($id)
	{
		global $db,$conf,$langs;
		
		$ticket= new Ticket($db);
		$res= $ticket->fetch($id);
		$mysoc = new Societe($db);
		$mysoc->fetch($ticket->socid);
		$userstatic=new User($db);
		$userstatic->fetch($ticket->user_close);
		
		$label=$ticket->ref;
		$facture = new Facture($db);
		if($ticket->fk_facture){
			$facture->fetch($ticket->fk_facture);
			$label=$facture->ref;
		}		
		
		$message = $conf->global->MAIN_INFO_SOCIETE_NOM." \n".$conf->global->MAIN_INFO_SOCIETE_ADRESSE." \n". $conf->global->MAIN_INFO_SOCIETE_CP.' '.$conf->global->MAIN_INFO_SOCIETE_VILLE." \n\n";
		//$label=$ticket->type==0?$langs->transnoentities("Ticket"):$langs->transnoentities("TicketCreditPrint");
		$message .= $label." \n".dol_print_date($ticket->date_closed,'dayhourtext')." \n";
		$message .= $langs->transnoentities("Vendor").': '.$userstatic->nom."\n";
		if(!empty($ticket->fk_place))
		{
			$place = new Place($db);
			$place->fetch($ticket->fk_place);
			$message .= $langs->trans("Place").': '.$place->name."\n";
		}
			
		$message .= "\n";
		$message .= $langs->transnoentities("Label")."\t\t\t\t\t\t". $langs->transnoentities("Qty")."/".$langs->transnoentities("Price")."\t\t".$langs->transnoentities("DiscountLineal")."\t\t".$langs->transnoentities("Total")."\n";
		$ticket->getLinesArray();
		if (! empty($ticket->lines))
		{
			$subtotal=0;
			foreach ($ticket->lines as $line)
			{
				$espacio = '';
				$totalline= $line->qty*$line->subprice;
				while(dol_strlen(dol_trunc($line->libelle,30).$espacio)<29){
					$espacio .="    \t";
				}
				$message .= dol_trunc($line->libelle,33).$espacio;
				$message .= "\t\t".$line->qty." * ".price($line->price)."\t\t".$line->remise_percent."%\t\t\t".price($totalline).' '.$langs->trans(currency_name($conf->currency))."\n";
				$subtotal+=$totalline;
			}
		}
		else
		{
			$message .= $langs->transnoentities("ErrNoArticles")."\n";
		}
		
		if($ticket->remise_percent>0)
		{
			$message .= "\n".$langs->transnoentities("Subtotal")."\t".price($subtotal)."\n";
			$message .= $langs->transnoentities("DiscountGlobal")."\t".$ticket->remise_percent."%\n";
		}
		$message .= "\n".$langs->transnoentities("TotalHT").":\t".price($ticket->total_ht)." ".$langs->trans(currency_name($conf->currency))."\n";
		$message .= $langs->transnoentities("TotalVAT").":\t".price($ticket->total_tva)." ".$langs->trans(currency_name($conf->currency))."\n";
		if($ticket->total_localtax1!=0){
			$message .= $langs->trans("TotalLT1ES").":\t".price($ticket->total_localtax1)." ".$langs->trans(currency_name($conf->currency))."\n";
		}
		if($ticket->total_localtax2!=0){
			$message .= $langs->trans("TotalLT2ES").":\t".price($ticket->total_localtax2)." ".$langs->trans(currency_name($conf->currency))."\n";
		}
		$message .= $langs->transnoentities("TotalTTC").":\t".price($ticket->total_ttc)." ".$langs->trans(currency_name($conf->currency))."\n";
		$message .= "\n\n";
			
		$terminal = new Cash($db);
		$terminal->fetch($ticket->fk_cash);
			
		if ($ticket->type==0)
		{
			$message .= $langs->transnoentities("Payment").":\t".$terminal->select_Paymentname($ticket->mode_reglement_id)."\n";
			if($ticket->mode_reglement_id==$terminal->fk_modepaycash)
			{
				$message .= $langs->transnoentities("CustomerPay").":\t".price($ticket->customer_pay)." ".$langs->trans(currency_name($conf->currency))."\n";
				$difpayment=$ticket->total_ttc - $ticket->customer_pay;
				if($difpayment<0)
					$message .= $langs->transnoentities("CustomerRet").":\t".price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."\n";
				else
					$message .= $langs->transnoentities("CustomerDeb").":\t".price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."\n";
			}
		}
		return $message;
	}
	
	/**
	 * Fill the body of email's message with a facture
	 *
	 * @param int $id
	 *
	 * @return string		String with ticket data
	 */
	public static function fillMailFactureBody($id)
	{
		global $db,$conf,$langs;
		$langs->Load("rewards@rewards");
	
		$facture= new Facture($db);
		$res= $facture->fetch($id);
		$mysoc = new Societe($db);
		$mysoc->fetch($facture->socid);
		$userstatic=new User($db);
		$userstatic->fetch($facture->user_valid);
	
		$label=$facture->ref;
					
		$message = $conf->global->MAIN_INFO_SOCIETE_NOM." \n".$conf->global->MAIN_INFO_SOCIETE_ADRESSE." \n". $conf->global->MAIN_INFO_SOCIETE_CP.' '.$conf->global->MAIN_INFO_SOCIETE_VILLE." \n\n";
		$message .= $label." \n".dol_print_date($facture->date_valid,'dayhourtext')." \n";
		$message .= $langs->transnoentities("Vendor").': '.$userstatic->nom."\n";
		
		$sql = "SELECT fk_place,fk_cash FROM ".MAIN_DB_PREFIX."pos_facture WHERE fk_facture =".$facture->id;
		$result=$db->query($sql);
		
		if ($result)
		{
			$objp = $db->fetch_object($result);
			$place = new Place($db);
			$place->fetch($objp->fk_place);
			$message .= $langs->trans("Place").': '.$place->name."\n";
		}
		
		$message .= "\n";
		$message .= $langs->transnoentities("Label")."\t\t\t\t\t\t". $langs->transnoentities("Qty")."/".$langs->transnoentities("Price")."\t\t".$langs->transnoentities("DiscountLineal")."\t\t".$langs->transnoentities("Total")."\n";
		$facture->getLinesArray();
		if (! empty($facture->lines))
		{
			$subtotal=0;
			foreach ($facture->lines as $line)
			{
				$espacio = '';
				$totalline= $line->qty*$line->subprice;
				while(dol_strlen(dol_trunc($line->libelle,30).$espacio)<29){
					$espacio .="    \t";
				}
				$message .= dol_trunc($line->libelle,33).$espacio;
				$message .= "\t\t".$line->qty." * ".price($line->price)."\t\t".$line->remise_percent."%\t\t\t".price($totalline).' '.$langs->trans(currency_name($conf->currency))."\n";
				$subtotal+=$totalline;
			}
		}
		else
		{
			$message .= $langs->transnoentities("ErrNoArticles")."\n";
		}
	
		/*if($facture->remise_percent>0)
		{
			$message .= "\n".$langs->transnoentities("Subtotal")."\t".price($subtotal)."\n";
			$message .= $langs->transnoentities("DiscountGlobal")."\t".$facture->remise_percent."%\n";
		}*/
		$message .= "\n".$langs->transnoentities("TotalHT").":\t".price($facture->total_ht)." ".$langs->trans(currency_name($conf->currency))."\n";
		$message .= $langs->transnoentities("TotalVAT").":\t".price($facture->total_tva)." ".$langs->trans(currency_name($conf->currency))."\n";
		if($facture->total_localtax1!=0){
			$message .= $langs->trans("TotalLT1ES").":\t".price($facture->total_localtax1)." ".$langs->trans(currency_name($conf->currency))."\n";
		}
		if($facture->total_localtax2!=0){
			$message .= $langs->trans("TotalLT2ES").":\t".price($facture->total_localtax2)." ".$langs->trans(currency_name($conf->currency))."\n";
		}
		$message .= $langs->transnoentities("TotalTTC").":\t".price($facture->total_ttc)." ".$langs->trans(currency_name($conf->currency))."\n";
		$message .= "\n\n";
			
		$terminal = new Cash($db);
		$terminal->fetch($objp->fk_cash);
		if (! empty($conf->rewards->enabled)){
			$rewards = new Rewards($db);
			$points = $rewards->getInvoicePoints($facture->id);
		}
		$message .= $langs->transnoentities("Payment").":\t".$terminal->select_Paymentname($facture->mode_reglement_id)."\n";
		if ($facture->type==0)
		{
			$sql = "SELECT SUM(amount) as amount from ".MAIN_DB_PREFIX."paiement_facture WHERE  fk_facture=".$facture->id;
			$res=$db->query($sql);
			$objp=$db->fetch_object($res);
			$pay = $objp->amount;
			if (! empty($conf->rewards->enabled)){
				$usepoints= abs($rewards->getInvoicePoints($facture->id,1));
				$moneypoints = abs($usepoints*$conf->global->REWARDS_DISCOUNT);
				$message.= $langs->trans("CustomerPay").":\t".price($pay-$moneypoints)." ".$langs->trans(currency_name($conf->currency))."\n";
			}
			else{
				$message.= $langs->trans("CustomerPay").":\t".price($pay)." ".$langs->trans(currency_name($conf->currency))."\n";
			}
			$difpayment=$facture->total_ttc - $pay;
			if (! empty($conf->rewards->enabled)){
				if ($moneypoints>0){
					$message.= $langs->transnoentities("RewardsDiscountDesc",$usepoints).":\t".price($moneypoints)." ".$langs->trans(currency_name($conf->currency))."\n";
				}
			}
			if($difpayment<0){
				$message.= $langs->trans("CustomerRet").":\t".price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."\n";
			}else{
				$message.= $langs->trans("CustomerDeb").":\t".price(abs($difpayment))." ".$langs->trans(currency_name($conf->currency))."\n";
			}
		}
		if ($points != 0 && ! empty($conf->rewards->enabled))
		{
			$message.= $langs->trans("TotalPointsInvoice").":\t".price($points)." ".$langs->trans('Points')."\n";
			$total_points = $rewards->getCustomerPoints($facture->socid);
			$now = dol_now();
			$message.= $langs->trans("TotalDispoPoints").":\t".dol_print_date($now,'day')." \t".price($total_points)." ".$langs->trans('Points')."\n";
		}
		return $message;
	}
	
	/**
	 * Fill the body of email's message with a close cash
	 *
	 * @param int $id
	 *
	 * @return string		String with ticket data
	 */
	public static function FillMailCloseCashBody($id,$terminal)
	{
		global $db,$conf,$langs;
		
		$sql = "select fk_user, date_c";
		$sql .=" from ".MAIN_DB_PREFIX."pos_control_cash";
		$sql .=" where rowid = ".$id." and fk_cash=".$terminal;
		$result=$db->query($sql);
		
		if ($result)
		{
				
			$objp = $db->fetch_object($result);
			$date = $objp->date_c;
			$fk_user = $objp->fk_user;
			 
		}
		
		$message = $conf->global->MAIN_INFO_SOCIETE_NOM." \n".$conf->global->MAIN_INFO_SOCIETE_ADRESSE." \n". $conf->global->MAIN_INFO_SOCIETE_CP.' '.$conf->global->MAIN_INFO_SOCIETE_VILLE." \n\n";
		$message .= $langs->transnoentities("CloseCashReport").': '.$id."\n";
		$cash = new Cash($db);
		$cash->fetch($terminal);
		$message .= $langs->transnoentities("Terminal").': '.$cash->name."\n";
		
		$userstatic=new User($db);
		$userstatic->fetch($fk_user);
		$message .= $langs->transnoentities("User").': '.$userstatic->nom."\n";
		$message .= dol_print_date($db->jdate($date),'dayhourtext')."\n\n";
		
		$message .= $langs->transnoentities("TicketsCash")."\n";
		$message .= $langs->transnoentities("Ticket")."\t\t\t\t\t". $langs->transnoentities("Total")."\n";
		
		$sql = "select ticketnumber, total_ttc, type";
		$sql .=" from ".MAIN_DB_PREFIX."pos_ticket";
		$sql .=" where fk_control = ".$id." and fk_cash=".$terminal." and fk_mode_reglement=".$cash->fk_modepaycash;
		
		$sql .= " union select f.facnumber, f.total_ttc, f.type";
		$sql .=" from ".MAIN_DB_PREFIX."pos_facture as pf,".MAIN_DB_PREFIX."facture as f ";
		$sql .=" where pf.fk_control_cash = ".$id." and pf.fk_cash=".$terminal." and f.fk_mode_reglement=".$cash->fk_modepaycash. " and pf.fk_facture = f.rowid and f.fk_statut > 0";
		 
		$result=$db->query($sql);
		
		if ($result)
		{
			$num = $db->num_rows($result);
			if($num>0)
			{
				$i = 0;
				$subtotalcash=0;
				while ($i < $num)
				{
					$objp = $db->fetch_object($result);
					if($objp->type == 1)$objp->total_ttc= $objp->total_ttc * -1;
					$message .= $objp->ticketnumber."\t\t\t".price($objp->total_ttc)."\n";
					$i++;
					$subtotalcash+=$objp->total_ttc;
				}
			}
			else
			{
				$message .= $langs->transnoentities("NoTickets")."\n";
			}
		}
		
		$message .= $langs->transnoentities("TotalCash")."\t\t\t"." ".price($subtotalcash)." ".$langs->trans(currency_name($conf->currency))."\n\n";
		
		$message .= $langs->transnoentities("TicketsCreditCard")."\n";
		$message .= $langs->transnoentities("Ticket")."\t\t\t\t\t". $langs->transnoentities("Total")."\n";
		
		$sql = "select ticketnumber, total_ttc, type";
		$sql .=" from ".MAIN_DB_PREFIX."pos_ticket";
		$sql .=" where fk_control = ".$id." and fk_cash=".$terminal." and fk_mode_reglement=".$cash->fk_modepaybank;
		
		$sql .= " union select f.facnumber, f.total_ttc, f.type";
		$sql .=" from ".MAIN_DB_PREFIX."pos_facture as pf,".MAIN_DB_PREFIX."facture as f ";
		$sql .=" where pf.fk_control_cash = ".$id." and pf.fk_cash=".$terminal." and f.fk_mode_reglement=".$cash->fk_modepaybank. " and pf.fk_facture = f.rowid and f.fk_statut > 0";
		$result=$db->query($sql);
		
		if ($result)
		{
			$num = $db->num_rows($result);
			if($num>0)
			{
				$i = 0;
				$subtotalcard=0;
				while ($i < $num)
				{
					$objp = $db->fetch_object($result);
					if($objp->type == 1)$objp->total_ttc= $objp->total_ttc * -1;
					$message .= $objp->ticketnumber."\t\t\t".price($objp->total_ttc)."\n";
					$i++;
					$subtotalcard+=$objp->total_ttc;
				}
			}
			else
			{
				$message .= $langs->transnoentities("NoTickets")."\n";
			}
		}
		
		$message .= $langs->transnoentities("TotalCard")."\t\t\t"."  ".price($subtotalcard)." ".$langs->trans(currency_name($conf->currency))."\n\n";
		$message .= $langs->transnoentities("TotalPOS")."\t\t\t".price($subtotalcard+$subtotalcash)." ".$langs->trans(currency_name($conf->currency))."\n";
		
		return $message;
	}	
	
	
	/**
	 * Send mail with ticket data
	 * @param  $email
	 * @return int 			<0 if KO; >0 if OK
	 */
	public static function sendMail($email)
	{
		global $db,$conf,$langs;
	
				
		require_once(DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php');
		if($email["idTicket"])
		{
			$ticket= new Ticket($db);
			$ticket->fetch($email["idTicket"]);
			$subject= $conf->global->MAIN_INFO_SOCIETE_NOM.': '.$langs->trans("CopyOfTicket").' '.$ticket->ticketnumber;
			$message = self::FillMailTicketBody($ticket->id);
		}
		if($email["idFacture"])
		{
			$facture= new Facture($db);
			$facture->fetch($email["idFacture"]);
			$subject= $conf->global->MAIN_INFO_SOCIETE_NOM.': '.$langs->trans("CopyOfFacture").' '.$facture->ref;
			$message = self::FillMailFactureBody($facture->id);
		}
		if($email["idCloseCash"])
		{
			$subject= $conf->global->MAIN_INFO_SOCIETE_NOM.': '.$langs->trans("CopyOfCloseCash").' '.$email["idCloseCash"];
			$message = self::FillMailCloseCashBody($email["idCloseCash"],$email["idTerminal"]);
		}
		$from = $conf->global->MAIN_INFO_SOCIETE_NOM."<".$conf->global->MAIN_INFO_SOCIETE_MAIL.">";
			
			
			
		$mailfile = new CMailFile($subject,$email["mail_to"],$from,$message);
		if ($mailfile->error)
		{
			$mesg='<div class="error">'.$mailfile->error.'</div>';
			$res = -1;
		}
		else
		{
			$result=$mailfile->sendfile();
			return $result;
		}
					
		return $res;
	}
	
	/**
	 *	Delete ticket
	 *	@param     	int		$idTicket    Id of ticket to delete
	 *	@return		int					<0 if KO, >0 if OK
	 */
	public static function Delete_Ticket($idTicket)
	{
		global $db;
	
		$object= new Ticket($db);
		$object->fetch($idTicket);
		$db->begin;
		$res=$object->delete_ticket();
	
		if ($res)
		{
			$db->commit();
		}
		else
		{
			$db->rollback();
		}
	
		return $res;
	}	
	public static function calculePrice($product)
	{
		global $db;
		require_once (DOL_DOCUMENT_ROOT."/core/lib/price.lib.php");
		$qty = $product["cant"];
		if($product["price_base_type"] == "HT"){
			$pu = $product["price"];
		}
		else{
			$pu = $product["price_ttc"];
		}
		$remise_percent_ligne = $product["discount"]?$product["discount"]:0;
		$txtva = $product["tva_tx"];
		$uselocaltax1_rate = $product["localtax1_tx"]?$product["localtax1_tx"]:0;
		$uselocaltax2_rate = $product["localtax2_tx"]?$product["localtax2_tx"]:0;
		$remise_percent_global = $product["remise_percent_global"]?$product["remise_percent_global"]:0;
		$price_base_type = $product["price_base_type"];
		$type = $product["fk_product_type"]?$product["fk_product_type"]:0;
		$info_bits = 0;
		$remise_percent_ligne = $remise_percent_global + $remise_percent_ligne;
		$remise_percent_global = 0;
		$tabprice = calcul_price_total($qty, $pu, $remise_percent_ligne, $txtva, $uselocaltax1_rate, $uselocaltax2_rate, $remise_percent_global, $price_base_type, $info_bits, $type);
		//$tabprice = calcul_price_total($qty, 126.2, 0, 21, 5.2, 0, $remise_percent_global, "TTC", $info_bits, $type);
		$result["total_ht"]  = $tabprice[0];
		$result["total_tva"] = $tabprice[1];
		$result["total_ttc"] = $tabprice[2];
		$result["total_localtax1"] = $tabprice[9];
		$result["total_localtax2"] = $tabprice[10];
		$result["pu_ht"]  = $tabprice[3];
		$result["pu_tva"] = $tabprice[4];
		$result["pu_ttc"] = $tabprice[5];
		$result["total_ttc_without_discount"] = $tabprice[8];
		return $result;
	}
	public static function getLocalTax($data)
	{
		global $db;
		require_once (DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php");
		$customer = new Societe($db);
		$customer->fetch($data["customer"]);
		$localtax['1'] = get_localtax($data["tva"], 1,$customer);
		$localtax['2'] = get_localtax($data["tva"], 2,$customer);
		return $localtax;
	}
	
	public static function getNotes($mode)
	{
		global $db, $conf;
		
		$ret=-1;
		$function="GetNotes";
		if($mode){
			$sql = 'SELECT f.rowid as ticketid, f.ticketnumber, fd.description, f.note as ticketNote, fd.note as lineNote';
		}
		else{
			$sql = 'SELECT count(*)';
		}
		$sql.= ' FROM '.MAIN_DB_PREFIX.'pos_ticket as f';
		$sql.= ', '.MAIN_DB_PREFIX.'pos_ticketdet as fd';
		$sql.= ' WHERE f.fk_statut = 0';
		$sql.= ' AND f.rowid = fd.fk_ticket';
		$sql.= ' AND (f.note is not null';
		$sql.= ' OR fd.note is not null)';
		if($mode == 0){
			$sql .= 'GROUP BY f.ticketnumber';
		}
		
		$res = $db->query($sql);
		if($res)
		{
			$num = $db->num_rows($res);
			if($mode){
				$i = 0;
				$j = 0;
				$id=0; 
				while ($i < $num)
				{
					$obj = $db->fetch_object($res);
				
					if($id != $obj->ticketid){
						$id = $obj->ticketid;
						$tickets[$j]["id"] = $j;
						$tickets[$j]["ticketid"] = $obj->ticketid;
						$tickets[$j]["ticketnumber"] = $obj->ticketnumber;
						$tickets[$j]["description"] = '';
						$tickets[$j]["note"] = $obj->ticketNote?$obj->ticketNote:'';
						$j++;
					}
					if($obj->lineNote){
						$tickets[$j]["id"] = $j;
						$tickets[$j]["ticketid"] = $obj->ticketid;
						$tickets[$j]["ticketnumber"] = '';
						$tickets[$j]["description"] = $obj->description;
						$tickets[$j]["note"] = $obj->lineNote;
						$j++;
					}
					
					$i++;
				}
				return $tickets;
			}
			else {
				return $num;
			}	
			
		}
		else
		{
			return ErrorControl($ret, $function);
		}
	}
	 /**
	 *  Return list of all warehouses
	 *
	 *	@param	int		$status		Status
	 * 	@return array				Array list of warehouses
	 */
	function getWarehouse($status=1)
	{
		global $db;
		$liste = array();

		$sql = "SELECT rowid, lieu";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot";
		$sql.= " WHERE entity IN (".getEntity('warehouse', 1).")";
		$sql.= " AND statut = ".$status;

		$result = $db->query($sql);
		$i = 0;
		$num = $db->num_rows($result);
		if ( $result )
		{
			while ($i < $num)
			{
				$row = $db->fetch_row($result);
				$liste[$i]["id"] = $row[0];
				$liste[$i]["lieu"] = $row[1];
				$i++;
			}
			$db->free($result);
		}
		return $liste;
	}
	
	/**
	 * 	Reconstruit l'arborescence des categories sous la forme d'un tableau
	 *	Renvoi un tableau de tableau('id','id_mere',...) trie selon arbre et avec:
	 *				id = id de la categorie
	 *				id_mere = id de la categorie mere
	 *				id_children = tableau des id enfant
	 *				label = nom de la categorie
	 *				fulllabel = nom avec chemin complet de la categorie
	 *				fullpath = chemin complet compose des id
	 *
	 *	@param      string	$type		      Type of categories (0=product, 1=suppliers, 2=customers, 3=members)
	 *  @param      int		$markafterid      Mark all categories after this leaf in category tree.
	 *	@return		array		      		  Array of categories
	 */
	function get_full_arbo($type)
	{
		global $db,$conf;
		
		$categorie = new Categorie($db);
		
		$categorie->cats = array();
	
		// Init $this->cats array
		$sql = "SELECT DISTINCT c.rowid, c.label, c.description, c.fk_parent";	// Distinct reduce pb with old tables with duplicates
		$sql.= " FROM ".MAIN_DB_PREFIX."categorie as c";
		$sql.= " WHERE c.entity IN (".getEntity('category',1).")";
		$sql.= " AND c.type = ".$type;
		$sql.= " AND fk_parent = 0";
			
		dol_syslog(get_class($categorie)."::get_full_arbo get category list sql=".$sql, LOG_DEBUG);
		$resql = $db->query($sql);
		if ($resql)
		{
			$i=0;
			while ($obj = $db->fetch_object($resql))
			{
				$categorie->cats[$obj->rowid]['rowid'] = $obj->rowid;
				$categorie->cats[$obj->rowid]['id'] = $obj->rowid;
				$categorie->cats[$obj->rowid]['fk_parent'] = $obj->fk_parent;
				$categorie->cats[$obj->rowid]['label'] = $obj->label;
				$categorie->cats[$obj->rowid]['description'] = $obj->description;
				$i++;
			}
		}
		else
		{
			dol_print_error($db);
			return -1;
		}
	
		// We add the fullpath property to each elements of first level (no parent exists)
		dol_syslog(get_class($categorie)."::get_full_arbo call to build_path_from_id_categ", LOG_DEBUG);
		foreach($categorie->cats as $key => $val)
		{
			$categorie->build_path_from_id_categ($key,0);	// Process a branch from the root category key (this category has no parent)
		}
	
		dol_syslog(get_class($categorie)."::get_full_arbo dol_sort_array", LOG_DEBUG);
		$categorie->cats=dol_sort_array($categorie->cats, 'fulllabel', 'asc', true, false);
	
		//$this->debug_cats();
	
		return $categorie->cats;
	}
	
	/**
	 * Retourne les filles de la categorie
	 *
	 * @return	void
	 */
	/*function get_filles($idCat)
	{
		global $db,$conf;
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."categorie";
		$sql.= " WHERE fk_parent = ".$idCat;
				
		$res  = $db->query($sql);
	
		if ($res)
		{
			$cats = array ();
			while ($rec = $db->fetch_array($res))
			{
				$cat = new Categorie($db);
				$cat->fetch($rec['rowid']);
				$cats[] = $cat;
			}
			return $cats;
		}
		else
		{
			dol_print_error($db);
			return -1;
		}
	}*/
	/**
	 * 	Return list of contents of a category
	 *
	 * 	@param	string	$field				Field name for select in table. Full field name will be fk_field.
	 * 	@param	string	$classname			PHP Class of object to store entity
	 * 	@param	string	$category_table		Table name for select in table. Full table name will be PREFIX_categorie_table.
	 *	@param	string	$object_table		Table name for select in table. Full table name will be PREFIX_table.
	 *	@return	void
	 */
	function get_prod($idCat,$more)
	{
		global $db,$conf;
		$objs = array();
		
			
		$sql = "SELECT o.rowid as id, o.ref, o.label, o.description, c.fk_product,";
		$sql .=" o.fk_product_type";
		$sql.= " FROM ".MAIN_DB_PREFIX."categorie_product as c";
		$sql.= ", ".MAIN_DB_PREFIX."product as o";
		$sql.= " WHERE o.entity IN (".getEntity("product", 1).")";
		$sql.= " AND c.fk_categorie = ".$idCat;
		$sql.= " AND c.fk_product = o.rowid";
		$sql.= " AND o.tosell = 1";
		if($more >= 0)
			$sql.=" LIMIT ".$more.",10 ";
		
		$resql = $db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			
			while ($i < $num)
			{
				$objp = $db->fetch_object($resql);
			
				$objs[$objp->id]["id"] = $objp->id;
				$objs[$objp->id]["ref"] = $objp->ref;
				$objs[$objp->id]["label"] = $objp->label;
				$objs[$objp->id]["description"] = $objp->description;
				$objs[$objp->id]["type"] = $objp->fk_product_type;
				
				$objs[$objp->id]["image"] = self::getImageProduct($objp->id, false);
				$objs[$objp->id]["thumb"] = self::getImageProduct($objp->id, true);
				$i++;
			}
			return $objs;
		}
		else
		{
			return -1;
		}
	}
}
?>