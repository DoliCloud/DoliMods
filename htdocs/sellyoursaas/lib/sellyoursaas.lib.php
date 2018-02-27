<?php
/* Copyright (C) 2018	Laurent Destailleur	<eldy@users.sourceforge.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    lib/sellyoursaas.lib.php
 * \ingroup sellyoursaas
 * \brief   Library files with common functions for SellYourSaas module
 */


/**
 * Return IP of server to deploy to
 */
function getRemoveServerDeploymentIp()
{
	global $conf;

	if (empty($conf->global->SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES)) $ip='localhost';
	else $ip = $conf->global->SELLYOURSAAS_REMOTE_SERVER_IP_FOR_INSTANCES;

	return $ip;
}

/**
 * Return if instance is a paid instance or not
 * Check if there is a template invoice
 *
 * @param 	Contrat $contract		Object contract
 * @return	int						>0 if this is a paid contract
 */
function sellyoursaasIsPaidInstance($contract)
{
	$contract->fetchObjectLinked();
	$foundtemplate=0;

	if (is_array($contract->linkedObjects['facturerec']))
	{
		foreach($contract->linkedObjects['facturerec'] as $idtemplateinvoice => $templateinvoice)
		{
			$foundtemplate++;
			break;
		}
	}

	if ($foundtemplate) return 1;

	if (is_array($contract->linkedObjects['facture']))
	{
		foreach($contract->linkedObjects['facture'] as $idtemplateinvoice => $templateinvoice)
		{
			$foundinvoice++;
			break;
		}
	}

	if ($foundinvoice) return 1;

	/*
	$nbinvoicenotpayed = 0;
	$amountdue = 0;
	foreach ($listofcontractid as $id => $contract)
	{
		$contract->fetchObjectLinked();
		if (is_array($contract->linkedObjects['facture']))
		{
			foreach($contract->linkedObjects['facture'] as $idinvoice => $invoice)
			{
				if ($invoice->statut != $invoice::STATUS_CLOSED)
				{
					$nbinvoicenotpayed++;
				}
				$alreadypayed = $invoice->getSommePaiement();
				$amount_credit_notes_included = $invoice->getSumCreditNotesUsed();
				$amountdue = $invoice->total_ttc - $alreadypayed - $amount_credit_notes_included;
			}
		}
	}*/

	return 0;
}


/**
 * Return date of expiration
 * Take lowest end of planed date for services (whatever is service status)
 *
 * @param 	Contrat $contract		Object contract
 * @return	int						Timestamp of expiration date, or 0 if error or not found
 */
function sellyoursaasGetExpirationDate($contract)
{
	$expirationdate = 0;

	// Loop on each line to get lowest expiration date
	foreach($contract->lines as $line)
	{
		if ($line->date_end)
		{
			if ($expirationdate > 0) $expirationdate = min($expirationdate, $line->date_end);
			else $expirationdate = $line->date_end;
		}
	}

	return $expirationdate;
}



