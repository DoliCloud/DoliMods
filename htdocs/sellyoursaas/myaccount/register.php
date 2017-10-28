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


/*
 * View
 */

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
    <font size="3em" style="color:#888; font-weight: bold;">Installing your instance...<br>Please Wait.<br></font>
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
        $logo = 'https://www.dolicloud.com/templates/dolicloud/images/dolicloud_logo.png';
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
            	<label class="control-label" for="orgName" trans="1">Nom de société/institution</label>
            	<div class="controls">
            		<input type="text" name="orgName" value="<?php echo GETPOST('orgName','alpha'); ?>" required="" maxlength="250" id="orgName" />
            	</div>
            </div>

            <div class="control-group  required">
            	<label class="control-label" for="username" trans="1">Email</label>
            	<div class="controls">
            		<input type="text" name="username" value="" required="" id="username" />

            	</div>
            </div>

          <div class="group">
                <div class="horizontal-fld">

                <div class="control-group  required">
                	<label class="control-label" for="password" trans="1">Mot de passe</label>
                	<div class="controls">

                        <input name="password" type="password" required />

                	</div>
                </div>

                </div>
                <div class="horizontal-fld">
                  <div class="control-group required">
                    <label class="control-label" for="password2" trans="1">Confirmer mot de passe</label>
                    <div class="controls">
                      <input name="password2" type="password" required />
                    </div>
                  </div>
                </div>
              </div>

            <hr />




<div class="control-group  ">
	<label class="control-label" for="address.country">Pays</label>
	<div class="controls">
		<select name="address.country" optionsValue="name" id="address.country" >
<option value="AF" >Afghanistan</option>
<option value="AX" >Aland Islands</option>
<option value="AL" >Albania</option>
<option value="DZ" >Algeria</option>
<option value="AS" >American Samoa</option>
<option value="AD" >Andorra</option>
<option value="AO" >Angola</option>
<option value="AI" >Anguilla</option>
<option value="AQ" >Antartica</option>
<option value="AG" >Antigua and Barbuda</option>
<option value="AR" >Argentina</option>
<option value="AM" >Armenia</option>
<option value="AW" >Aruba</option>
<option value="AU" >Australia</option>
<option value="AT" >Austria</option>
<option value="AZ" >Azerbaijan</option>
<option value="BS" >Bahamas</option>
<option value="BH" >Bahrain</option>
<option value="BD" >Bangladesh</option>
<option value="BB" >Barbados</option>
<option value="BY" >Belarus</option>
<option value="BE" >Belgium</option>
<option value="BZ" >Belize</option>
<option value="BJ" >Benin</option>
<option value="BM" >Bermuda</option>
<option value="BT" >Bhutan</option>
<option value="BO" >Bolivia</option>
<option value="BQ" >Bonaire, Sint Eustatius and Saba</option>
<option value="BA" >Bosnia and Herzegovina</option>
<option value="BW" >Botswana</option>
<option value="BV" >Bouvet Island</option>
<option value="BR" >Brazil</option>
<option value="IO" >British Indian Ocean Territory</option>
<option value="BN" >Brunei</option>
<option value="BG" >Bulgaria</option>
<option value="BF" >Burkina Faso</option>
<option value="MM" >Burma (Myanmar)</option>
<option value="BI" >Burundi</option>
<option value="KH" >Cambodia</option>
<option value="CM" >Cameroon</option>
<option value="CA" >Canada</option>
<option value="CV" >Cape Verde</option>
<option value="KY" >Cayman Islands</option>
<option value="CF" >Central African Republic</option>
<option value="TD" >Chad</option>
<option value="CL" >Chile</option>
<option value="CN" >China</option>
<option value="CX" >Christmas Island</option>
<option value="CC" >Cocos (Keeling) Islands</option>
<option value="CO" >Colombia</option>
<option value="KM" >Comoros</option>
<option value="CG" >Congo</option>
<option value="CD" >Congo (Democratic Republic)</option>
<option value="CK" >Cook Islands</option>
<option value="CR" >Costa Rica</option>
<option value="HR" >Croatia</option>
<option value="CU" >Cuba</option>
<option value="CW" >Cura&ccedil;ao</option>
<option value="CY" >Cyprus</option>
<option value="CZ" >Czech Republic</option>
<option value="DK" >Denmark</option>
<option value="DJ" >Djibouti</option>
<option value="DM" >Dominica</option>
<option value="DO" >Dominican Republic</option>
<option value="TL" >East Timor</option>
<option value="EC" >Ecuador</option>
<option value="EG" >Egypt</option>
<option value="SV" >El Salvador</option>
<option value="GQ" >Equatorial Guinea</option>
<option value="ER" >Eritrea</option>
<option value="EE" >Estonia</option>
<option value="ET" >Ethiopia</option>
<option value="FK" >Falkland Islands (Malvinas)</option>
<option value="FO" >Faroe Islands</option>
<option value="FJ" >Fiji</option>
<option value="FI" >Finland</option>
<option value="FR" >France</option>
<option value="GF" >French Guaiana</option>
<option value="PF" >French Polynesia</option>
<option value="TF" >French Southern territories</option>
<option value="GA" >Gabon</option>
<option value="GM" >Gambia</option>
<option value="GE" >Georgia</option>
<option value="DE" >Germany</option>
<option value="GH" >Ghana</option>
<option value="GI" >Gibraltar</option>
<option value="GR" >Greece</option>
<option value="GL" >Greenland</option>
<option value="GD" >Grenada</option>
<option value="GP" >Guadelupe</option>
<option value="GU" >Guam</option>
<option value="GT" >Guatemala</option>
<option value="GN" >Guinea</option>
<option value="GW" >Guinea-Bissau</option>
<option value="GY" >Guyana</option>
<option value="HT" >Haiti</option>
<option value="HM" >Heard Island And Mcdonald Islands</option>
<option value="HN" >Honduras</option>
<option value="HK" >Hong Kong</option>
<option value="HU" >Hungary</option>
<option value="IS" >Iceland</option>
<option value="IN" >India</option>
<option value="ID" >Indonesia</option>
<option value="IR" >Iran</option>
<option value="IQ" >Iraq</option>
<option value="IE" >Ireland</option>
<option value="IM" >Isle of Man</option>
<option value="IL" >Israel</option>
<option value="IT" >Italy</option>
<option value="CI" >Ivory Coast</option>
<option value="JM" >Jamaica</option>
<option value="JP" >Japan</option>
<option value="JE" >Jersey</option>
<option value="JO" >Jordan</option>
<option value="KZ" >Kazakhstan</option>
<option value="KE" >Kenya</option>
<option value="KI" >Kiribati</option>
<option value="KP" >Korea (north)</option>
<option value="KR" >Korea (south)</option>
<option value="KW" >Kuwait</option>
<option value="KG" >Kyrgyzstan</option>
<option value="LA" >Laos</option>
<option value="LV" >Latvia</option>
<option value="LB" >Lebanon</option>
<option value="LS" >Lesotho</option>
<option value="LR" >Liberia</option>
<option value="LY" >Libya</option>
<option value="LI" >Liechtenstein</option>
<option value="LT" >Lithuania</option>
<option value="LU" >Luxembourg</option>
<option value="MO" >Macao</option>
<option value="MK" >Macedonia</option>
<option value="MG" >Madagascar</option>
<option value="MW" >Malawi</option>
<option value="MY" >Malaysia</option>
<option value="MV" >Maldives</option>
<option value="ML" >Mali</option>
<option value="MT" >Malta</option>
<option value="MH" >Marshall Islands</option>
<option value="MQ" >Martinique</option>
<option value="MR" >Mauritania</option>
<option value="MU" >Mauritius</option>
<option value="YT" >Mayotte</option>
<option value="MX" >Mexico</option>
<option value="FM" >Micronesia</option>
<option value="MD" >Moldova</option>
<option value="MC" >Monaco</option>
<option value="MN" >Mongolia</option>
<option value="ME" >Montenegro</option>
<option value="MS" >Montserrat</option>
<option value="MA" >Morocco</option>
<option value="MZ" >Mozambique</option>
<option value="NA" >Namibia</option>
<option value="NR" >Nauru</option>
<option value="NP" >Nepal</option>
<option value="NL" >Netherlands</option>
<option value="NC" >New Caledonia</option>
<option value="NZ" >New Zealand</option>
<option value="NI" >Nicaragua</option>
<option value="NE" >Niger</option>
<option value="NG" >Nigeria</option>
<option value="NU" >Niue</option>
<option value="NF" >Norfolk Island</option>
<option value="MP" >Northern Mariana Islands</option>
<option value="NO" >Norway</option>
<option value="OM" >Oman</option>
<option value="PK" >Pakistan</option>
<option value="PW" >Palau</option>
<option value="PS" >Palestine, State of</option>
<option value="PA" >Panama</option>
<option value="PG" >Papua New Guinea</option>
<option value="PY" >Paraguay</option>
<option value="PE" >Peru</option>
<option value="PH" >Philippines</option>
<option value="PN" >Pitcairn</option>
<option value="PL" >Poland</option>
<option value="PT" >Portugal</option>
<option value="PR" >Puerto Rico</option>
<option value="QA" >Qatar</option>
<option value="RE" >Reunion</option>
<option value="RO" >Romania</option>
<option value="RU" >Russian Federation</option>
<option value="RW" >Rwanda</option>
<option value="SH" >Saint Helena</option>
<option value="WS" >Samoa</option>
<option value="SM" >San Marino</option>
<option value="ST" >Sao Tome and Principe</option>
<option value="SA" >Saudi Arabia</option>
<option value="SN" >Senegal</option>
<option value="RS" >Serbia</option>
<option value="SC" >Seychelles</option>
<option value="SL" >Sierra Leone</option>
<option value="SG" >Singapore</option>
<option value="SX" >Sint Maarten (Dutch part)</option>
<option value="SK" >Slovakia</option>
<option value="SI" >Slovenia</option>
<option value="SB" >Solomon Islands</option>
<option value="SO" >Somalia</option>
<option value="ZA" >South Africa</option>
<option value="GS" >South Georgia and the South Sandwich Islands</option>
<option value="ES" >Spain</option>
<option value="LK" >Sri Lanka</option>
<option value="BL" >St. Barth&eacute;lemy</option>
<option value="KN" >St. Kitts &amp; Nevis</option>
<option value="LC" >St. Lucia</option>
<option value="MF" >St. Martin (French part)</option>
<option value="PM" >St. Pierre and Miquelon</option>
<option value="VC" >St. Vincent &amp; the Grenadines</option>
<option value="SD" >Sudan</option>
<option value="SR" >Suriname</option>
<option value="SJ" >Svalbard And Jan Mayen</option>
<option value="SZ" >Swaziland</option>
<option value="SE" >Sweden</option>
<option value="CH" >Switzerland</option>
<option value="SY" >Syria</option>
<option value="TW" >Taiwan, Province of China</option>
<option value="TJ" >Tajikistan</option>
<option value="TZ" >Tanzania</option>
<option value="TH" >Thailand</option>
<option value="TG" >Togo</option>
<option value="TK" >Tokelau</option>
<option value="TO" >Tonga</option>
<option value="TT" >Trinidad &amp; Tobago</option>
<option value="TN" >Tunisia</option>
<option value="TR" >Turkey</option>
<option value="TM" >Turkmenistan</option>
<option value="TC" >Turks And Caicos Islands</option>
<option value="TV" >Tuvalu</option>
<option value="UM" >U.S. Outlying Islands</option>
<option value="VI" >U.S. Virgin Islands</option>
<option value="UG" >Uganda</option>
<option value="UA" >Ukraine</option>
<option value="AE" >United Arab Emirates</option>
<option value="GB" >United Kingdom</option>
<option value="US" selected="selected" >United States</option>
<option value="UY" >Uruguay</option>
<option value="UZ" >Uzbekistan</option>
<option value="VU" >Vanuatu</option>
<option value="VA" >Vatican City</option>
<option value="VE" >Venezuela</option>
<option value="VN" >Vietnam</option>
<option value="WF" >Wallis and Fortuna</option>
<option value="EH" >Western Shahara</option>
<option value="YE" >Yemen</option>
<option value="ZM" >Zambia</option>
<option value="ZW" >Zimbabwe</option>
</select>

	</div>
</div>


          </section>

          <hr/>

          <section id="selectDomain">
            <div class="fld select-domain required">
              <label trans="1">Choisissez un nom d'adresse d'application.</label>
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
          <p style="font-size:0.9em;color:#444;margin:10px 0;" trans="1">En vous enregistrant, vous acceptez les <a href="https://www.dolicloud.com/en/terms-and-conditions" target="_blank">Conditions d'utilisation</a></p>
          <div class="form-actions">
              <input type="submit" name="submit" value="Créer mon instance" class="btn btn-primary" id="submit" />
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

