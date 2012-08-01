<?php
/*   Copyright (C) 2012 Alexis José Turruella Sánchez
     Desarrollado en el mes de enero de 2012
     Correo electrónico: alexturruella@gmail.com
     Módulo que permite obtener los mejores 10 clientes, producto y facturas del mes año y un rango de fechas
	 Fichero topten.class.php
 */

class TOPTEN
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='skeleton';			//!< Id that identify managed objects
	//var $table_element='skeleton';	//!< Name of table without prefix where object is stored

   var $maximo;

	/**
     *	Constructor
     *
     * 	@param	DoliDB	$db		Database handler
     * 	@param	int		$max	Max number
     */
	function __construct($db,$max=10)
    {
        $this->db = $db;
		$this->maximo=$max;
        return 1;
    }
    /*
	si es anual $datos[0]=$ano
	si es mensual $datps[0]=$ano y $datos[1]=$mes
	si es entre fechas $datos[0]=$fechainicio y $datos[1]=$fechafin
	*/
	function TTClienteDinero($tipotopten,$datos)
	{
	switch($tipotopten)
	{
		case "ttanual":
		{
			$sqlfiltro .= " AND YEAR(f.datef)='".$datos[0]."' ";
			break;
		}
		case "ttmensual":
		{
			$sqlfiltro .= " AND MONTH(f.datef)='".$datos[0]."' AND YEAR(f.datef)='".$datos[1]."'";
			break;
		}
		case "ttentrefecha":
		{
			$sqlfiltro .= " AND (f.datef)>='".$datos[0]."' AND (f.datef)<='".$datos[1]."'";
			break;
		}
	}
	    $topclientes = array();
        $sql = "SELECT s.rowid,SUM(f.total_ttc) as total_gastado";
        $sql .= " FROM ".MAIN_DB_PREFIX."societe as s ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facture as f ON s.rowid=f.fk_soc ";

        $sql .= " where f.fk_statut = '2' AND f.paye = '1'";
		$sql .= $sqlfiltro;
		$sql .= " Group By (s.rowid)";
		//$sql .= " order by (total_gastado) desc";
        $sql.= $this->db->order("total_gastado","DESC");
        $sql.= $this->db->plimit($this->maximo,0);

        $resql=$this->db->query($sql);
        if ($resql)
        {
			include_once(DOL_DOCUMENT_ROOT."/societe/class/client.class.php");
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
			    $objc = $this->db->fetch_object($resql);
			    $cliente=new Client($this->db);
				$cliente->fetch($objc->rowid);

                $topclientes[$i][0]=$objc;
				$topclientes[$i][1]=$cliente;
                $i++;
            }

        }
		//$this->db->free();
        return $topclientes;
	}
	 /*
	si es anual $datos[0]=$ano
	si es mensual $datps[0]=$ano y $datos[1]=$mes
	si es entre fechas $datos[0]=$fechainicio y $datos[1]=$fechafin
	*/
	function TTClienteFactura($tipotopten,$datos)
	{
	switch($tipotopten)
	{
		case "ttanual":
		{
			$sqlfiltro .= " AND YEAR(f.datef)='".$datos[0]."' ";
			break;
		}
		case "ttmensual":
		{
			$sqlfiltro .= " AND MONTH(f.datef)='".$datos[0]."' AND YEAR(f.datef)='".$datos[1]."'";
			break;
		}
		case "ttentrefecha":
		{
			$sqlfiltro .= " AND (f.datef)>='".$datos[0]."' AND (f.datef)<='".$datos[1]."'";
			break;
		}
	}
	    $topclientes = array();
        $sql = "SELECT s.rowid,count(f.rowid) as cantidad_facturas";
        $sql .= " FROM ".MAIN_DB_PREFIX."societe as s ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facture as f ON s.rowid=f.fk_soc ";

        $sql .= " where f.fk_statut = '2' AND f.paye = '1' ";
		$sql .= $sqlfiltro;
		$sql .= " Group By (s.rowid)";
		//$sql .= " order by (cantidad_facturas) desc";
        $sql.= $this->db->order("cantidad_facturas","DESC");
        $sql.= $this->db->plimit($this->maximo,0);

        $resql=$this->db->query($sql);
        if ($resql)
        {
			include_once(DOL_DOCUMENT_ROOT."/societe/class/client.class.php");
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
			    $objc = $this->db->fetch_object($resql);
			    $cliente=new Client($this->db);
				$cliente->fetch($objc->rowid);

                $topclientes[$i][0]=$objc;
				$topclientes[$i][1]=$cliente;
                $i++;
            }

        }
		//$this->db->free();
        return $topclientes;
	}
    function TTProductoDinero($tipotopten,$datos)
	{
	switch($tipotopten)
	{
		case "ttanual":
		{
			$sqlfiltro .= " AND YEAR(f.datef)='".$datos[0]."' ";
			break;
		}
		case "ttmensual":
		{
			$sqlfiltro .= " AND MONTH(f.datef)='".$datos[0]."' AND YEAR(f.datef)='".$datos[1]."'";
			break;
		}
		case "ttentrefecha":
		{
			$sqlfiltro .= " AND (f.datef)>='".$datos[0]."' AND (f.datef)<='".$datos[1]."'";
			break;
		}
	}
	    $topproductos = array();
        $sql = "SELECT p.rowid,SUM(pf.total_ht) as dinero_recaudado";
        $sql .= " FROM ".MAIN_DB_PREFIX."product as p ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facturedet as pf ON p.rowid=pf.fk_product ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facture as f ON f.rowid=pf.fk_facture ";
        $sql .= " where f.fk_statut = '2' AND f.paye = '1' ";
		$sql .= $sqlfiltro;
		$sql .= " Group By (p.rowid)";
		//$sql .= " order by (dinero_recaudado) desc";
        $sql.= $this->db->order("dinero_recaudado","DESC");
        $sql.= $this->db->plimit($this->maximo,0);

        $resql=$this->db->query($sql);
        if ($resql)
        {
			require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
			    $objp = $this->db->fetch_object($resql);
			    $producto=new Product($this->db);
				$producto->fetch($objp->rowid);

                $topproductos[$i][0]=$objp;
				$topproductos[$i][1]=$producto;
                $i++;
            }

        }
		//$this->db->free();
        return $topproductos;
	}
	function TTProductoCantidadVendida($tipotopten,$datos)
	{
	switch($tipotopten)
	{
		case "ttanual":
		{
			$sqlfiltro .= " AND YEAR(f.datef)='".$datos[0]."' ";
			break;
		}
		case "ttmensual":
		{
			$sqlfiltro .= " AND MONTH(f.datef)='".$datos[0]."' AND YEAR(f.datef)='".$datos[1]."'";
			break;
		}
		case "ttentrefecha":
		{
			$sqlfiltro .= " AND (f.datef)>='".$datos[0]."' AND (f.datef)<='".$datos[1]."'";
			break;
		}
	}
	    $topproductos = array();
        $sql = "SELECT p.rowid,SUM(pf.qty) as cantidad_vendida";
        $sql .= " FROM ".MAIN_DB_PREFIX."product as p ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facturedet as pf ON p.rowid=pf.fk_product ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facture as f ON f.rowid=pf.fk_facture ";
        $sql .= " where f.fk_statut = '2' AND f.paye = '1' ";
		$sql .= $sqlfiltro;
		$sql .= " Group By (p.rowid)";
		//$sql .= " order by (cantidad_vendida) desc";
        $sql.= $this->db->order("cantidad_vendida","DESC");
        $sql.= $this->db->plimit($this->maximo,0);

        $resql=$this->db->query($sql);
        if ($resql)
        {
			require_once(DOL_DOCUMENT_ROOT.'/product/class/product.class.php');
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
			    $objp = $this->db->fetch_object($resql);
			    $producto=new Product($this->db);
				$producto->fetch($objp->rowid);

                $topproductos[$i][0]=$objp;
				$topproductos[$i][1]=$producto;
                $i++;
            }

        }
		//$this->db->free();
        return $topproductos;
	}
	function TTFacturaDinero($tipotopten,$datos)
	{
	switch($tipotopten)
	{
		case "ttanual":
		{
			$sqlfiltro .= " AND YEAR(f.datef)='".$datos[0]."' ";
			break;
		}
		case "ttmensual":
		{
			$sqlfiltro .= " AND MONTH(f.datef)='".$datos[0]."' AND YEAR(f.datef)='".$datos[1]."'";
			break;
		}
		case "ttentrefecha":
		{
			$sqlfiltro .= " AND (f.datef)>='".$datos[0]."' AND (f.datef)<='".$datos[1]."'";
			break;
		}
	}
	    $topfacturas = array();
        $sql = "SELECT f.rowid,f.total_ttc as importe";
        $sql .= " FROM ".MAIN_DB_PREFIX."facture as f ";
        $sql .= " where f.fk_statut = '2' AND f.paye = '1' ";
		$sql .= $sqlfiltro;

        $sql.= $this->db->order("importe","DESC");
        $sql.= $this->db->plimit($this->maximo,0);

        $resql=$this->db->query($sql);
        if ($resql)
        {
			require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
			    $objf = $this->db->fetch_object($resql);
			    $factura=new Facture($this->db);
				$factura->fetch($objf->rowid);

                $topfacturas[$i][0]=$objf;
				$topfacturas[$i][1]=$factura;
                $i++;
            }

        }
		//$this->db->free();
        return $topfacturas;
	}
	function TTFacturaTotalProductosUnidades($tipotopten,$datos)
	{
	switch($tipotopten)
	{
		case "ttanual":
		{
			$sqlfiltro .= " AND YEAR(f.datef)='".$datos[0]."' ";
			break;
		}
		case "ttmensual":
		{
			$sqlfiltro .= " AND MONTH(f.datef)='".$datos[0]."' AND YEAR(f.datef)='".$datos[1]."'";
			break;
		}
		case "ttentrefecha":
		{
			$sqlfiltro .= " AND (f.datef)>='".$datos[0]."' AND (f.datef)<='".$datos[1]."'";
			break;
		}
	}
	    $topfacturas = array();
        $sql = "SELECT f.rowid,SUM(pf.qty) as suma_productos";
        $sql .= " FROM ".MAIN_DB_PREFIX."facture as f ";
		$sql .= " JOIN ".MAIN_DB_PREFIX."facturedet as pf ON f.rowid=pf.fk_facture ";
        $sql .= " where f.fk_statut = '2' AND f.paye = '1' ";
		$sql .= $sqlfiltro;
		$sql .= " group BY(f.rowid) ";
        $sql.= $this->db->order("suma_productos","DESC");
        $sql.= $this->db->plimit($this->maximo,0);

        $resql=$this->db->query($sql);
        if ($resql)
        {
			require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
			    $objf = $this->db->fetch_object($resql);
			    $factura=new Facture($this->db);
				$factura->fetch($objf->rowid);

                $topfacturas[$i][0]=$objf;
				$topfacturas[$i][1]=$factura;
                $i++;
            }

        }
		//$this->db->free();
        return $topfacturas;
	}


	function select_year($selected='',$htmlname='yearid',$useempty=0, $min_year=10, $max_year=5)
    {
        $currentyear = date("Y");
    	$max_year = $currentyear+$max_year;
        $min_year = $currentyear-$min_year;
        if(empty($selected)) $selected = $currentyear;

        print '<select class="flat" name="' . $htmlname . '">';
        if($useempty)
        {
            if ($selected == '') $selected_html = ' selected="selected"';
            print '<option value=""' . $selected_html . '>&nbsp;</option>';
        }
        for ($y = $max_year; $y >= $min_year; $y--)
        {
            $selected_html='';
            if ($selected > 0 && $y == $selected) $selected_html = ' selected="selected"';
            print '<option value="'.$y.'"'.$selected_html.' >'.$y.'</option>';
        }
        print "</select>\n";
    }


	function select_month($selected='',$htmlname='monthid',$useempty=0)
    {
        global $langs;
        require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

        $month = monthArray($langs);	// Get array

        $select_month = '<select class="flat" name="'.$htmlname.'">';
        if ($useempty)
        {
            $select_month .= '<option value="0">&nbsp;</option>';
        }
        foreach ($month as $key => $val)
        {
            if ($selected == $key)
            {
                $select_month .= '<option value="'.$key.'" selected="selected">';
            }
            else
            {
                $select_month .= '<option value="'.$key.'">';
            }
            $select_month .= $val;
        }
        $select_month .= '</select>';
        return $select_month;
    }
}
?>