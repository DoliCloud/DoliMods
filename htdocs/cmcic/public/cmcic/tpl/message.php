<?php
/* Copyright (C) 2012      Mikael Carlavan        <mcarlavan@qis-network.com>
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
 */

/**
 *     	\file       htdocs/public/cmcic/tpl/message.php
 *		\ingroup    cmcic
 */
 
if (empty($conf->cmcic->enabled)) 
    exit;

header('Content-type: text/html; charset=utf-8');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta name="robots" content="noindex,nofollow" />
    <title><?php echo $langs->trans('CMCIC_PAYMENT_FORM_TITLE'); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo DOL_URL_ROOT.$conf->css.'?lang='.$langs->defaultlang; ?>" />
    <style type="text/css">
        body{
            width : 50%; 
            margin: auto;
            text-align : center;
        }
        
        #logo{
            width : 100%;
            margin : 30px 0px 30px 0px;
        }       

        #payment-content{
            width : 100%;
            text-align : left;
        }
        
        #payment-table{
            width : 100%;
            text-align : left;
            border : 1px solid #000;            
        }

        #payment-table tr{
            width : 100%;
        }        
        
        .liste_total{
            text-align : left;
        }
        
        .payment-row-left{
            width : 40%;
            text-align : left;

        }
        
        .payment-row-right{
            width : 60%;
            text-align : right;
        } 
        
        .payment-button{
            text-align : right;  
        }                 
    </style>
</head>

<body>
    <div id="logo">
        <?php if (!empty($urlLogo)) { ?>    
            <img id="paymentlogo" title="<?php echo $societyName; ?>" src="<?php echo $urlLogo; ?>" />
        <?php } ?>        
    </div>
    <div id="payment-content">
        <h1><?php echo $welcomeTitle; ?></h1>
        <p><?php echo $msg; ?></p>
    </div>
</body>
</html>

