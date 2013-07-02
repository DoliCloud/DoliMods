<?php
/* Copyright (C) 2011-2012	   Juanjo Menent   	   <jmenent@2byte.es>
 * Copyright (C) 2012	   	   Ferran Marcet   	   <jmenent@2byte.es>
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
 *	\file       htdocs/pos/frontend/index.php
 * 	\ingroup	pos
 *  \brief      File to login to point of sales
 */

// Set and init common variables
// This include will set: config file variable $dolibarr_xxx, $conf, $langs and $mysoc objects

$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/pos.class.php');

$langs->load("admin");
$langs->load("pos@pos");

if (! $user->rights->pos->frontend)
  accessforbidden();

// Test if user logged
if ( $_SESSION['uid'] > 0 )
{
	header ('Location: '.dol_buildpath('/pos/frontend/disconect.php',1));
	exit;
}

global $user,$conf;

$usertxt=$user->login;
$pwdtxt=$user->pass;

$openterminal=GETPOST("openterminal");

/*
 * View
 */

$arrayofcss=array('/pos/frontend/css/pos.css');
top_htmlhead('','',0,0,'',$arrayofcss);

?>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>DoliPOS</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
  ================================================== -->
  	<link rel="stylesheet" href="js/jqtransform.css" type="text/css" media="all" />
  	<link rel="stylesheet" type="text/css" href="css/jquery.tweet.css"/>
	<link rel="stylesheet" type="text/css" href="css/keyboard.css">
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/layout.css"> 
	

	
	
	
    <link href='http://fonts.googleapis.com/css?family=Exo:200,700' rel='stylesheet' type='text/css'>
    
	<script type="text/javascript" src="js/jquery.jqtransform.js" ></script>
	<script type="text/javascript" src="js/jquery.keyboard.min.js"></script>
	<script language="javascript">
	$(function(){
		$('form.nice').jqTransform({imgPath:'img/'});
		
		});
		$(document).ready(function() {
			$('#tpvtactil').click(function(){
				tpvtactil();
			});
		});
	
		function tpvtactil()
		{
			$('#tpvtactil').removeClass('tactilon');	
			$('[type=text]').keyboard({
				layout:'qwerty',
				usePreview:false , 
				autoAccept : true,
				accepted : function(e, keyboard, el){
			
			}	
		});
		$('[type=password]').keyboard({
			layout:'qwerty',
			usePreview:false , 
			autoAccept : true,
			accepted : function(e, keyboard, el){
			
			}	
		});
	}
</script>
	
<body>


	<div class="container" >
		<div class="twelve2 columns">
        	<div class="twelve2 columns">
				<a href="index.php" title="" target="_self"><h1 class="remove-bottom" style="margin-top: 10px"><img src="img/logo_pos.png" width="" height="" alt="Logo" title=""></h1></a>
				<h3><?php echo $langs->trans("HeadPos"); ?> </h3>
			</div>
           
        	
		</div>
		
        
        
	<div class="twelve columns">
		<?php if(GETPOST("err","string")) {?>	
        <div class="errorLogin"><?php print GETPOST("err","string")."<br>"; ?></div> <?php }?>
		<fieldset class="cadre_facturation"><!--<legend class="titre1"><?php /*?><?php echo $langs->trans("Identification"); ?><?php */?></legend>-->

		
		 <?php 
				$terminals=POS::select_Terminals();
				if(sizeof($terminals))
				{	
?>
		  <div class="six columns">	
			<form id="frmLogin" method="POST" action="verify.php" class="nice">
				<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />

					<br>
					<label><?php echo $langs->trans("Login"); ?></label>
					<input name="txtUsername" class="texte_login" type="text" value="<?php echo $usertxt; ?>"  />
					<div class="sep"></div>
					<label><?php echo $langs->trans("Password"); ?></label>
					<input name="pwdPassword" class="texte_login" type="password"	value="" />
					<div class="sep"></div>
					<label><?php echo $langs->trans("CashS"); ?></label>
					<select name='txtTerminal'>
					<!--<option value='-1'><?php $langs->trans("Choose"); ?></option> -->
        
<?php 
					
					$i=0;
					foreach ($terminals as $terminal)
	    			{
	    				if($conf->browser->phone)
	    				{
	    					if($terminal["tactil"] == 2)
	    					{
	    						print "<option value='".$terminal["rowid"]."'>".$terminal["name"]."</option>\n";
	    					}
	    				}	
	    				else 
	    				{
	    					print "<option value='".$terminal["rowid"]."'>".$terminal["name"]."</option>\n";
	    				}
	      				
	      				$i++;
	    			}
?>
			  		</select>
			  	
			  	
        				<div class="sep"></div>
            			<input type="submit"  name="sbmtConnexion" value=<?php echo $langs->trans("Connection"); ?> />
						<input id="tpvtactil" type="button"  value=<?php echo $langs->trans("Tactil"); ?> />
						<input type="submit" id="Backend" name="sbmtBackend" value=<?php echo $langs->trans("Backend"); ?> />
				</form>		
					
		 </div>
		
		<?php 
			
	    	}
	    	else
	    	{ ?>
	    	<div class="six2 columns">
	    	<form id="frmLogin" method="POST" action="verify.php" class="nice">
	    		<p><?php echo $langs->trans("NotHasTerminal"); ?></p>
	    		<div class="sep"></div>
	    		<input type="submit" id="Backend" name="sbmtBackend" value=<?php echo $langs->trans("Backend"); ?> />
	    	</form>	
			</div>
<?php    	}
?>
		
		
        	<div class="sep"></div>
        	<div class="five columns">
				<div class="second_login">
					<img src="img/logo_pos.png" width="245" height="79" alt="Logo" title="" >
 					<div id="tweets">
            			<div class="tweet"></div> 
            		</div>
 				</div>
 			</div>
        			
        	
			</fieldset>

<?php
		if ($_GET['err'] < 0) 
		{

			echo ('<script type="text/javascript">');
			echo ('	document.getElementById(\'frmLogin\').pwdPassword.focus();');
			echo ('</script>');

		}	 
		else 
		{

			echo ('<script type="text/javascript">');
			echo ('	document.getElementById(\'frmLogin\').txtUsername.focus();');
			echo ('</script>');

		}
?>
        	
        </div>	
		
        
		                    
		<!--  </li>-->
<!--END PAGE 1-->   
        
  

	<!--  </ul>-->

	<div class="twelve2 columns">  
	</br> 

	
	
		<div class="milogo"><img src="img/co_logo.png" class="scale-with-grid" alt="" title="" width="" height=""></div>
        <?php echo $langs->trans("CopyRight"); ?> &copy; 2012 2byte.es - <?php echo $langs->trans("RightsReserved"); ?>
	</div>
</div><!-- container -->



	<script type="text/javascript" src="js/jquery.tweet.js"></script>

<!-- LATEST TWEETS MODULE -->                       
<script type='text/javascript'>
    jQuery(function($){
        $(".tweet").tweet({
            username: "dolipos",
            join_text: "auto",
            avatar_size: 0,
            count: 20,
            auto_join_text_default: "",
            auto_join_text_ed: "",
            auto_join_text_ing: "",
            auto_join_text_reply: "",
            auto_join_text_url: "",
            loading_text: "Loading Tweets..."
        });
    });
</script> 


</body>

<?php
print '</html>';
?>