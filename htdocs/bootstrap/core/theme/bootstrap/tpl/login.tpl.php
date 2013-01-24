<?php
/* Copyright (C) 2009-2010 Regis Houssin <regis@dolibarr.fr>
 * Copyright (C) 2011-2012	Philippe Grand	<philippe.grand@atoo-net.com>
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
 *
 */
header('Cache-Control: Public, must-revalidate');
header("Content-type: text/html; charset=".$conf->file->character_set_client);

?>
<!DOCTYPE html>
<html>
<?php
include('header.tpl.php');
?>
<body>

<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#"><?php print $title; ?></a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#"><?php echo $langs->trans("Home") ?></a></li>
              <li><a href="#about"><?php echo $langs->trans("About") ?></a></li>
              <li><a href="#contact"><?php echo $langs->trans("Contact") ?></a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div class="container">

	<!-- Main hero unit for a primary marketing message or call to action -->
    <div class="hero-unit">
		<h1>Hello, Dolibarr user!</h1>
        <p>&nbsp;</p>

		<form id="login" class="well form-inline" name="login" method="post" action="<?php echo $php_self; ?>">
		<fieldset>
			
				<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
				<input type="hidden" name="loginfunction" value="loginfunction">
				<!-- Add fields to send local user information -->
				<input type="hidden" name="tz" id="tz" value="">
				<input type="hidden" name="dst_observed" id="dst_observed" value="">
				<input type="hidden" name="dst_first" id="dst_first" value="">
				<input type="hidden" name="dst_second" id="dst_second" value="">
				<input type="hidden" name="screenwidth" id="screenwidth" value="">
				<input type="hidden" name="screenheight" id="screenheight" value="">
				<!-- Login -->
				<div class="control-group">
					<div class="controls">
						<input type="text" placeholder="<?php echo $langs->trans('Login'); ?>" name="username" id="username" maxlength="40" value="<?php echo GETPOST('username')?GETPOST('username'):$login; ?>" tabindex="1">
					</div>
				</div>
				<!-- Password -->
				<div class="control-group">
					<div class="controls">
						<input type="password" placeholder="<?php echo $langs->trans('Password'); ?>" name="password" id="password" maxlength="30" value="<?php echo $password; ?>" tabindex="2">
					</div>
				</div>
			
		<?php
		if (! empty($hookmanager->resArray['options'])) {
			foreach ($hookmanager->resArray['options'] as $option)
			{?>
				<div class="control-group">
					<div class="controls">
			  <?php echo '<!-- Option by hook -->';
					echo $option;?>
					</div>
				</div>
	 <?php  }
		}
		?>

		<?php if ($captcha) { ?>
		<!-- Captcha -->
		<label class="control-label"><?php echo $langs->trans('SecurityCode'); ?></label>
			<div class="controls">
				<input id="securitycode" type="text" placeholder="<?php echo $langs->trans('SecurityCode'); ?>" maxlength="5" name="code" tabindex="4">
			</div>
        </div>
		<div class="control-group">
			<div class="controls">
				<img src="<?php echo DOL_URL_ROOT ?>/core/antispamimage.php" border="0" width="80" height="32" id="captcha">
				<a class="btn btn-primary" href="<?php echo $php_self; ?>"><i class="icon-refresh icon-white"></i><?php echo $captcha_refresh; ?></a>
			</div>
        </div>
		<?php } ?>
		
		<!-- Button Connection -->
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn btn-primary btn-large" value="<?php echo $langs->trans('Connection'); ?>" tabindex="5">
			</div>
		</div>		     
      </div>
	  </fieldset>
</form>

	   <!-- Example row of columns -->
	  <?php if ($forgetpasslink || $helpcenterlink) { ?>
      <div class="row">
        <div class="span4">
          <h2><?php echo $langs->trans('PasswordForgotten'); ?></h2>
           <p>Redirection vers le formulaire permettant d'envoyer un nouveau mot de passe. Il sera envoyé à l'adresse email de votre user. La modification du mot de passe ne sera effective qu'après clic par le destinataire du lien de confirmation inclut dans ce mail. Surveillez votre messagerie.</p>
		   <?php if ($forgetpasslink) { ?>
          <p><a class="btn btn-large btn-info" href="<?php echo DOL_URL_ROOT.'/user/passwordforgotten.php'; ?>">
		  <i class="icon-info-sign icon-white"></i>&nbsp;Redirection &raquo;</a>	</p>
		  <?php } ?>
        </div>
        <div class="span4">
          <h2><?php echo $langs->trans('NeedHelpCenter'); ?></h2>
           <p>Cette application, indépendante de Dolibarr, vous permet de vous aider à obtenir un service de support sur Dolibarr.<BR>
			Choisissez le service qui correspond à votre besoin en cliquant sur le lien adéquat ( Certains de ces services ne sont disponibles qu'en anglais )... </p>
		   <?php if ($helpcenterlink) { ?>
          <p><a class="btn btn-large btn-info" href="<?php echo $dol_url_root.'/support/index.php'; ?>">
		  <i class="icon-info-sign icon-white"></i>&nbsp;Support &raquo;</a>		  
		  </p>
		  <?php } ?>
       </div>
       
      </div>
	<?php } ?>
      <hr>

     <?php if ($main_home) { ?>
	<div id="infoLogin">
	<?php echo $main_home; ?>
	</div>
<?php } ?>

<?php if ($_SESSION['dol_loginmesg']) { ?>
	<div class="error">
	<?php echo $_SESSION['dol_loginmesg']; ?>
	</div>
<?php } ?>

	<?php
	if (! empty($conf->global->MAIN_GOOGLE_AD_CLIENT) && ! empty($conf->global->MAIN_GOOGLE_AD_SLOT))
	{
	?>
		<div align="center">
			<script type="text/javascript"><!--
				google_ad_client = "<?php echo $conf->global->MAIN_GOOGLE_AD_CLIENT ?>";
				google_ad_slot = "<?php echo $conf->global->MAIN_GOOGLE_AD_SLOT ?>";
				google_ad_width = <?php echo $conf->global->MAIN_GOOGLE_AD_WIDTH ?>;
				google_ad_height = <?php echo $conf->global->MAIN_GOOGLE_AD_HEIGHT ?>;
				//-->
			</script>
			<script type="text/javascript"
				src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
	<?php } ?>
	




<!-- authentication mode = <?php echo $main_authentication ?> -->
<!-- cookie name used for this session = <?php echo $session_name ?> -->
<!-- urlfrom in this session = <?php echo $_SESSION["urlfrom"] ?> -->

<?php if (! empty($conf->global->MAIN_HTML_FOOTER)) print $conf->global->MAIN_HTML_FOOTER; ?>

</body>
</html>

<!-- END PHP TEMPLATE -->