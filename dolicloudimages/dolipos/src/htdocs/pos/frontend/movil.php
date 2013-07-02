<?php
/*$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");;
if (! $res) die("Include of main fails");

require_once(DOL_DOCUMENT_ROOT ."/pos/backend/class/pos.class.php");*/

$res=@include("../../main.inc.php");                                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");                // For "custom" directory

dol_include_once('/pos/backend/class/pos.class.php');
global $db, $langs;
$langs->load("pos@pos");
$langs->load("bills");
$langs->load("companies");

if(empty($_SESSION['uname']) || empty($_SESSION['TERMINAL_ID']))
{
	accessforbidden();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html style="height: 100%; overflow: hidden;" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Cache-Control" content="private, max-age=5400, pre-check=5400"/>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
	<title><?php echo $langs->trans("DolibarTPV"); ?></title> 

	
<!--  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />-->
 <link href="css/jquery.mobile-1.2.0.min.css" type="text/css" rel="Stylesheet" class="ui-theme">
  <link href="css/movil.css" type="text/css" rel="Stylesheet" >
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<!--  <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>-->
	<script type="text/javascript" src="js/jquery.mobile-1.2.0.min.js"></script>
	<script type="text/javascript" src="js/jquery.class.js"></script>
	<script type="text/javascript" src="js/movil.js"></script>
 	
 


</head>

<body> 
<div data-role="page" id="home" data-theme="b" class="type-interior">
            <div data-role="header">
                <h3>
                    <?php echo $langs->trans("Dolipos"); ?>
                </h3>
            </div>
            <div data-role="content">

            
				<div>
				<ul>
					<li><?php echo $langs->trans("Employee"); ?>:<strong><span id="id_user_name"></span></strong></li>
					<li><?php echo $langs->trans("Terminal"); ?>:<strong><span id="id_user_terminal"></span></strong></li>
				</ul>
				</div>
            </div>
	
			<div data-role="footer" data-id="pageFooter" data-theme="b" data-position="fixed">
                <div  data-role="navbar" class="nav-glyphish-example" >
                    <ul>
                        <li data-theme="b"><a id="btnLogout" data-icon="home" ><?php echo $langs->trans("Close"); ?></a></li>
                        <li data-theme="c"><a href="#services" onclick="_TPV.getPlaces();" id="user" data-icon="star"><?php echo $langs->trans("Places"); ?></a></li>
                    </ul>
                </div>

			</div>
</div>
<div data-role="page"  id="services"  data-theme="b" class="type-interior">
	
	<div data-role="content" >  
		
		<ul id="listplaces" data-theme="c" data-role="listview">
		
	
		</ul>
	</div>
	<div data-role="footer" data-id="pageFooter" data-theme="b" data-position="fixed">
    	<div data-role="navbar" class="nav-glyphish-example" >
			<ul>
				<li><a href="#categorypage" id="products" data-icon="grid"><?php echo $langs->trans("Products"); ?></a></li>
				<li><a href="#cart" id="cart" data-icon="custom"><?php echo $langs->trans("Ticket"); ?></a></li>
				<li ><a href="#home" data-icon="home" id="home"><?php echo $langs->trans("Home"); ?></a></li>
			</ul>
		</div>
   </div>	
</div>
<div data-role="page"  id="categorypage"  data-theme="b" class="type-interior">
	<div data-role="header" data-id="pageHeader" data-theme="b" >
		<div data-role="navbar" class="nav-glyphish-example" data-iconpos="left" >
			<ul>
				<li><a href="#" id="homeCategory" onclick="_TPV.getCategories(0);" data-icon="grid"><?php echo $langs->trans("Home"); ?></a></li>
				
			</ul>
			</div>
	</div>
	<div data-role="content" >  
			<ul id="categories" data-theme="c" data-role="listview">
		</ul>
	
	
	
	</div>
	<div data-role="footer" data-id="pageFooter" data-theme="b" data-position="fixed">
    	<div data-role="navbar" class="nav-glyphish-example" >
			<ul>
				<li><a href="#categorypage" id="products" data-icon="grid"><?php echo $langs->trans("Products"); ?></a></li>
				<li><a href="#cart" id="cart" data-icon="custom"><?php echo $langs->trans("Ticket"); ?></a></li>
				<li><a href="#services" onclick="_TPV.getPlaces();" id="user" data-icon="star"><?php echo $langs->trans("Places"); ?></a></li>
			</ul>
		</div>
   </div>	
</div>


<div data-role="page" id="cart" data-theme="b">
	<div data-role="header" data-id="pageHeader" data-theme="b" >
		<h3><a href="#infocart" onclick="_TPV.ticket.editInfoTicket();"  data-icon="grid"><span id="placeName"></span> - <span id="totalTicket"></span> <?php echo $conf->currency ?></a></h3>
	</div>
	<div data-role="content">  
	<ul id="ticketCart" data-theme="c" data-role="listview"></ul>
		
    </div>
    
    <div data-role="footer" data-id="pageFooter" data-theme="b" data-position="fixed">
    <div data-role="navbar" class="nav-glyphish-example" >
			<ul>
				<li><a href="#categorypage" id="products" data-icon="grid"><?php echo $langs->trans("Products"); ?></a></li>
				<li><a href="#services" onclick="_TPV.getPlaces();" id="" data-icon="grid"><?php echo $langs->trans("Places"); ?></a></li>		
				<li><a href="#" id="btnSaveTicket" data-icon="star"><?php echo $langs->trans("Send"); ?></a></li>
			</ul>
	</div>
   </div>	
</div>


<div data-role="page" id="productEdit" data-theme="b">
	<div data-role="header" data-id="pageHeader" data-theme="b" >
		<h3 id="productLabel"><?php echo $langs->trans("SelectProduct"); ?></h3>
	</div>
	<div data-role="content">  
	<div class="clearfix" id="info_product">
	<input type="hidden" name="idProduct" id="idProduct" value="0"  />
		<div id="productForm">
		
			<label for="cantidad"><?php echo $langs->trans("Quantity"); ?></label>
			<input type="range" name="line_quantity" id="line_quantity" value="1" min="0" max="50" />
			<label for="line_note"><?php echo $langs->trans("Note"); ?></label>
			<input name="line_note" id="line_note" />
		</div>
		<div>	
		
            <div class="">
				
                <div id="short_description_block">
					<div class="rte align_justify" id="short_description_content"><p><?php echo $langs->trans("NoDescription"); ?></p></div>
				</div>
                <p class="price">
					<span class="our_price_display">
						<span id="our_price_display"><?php echo $langs->trans("00,00"); ?></span>€
                	</span>
				</p>
			</div> 
				<div class="">
                    <img  id="bigpic" alt="" title="<?php echo $langs->trans("Product"); ?>" src="" style="display: inline;"></img>
            </div>  		
		</div>
    </div>	
	
    </div>
    
    <div data-role="footer" data-id="pageFooter" data-theme="b" data-position="fixed">
    <div data-role="navbar" class="nav-glyphish-example" >
			<ul>
				 <li><a href="#cart" id="" data-icon="grid"><?php echo $langs->trans("Cancel"); ?></a></li>
				<li><a href="#cart" id="deleteLine"  data-icon="delete"><?php echo $langs->trans("Delete"); ?></a></li>
				<li><a href="#" id="saveTicketLine" data-icon="star"><?php echo $langs->trans("Save"); ?></a></li>
			</ul>
		</div>
   </div>	
</div>

<div data-role="page" id="infocart" data-theme="b">
	
	<div data-role="content">  
	<label for="ticketnotes"><?php echo $langs->trans("Note"); ?></label>
	<input id="ticketnotes" type="text" value="" placeholder="<?php echo $langs->trans("Note"); ?>" name="ticketnotes">
		
    </div>
    
    <div data-role="footer" data-id="pageFooter" data-theme="b" data-position="fixed">
    <div data-role="navbar" class="nav-glyphish-example" >
			<ul>
				<li><a href="#cart"  data-icon="grid"><?php echo $langs->trans("Cancel"); ?></a></li>
				<li><a id="btnSaveInfoTicket" data-icon="star"><?php echo $langs->trans("Save"); ?></a></li>
			</ul>
	</div>
   </div>	
</div>


</body>
</html>