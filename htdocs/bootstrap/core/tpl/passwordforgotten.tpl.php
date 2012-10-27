<?php
/* Copyright (C) 2009-2010 Regis Houssin  <regis@dolibarr.fr>
 * Copyright (C) 2011-2012 Philippe Grand <philippe.grand@atoo-net.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
top_httphead();
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
              <li class="active"><a href="<?php echo $main_home; ?>">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

<div class="container">
<div class="hero-unit">
<h1>Régénérez votre mot de passe!</h1>
<p><?php echo $langs->trans('SendNewPasswordDesc'); ?></p>

<form class="form-horizontal" id="login" name="login" method="post" action="<?php echo $php_self; ?>">
	<fieldset>
		
		<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
		<input type="hidden" name="action" value="buildnewpassword">
		<!-- Login -->
		<div class="control-group">			
			<label class="control-label" for="username"><?php echo $langs->trans('Login'); ?></label>
			<div class="controls">
				<input type="text" id="username" name="username" maxlength="25"  value="<?php echo $login; ?>" tabindex="1">
			</div>
		</div>
		<div class="control-group">
		<?php if ($select_entity) { ?>
		<label class="control-label"><?php echo $langs->trans('Entity'); ?></label>
			<div class="controls">	
				<?php echo $select_entity; ?>        
			</div>
		<?php } ?>
		</div>

		<div class="control-group">
		<?php if ($captcha) { ?>
		<label class="control-label"><?php echo $langs->trans('SecurityCode'); ?></label>
			<div class="controls">
				<input id="securitycode" type="text" placeholder="<?php echo $langs->trans('SecurityCode'); ?>" maxlength="5" name="code" tabindex="3">
			</div>
        </div>
		<div class="control-group">
			<div class="controls">
				<img src="<?php echo $dol_url_root.'/core/antispamimage.php'; ?>" border="0" width="80" height="32" id="img_securitycode">
				<a class="btn btn-primary" href="<?php echo $php_self; ?>"><i class="icon-refresh icon-white"></i>&nbsp;<?php echo $langs->trans('Rafraichir'); ?></a>
			</div>
        </div>
		<?php } ?>

		<div class="control-group">
			<input id="password" type="submit" <?php echo $disabled; ?> class="btn btn-primary" name="password" value="<?php echo $langs->trans('SendNewPassword'); ?>">
		</div>
	</div>
	</fieldset>
</form>


		<?php if ($mode == 'dolibarr' || ! $disabled) {
			echo $langs->trans('');
		}else{
			echo $langs->trans('AuthenticationDoesNotAllowSendNewPassword', $mode);
		} ?>
	

<?php if ($message) {?>
	<div class="alert alert-error"><?php echo $message;?></div>
 <?php } ?>


<div class="other">
<a href="<?php echo $dol_url_root; ?>/">
	<?php echo $langs->trans('BackToLoginPage'); ?>
</a>
</div>

</body>
</html>

<!-- END PHP TEMPLATE -->