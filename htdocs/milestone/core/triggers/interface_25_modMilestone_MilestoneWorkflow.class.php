<?php
/* Copyright (C) 2010-2012  Regis Houssin  <regis@dolibarr.fr>
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
 *      \file       /milestone/inc/triggers/interface_25_modMilestone_MilestoneWorkflow.class.php
 *      \ingroup    milestone
 *      \brief      Trigger file for create milestone data
 */


/**
 *      \class      InterfaceMilestoneWorkflow
 *      \brief      Classe des fonctions triggers des actions personalisees du milestone
 */

class InterfaceMilestoneWorkflow
{
    var $db;
    
    /**
     *   Constructor
     *   
     *   @param      DB      Handler d'acces base
     */
    function __construct($db)
    {
        $this->db = $db;
    
        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "milestone";
        $this->description = "Triggers of this module allows to create milestone data";
        $this->version = '1.0.3.4';            // 'development', 'experimental', 'dolibarr' or version
        $this->picto = 'milestone@milestone';
    }
    
    
    /**
     *   Renvoi nom du lot de triggers
     *   
     *   @return     string      Nom du lot de triggers
     */
    function getName()
    {
        return $this->name;
    }
    
    /**
     *   Renvoi descriptif du lot de triggers
     *   
     *   @return     string      Descriptif du lot de triggers
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *   Renvoi version du lot de triggers
     *   
     *   @return     string      Version du lot de triggers
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') return $langs->trans("Development");
        elseif ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }
    
    /**
     *      Fonction appelee lors du declenchement d'un evenement Dolibarr.
     *      D'autres fonctions run_trigger peuvent etre presentes dans core/triggers
     *      
     *      @param      action      Code de l'evenement
     *      @param      object      Objet concerne
     *      @param      user        Objet user
     *      @param      lang        Objet lang
     *      @param      conf        Objet conf
     *      @return     int         <0 if fatal error, 0 si nothing done, >0 if ok
     */
	function run_trigger($action,$object,$user,$langs,$conf)
    {	
        // Mettre ici le code a executer en reaction de l'action
        // Les donnees de l'action sont stockees dans $object

    	
        // Add line
        if (($action == 'LINEPROPAL_INSERT' || $action == 'LINEORDER_INSERT' || $action == 'LINEBILL_INSERT') && ! empty($object->fk_parent_line))
        {
        	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->rowid);
        	
        	if ($action == 'LINEPROPAL_INSERT')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php');
        		$milestone = new PropaleLigne($this->db);
        	}
        	else if ($action == 'LINEORDER_INSERT')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php');
        		$milestone = new OrderLine($this->db);
        	}
        	else if ($action == 'LINEBILL_INSERT')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
        		$milestone = new FactureLigne($this->db);
        	}
        	
        	$milestone->fetch($object->fk_parent_line);
        	
        	$milestone->total_ht += $object->total_ht;
        	$milestone->total_tva += $object->total_tva;
        	$milestone->total_ttc += $object->total_ttc;
        	
        	$ret = $milestone->update_total();
        	
        	return $ret;
        }

        // Update line
        else if (($action == 'LINEPROPAL_UPDATE' || $action == 'LINEORDER_UPDATE' || $action == 'LINEBILL_UPDATE') && ($object->fk_parent_line > 0 || ! empty($object->oldline->fk_parent_line)))
        {
        	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->rowid);
        	
			if ($action == 'LINEPROPAL_UPDATE')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php');
        		$milestone = new PropaleLigne($this->db);
        	}
        	else if ($action == 'LINEORDER_UPDATE')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php');
        		$milestone = new OrderLine($this->db);
        	}
        	else if ($action == 'LINEBILL_UPDATE')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
        		$milestone = new FactureLigne($this->db);
        	}

        	// Stay a child
        	if ($object->fk_parent_line > 0 && ! empty($object->oldline->fk_parent_line))
        	{
        		// remove old values
        		$milestone->fetch($object->oldline->fk_parent_line);
        		if ($milestone->total_ht > 0) $milestone->total_ht -= $object->oldline->total_ht;
        		if ($milestone->total_tva > 0) $milestone->total_tva -= $object->oldline->total_tva;
        		if ($milestone->total_ttc > 0) $milestone->total_ttc -= $object->oldline->total_ttc;
        		$ret = $milestone->update_total();
        		
        		// add new values
        		if ($ret > 0)
        		{
        			$milestone->fetch($object->fk_parent_line);
        			$milestone->total_ht += $object->total_ht;
        			$milestone->total_tva += $object->total_tva;
        			$milestone->total_ttc += $object->total_ttc;
        			$ret = $milestone->update_total();
        		}
        	}
        	// Become a child
        	else if ($object->fk_parent_line > 0 && empty($object->oldline->fk_parent_line))
        	{
        		// add new values
        		$milestone->fetch($object->fk_parent_line);
        		$milestone->total_ht += $object->total_ht;
        		$milestone->total_tva += $object->total_tva;
        		$milestone->total_ttc += $object->total_ttc;
        		$ret = $milestone->update_total();
        	}
        	// Become an individual line
        	else if ($object->fk_parent_line < 0 && ! empty($object->oldline->fk_parent_line))
        	{
        		// remove old values
        		$milestone->fetch($object->oldline->fk_parent_line);
        		$milestone->total_ht -= $object->oldline->total_ht;
        		$milestone->total_tva -= $object->oldline->total_tva;
        		$milestone->total_ttc -= $object->oldline->total_ttc;
        		$ret = $milestone->update_total();
        	}

        	return $ret;
        }
        
    	// Delete line
        else if (($action == 'LINEPROPAL_DELETE' || $action == 'LINEORDER_DELETE' || $action == 'LINEBILL_DELETE') && ! empty($object->fk_parent_line))
        {
        	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->rowid);
        	
			if ($action == 'LINEPROPAL_DELETE')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php');
        		$milestone = new PropaleLigne($this->db);
        	}
        	else if ($action == 'LINEORDER_DELETE')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php');
        		$milestone = new OrderLine($this->db);
        	}
        	else if ($action == 'LINEBILL_DELETE')
        	{
        		require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
        		$milestone = new FactureLigne($this->db);
        	}
        	
        	$milestone->fetch($object->fk_parent_line);
        	
        	$milestone->total_ht -= $object->total_ht;
        	$milestone->total_tva -= $object->total_tva;
        	$milestone->total_ttc -= $object->total_ttc;
        	
        	$ret = $milestone->update_total();
        	
        	return $ret;
        }
        
        // Delete object
        else if ($action == 'PROPAL_DELETE' || $action == 'ORDER_DELETE' || $action == 'BILL_DELETE')
        {
        	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->rowid);
        	
        	dol_include_once("/milestone/class/dao_milestone.class.php");
        	
        	$milestone = new DaoMilestone($this->db);
        	
        	$error=0;
        	
        	foreach ($object->lines as $line)
        	{
        		if ($line->product_type == 9 && ! empty($line->special_code))
        		{
        			$ret=$milestone->delete($line->rowid,$object->element,false);
        			if ($ret<0) $error++;
        		}
        	}
        	
        	if (! $error) return 1;
        	else return -1;
        }

		return 0;
    }

}
?>
