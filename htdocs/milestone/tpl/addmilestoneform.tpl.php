<?php
/* Copyright (C) 2010-2014 Regis Houssin <regis.houssin@capnetworks.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 */
?>

<!-- BEGIN PHP TEMPLATE -->
<script type="text/javascript">
$(document).ready(function () {
	$("#milestone_label").focus(function() {
		hideMessage("milestone_label","<?php echo $langs->transnoentities('Label'); ?>");
    });
    $("#milestone_label").blur(function() {
        displayMessage("milestone_label","<?php echo $langs->transnoentities('Label'); ?>");
    });
	displayMessage("milestone_label","<?php echo $langs->transnoentities('Label'); ?>");
	$("#milestone_label").css("color","grey");
})
</script>

<tr class="liste_titre nodrag nodrop">
	<td><?php echo $langs->trans('AddMilestone'); ?></td>
	<td colspan="10">&nbsp;</td>
</tr>

<input type="hidden" name="special_code" value="1790">
<input type="hidden" name="product_type" value="9">

<tr <?php echo $GLOBALS['bcnd'][$GLOBALS['var']]; ?>>
	<td colspan="6">
	<input size="30" type="text" id="milestone_label" name="milestone_label" value="<?php echo $_POST["milestone_label"]; ?>">
	<input type="checkbox" name="pagebreak" value="1" /> <?php echo $langs->transnoentities('AddPageBreak'); ?>
	</td>

	<td align="center" valign="middle" rowspan="2" colspan="4">
	<input type="submit" class="button" value="<?php echo $langs->trans('Add'); ?>" name="addmilestone">
	</td>
</tr>

<tr <?php echo $GLOBALS['bcnd'][$GLOBALS['var']]; ?>>
	<td colspan="6">
	<?php
	require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
    $nbrows=ROWS_2;
    if (! empty($conf->global->MAIN_INPUT_DESC_HEIGHT)) $nbrows=$conf->global->MAIN_INPUT_DESC_HEIGHT;
	$doleditor=new DolEditor('milestone_desc',$_POST["milestone_desc"],'',100,'dolibarr_details','',false,true,$conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_DETAILS,$nbrows,70);
	$doleditor->Create();
	?>
	</td>
</tr>
<!-- END PHP TEMPLATE -->
