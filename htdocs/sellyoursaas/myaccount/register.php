<?php

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				    // If this page is public (can be called outside logged session)


include ('./common.inc.php');

// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include($_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php");
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include(substr($tmp, 0, ($i+1))."/main.inc.php");
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=include(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php");
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res) die("Include of main fails");


require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

$langs->load("sellyoursaas@sellyoursaas");


/*
 * View
 */

$form = new Form($db);

$conf->dol_hide_topmenu = 1;
$conf->dol_hide_leftmenu = 1;

llxHeader();


include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
$productref='DOLV1Basic';
if (GETPOST('plan','alpha'))
{
	$productref='DOLICLOUD-PACK-'.GETPOST('plan','alpha');
}
$tmpproduct = new Product($db);
$result = $tmpproduct->fetch(0, $productref);
?>

<div id="waitMask" style="display:none;">
    <font size="3em" style="color:#888; font-weight: bold;"><?php echo $langs->trans("InstallingInstance") ?><br><?php echo $langs->trans("PleaseWait") ?><br></font>
    <img id="waitMaskImg" width="100px" src="<?php echo 'ajax-loader.gif'; ?>" alt="Loading" />
</div>

    <style>
      #logo { text-align: center;margin-bottom:20px;max-width:300px; }
      .block.small {
        width: 500px;
        min-width: 500px;
        margin-top: 20px;
      }
      .block.medium {
        width: 700px;
        min-width: 700px;
        margin-top: 20px;
      }
      .signup { margin: 0 auto; width: 700px; }

    div#waitMask {
        text-align: center;
        z-index: 999;
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 100%;
        cursor: wait;
        padding-top: 250px;
        background-color: #000;
        opacity: 0;
        transition-duration: 0.5s;
        -webkit-transition-duration: 0.5s;
    }
    </style>

    <div class="signup">

      <div style="text-align: center; height: 80px">
        <?php
        $logo = 'dolicloud_logo.png';
        if (GETPOST('partner','alpha'))
        {
            $tmpthirdparty = new Societe($db);
            $result = $tmpthirdparty->fetch(0, GETPOST('partner','alpha'));
            $logo = $tmpthirdparty->logo;
        }
        print '<img style="center" class="logoheader"  src="'.$logo.'" id="logo" />';
        ?>
      </div>
      <div class="block medium">

        <style>
      	div.block .content section {
      		padding-top: 0em;
      		padding-bottom: 0em;
      	}
      	select .chzn-select{
      		margin-right: 20px;
      	}
      	</style>
        <header class="inverse">
          <h1>Inscription <small><?php echo ($tmpproduct->label?'('.$tmpproduct->label.')':''); ?></h1>
        </header>


      <form action="register_processing.php" method="post" id="formregister">
        <div class="form-content">
          <input type="hidden" name="planCode" value="basic" />
          <input type="hidden" name="cpCode" value="" />
          <section id="enterUserAccountDetails">


            <div class="control-group  required">
            	<label class="control-label" for="orgName" trans="1"><?php echo $langs->trans("NameOfCompany") ?></label>
            	<div class="controls">
            		<input type="text" name="orgName" value="<?php echo GETPOST('orgName','alpha'); ?>" required="" maxlength="250" id="orgName" />
            	</div>
            </div>

            <div class="control-group  required">
            	<label class="control-label" for="username" trans="1"><?php echo $langs->trans("Email") ?></label>
            	<div class="controls">
            		<input type="text" name="username" value="" required="" id="username" />

            	</div>
            </div>

          <div class="group">
                <div class="horizontal-fld">

                <div class="control-group  required">
                	<label class="control-label" for="password" trans="1"><?php echo $langs->trans("Password") ?></label>
                	<div class="controls">

                        <input name="password" type="password" required />

                	</div>
                </div>

                </div>
                <div class="horizontal-fld">
                  <div class="control-group required">
                    <label class="control-label" for="password2" trans="1"><?php echo $langs->trans("ConfirmPassword") ?></label>
                    <div class="controls">
                      <input name="password2" type="password" required />
                    </div>
                  </div>
                </div>
              </div>

            <hr />




<div class="control-group  ">
	<label class="control-label" for="address.country"><?php echo $langs->trans("Country") ?></label>
	<div class="controls">
<?php
$countryselected=dolGetCountryCodeFromIp($_SERVER["REMOTE_ADDR"]);
print '<!-- Autodetected IP/Country: '.$_SERVER["REMOTE_ADDR"].'/'.$countryselected.' -->'."\n";
if (empty($countryselected)) $countryselected='US';
print $form->select_country($countryselected, 'address.country', 'optionsValue="name"', 0, 'minwidth300', 'code2');
?>
	</div>
</div>


          </section>

          <hr/>

          <section id="selectDomain">
            <div class="fld select-domain required">
              <label trans="1"><?php echo $langs->trans("ChooseANameForYourApplication") ?></label>
              <div class="linked-flds">
                <input class="sldAndSubdomain" type="text" name="sldAndSubdomain" value="" maxlength="29" />
                <select name="tld.id" style="width:20em" id="tld.id" >
                    <option value="23" >.with.dolicloud.com</option>
                </select>
                <br class="unfloat" />
              </div>
            </div>
          </section>



          <section id="formActions">
          <p style="font-size:0.9em;color:#444;margin:10px 0;" trans="1"><?php echo $langs->trans("WhenRegisteringYouAccept", 'https://www.dolicloud.com/en/terms-and-conditions') ?></p>
          <div class="form-actions">
              <input type="submit" name="submit" value="<?php echo $langs->trans("SignMeUp") ?>" class="btn btn-primary" id="submit" />
          </div>
         </section>
       </div> <!-- end form-content -->
     </form>


      </div>
    </div>




<script type="text/javascript" language="javascript">
    function applyDomainConstraints( domain )
    {
        domain = domain.replace(/ /g,"");
        domain = domain.replace(/\W/g,"");
        domain = domain.replace(/\_/g,"");
        domain = domain.toLowerCase();
        if (!isNaN(domain)) {
          return ""
        }
        while ( domain.length >1 && !isNaN( domain.charAt(0))  ){
          domain=domain.substr(1)
        }
        return domain
    }

    jQuery(document).ready(function() {

        /* Autofill the domain */
        jQuery("[name=orgName]").change(function(){
        	dn = applyDomainConstraints( $(this).val() )
    	    	$("[name=sldAndSubdomain]").val( applyDomainConstraints( $(this).val() ) );
        });


        /* Sow hourglass */
        $('#formregister').submit(function() {
                console.log("We clicked on submit")
                jQuery(document.body).css({ 'cursor': 'wait' });
                jQuery("div#waitMask").show();
                jQuery("#waitMask").css("opacity"); // must read it first
                jQuery("#waitMask").css("opacity", "0.5");
                return true;
        });
	});
</script>


<?php

llxFooter();
$db->close();

