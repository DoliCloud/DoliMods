<?php
/* Copyright (C) 2011 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/concatpdf/class/actions_concatpdf.class.php
 *	\ingroup    societe
 *	\brief      File to control actions
 *	\version    $Id: actions_cabinetmed.class.php,v 1.8 2011/09/11 18:41:48 eldy Exp $
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");


/**
 *	\class 		ActionsConcatPdf
 *	\brief 		Class to manage hooks for module ConcatPdf
 */
class ActionsConcatPdf
{
    var $db;
    var $error;
    var $errors=array();

    /**
     *	Constructor
     *
     *  @param		DoliDB		$db      Database handler
     */
    function ActionsCabinetmed($db)
    {
        $this->db = $db;
    }


    /**
     * Complete doc forms
     *
     * @param	array	$parameters		Array of parameters
     * @return	string					HTML content to add by hook
     */
    function formBuilddocOptions($parameters)
    {
        global $langs, $user, $conf;

        $langs->load("concatpdf@concatpdf");
        $htmlform=new Form($this->db);

        $out='';
        $out.='<tr class="liste_titre">';
        $out.='<td align="left" colspan="4" valign="top" class="formdoc">';
        $out.=$langs->trans("ConcatFile").' ';

        $filescgv='';
        if ($parameters['modulepart'] == 'propal') $filescgv=glob($conf->concatpdf->dir_output."/proposals/*.pdf");
        if ($parameters['modulepart'] == 'order'   || $parameters['modulepart'] == 'commande') $filescgv=glob($conf->concatpdf->dir_output."/orders/*.pdf");
        if ($parameters['modulepart'] == 'invoice' || $parameters['modulepart'] == 'facture')  $filescgv=glob($conf->concatpdf->dir_output."/invoices/*.pdf");

        if ($filescgv)
        {
            foreach ($filescgv as $cgvfilename)
            {
                $morefiles[] = basename($cgvfilename, ".pdf");
            }
        }
        if (count($morefiles) > 0)
        {
            $out.= $htmlform->selectarray('concatpdffile',$morefiles,(GETPOST('concatpdffile')?GETPOST('concatpdffile'):-1),1,0,1);
        }

        return $out;
    }



    /**
     * Execute action
     *
     * @param	array	$parameters		Array of parameters
     * @param   Object	&$object    	Deprecated. This field is nto used
     * @param   string	$action     	'add', 'update', 'view'
     * @return  int 		        	<0 if KO,
     *                          		=0 if OK but we want to process standard actions too,
     *  	                            >0 if OK and we want to replace standard actions.
     */
    function afterPDFCreation($parameters,&$object,&$action)
    {
        global $langs,$conf;

        $outputlangs=$langs;

        $ret=0;
        dol_syslog(get_class($this).'::executeHooks action='.$action);

        $filetoconcat1=$parameters['file'];
        $filetoconcat2='';
        //var_dump($parameters['object']->element); exit;
        if ($parameters['object']->element == 'propal')  $filetoconcat2=$conf->concatpdf->dir_output.'/proposals/'.GETPOST('concatpdffile').'.pdf';
        if ($parameters['object']->element == 'order'   || $parameters['object']->element == 'commande') $filetoconcat2=$conf->concatpdf->dir_output.'/orders/'.GETPOST('concatpdffile').'.pdf';
        if ($parameters['object']->element == 'invoice' || $parameters['object']->element == 'facture')  $filetoconcat2=$conf->concatpdf->dir_output.'/invoices/'.GETPOST('concatpdffile').'.pdf';

        dol_syslog(get_class($this).'::afterPDFCreation '.$filetoconcat1.' - '.$filetoconcat2);

        if ($filetoconcat2 && GETPOST('concatpdffile') && GETPOST('concatpdffile') != '-1')
        {
            // Create empty PDF
            $pdf=pdf_getInstance();
            if (class_exists('TCPDF'))
            {
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
            }
            $pdf->SetFont(pdf_getPDFFont($outputlangs));

            if ($conf->global->MAIN_DISABLE_PDF_COMPRESSION) $pdf->SetCompression(false);
            //$pdf->SetCompression(false);

            // Insert file 1
            $pagecount = $pdf->setSourceFile($filetoconcat1);
            for ($i = 1; $i <= $pagecount; $i++)
            {
                $tplidx = $pdf->importPage($i);
                $s = $pdf->getTemplatesize($tplidx);
                $pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
                $pdf->useTemplate($tplidx);
            }

            // Insert file 2
            $pagecount = $pdf->setSourceFile($filetoconcat2);
            for ($i = 1; $i <= $pagecount; $i++)
            {
                $tplidx = $pdf->importPage($i);
                $s = $pdf->getTemplatesize($tplidx);
                $pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
                $pdf->useTemplate($tplidx);
            }

            if ($pagecount)
            {
                $pdf->Output($filetoconcat1,'F');
                if (! empty($conf->global->MAIN_UMASK))
            				@chmod($file, octdec($conf->global->MAIN_UMASK));
            }
        }

        return $ret;
    }

}

?>
