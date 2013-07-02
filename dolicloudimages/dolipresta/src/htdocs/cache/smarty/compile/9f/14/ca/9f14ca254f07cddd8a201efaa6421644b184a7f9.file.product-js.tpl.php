<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:12
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/product-js.tpl" */ ?>
<?php /*%%SmartyHeaderCode:209414369451c1c0ec15ad62-35877430%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9f14ca254f07cddd8a201efaa6421644b184a7f9' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/mobile/product-js.tpl',
      1 => 1371647178,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '209414369451c1c0ec15ad62-35877430',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'currencySign' => 0,
    'currencyRate' => 0,
    'currencyFormat' => 0,
    'currencyBlank' => 0,
    'tax_rate' => 0,
    'product' => 0,
    'groups' => 0,
    'display_qties' => 0,
    'allow_oosp' => 0,
    'key_specific_price' => 0,
    'specific_price_value' => 0,
    'group_reduction' => 0,
    'ecotaxTax_rate' => 0,
    'last_qties' => 0,
    'no_tax' => 0,
    'priceDisplay' => 0,
    'restricted_country_mode' => 0,
    'PS_CATALOG_MODE' => 0,
    'cover' => 0,
    'productPriceWithoutReduction' => 0,
    'productPrice' => 0,
    'img_ps_dir' => 0,
    'customizationFields' => 0,
    'field' => 0,
    'imgIndex' => 0,
    'textFieldIndex' => 0,
    'key' => 0,
    'pictures' => 0,
    'img_prod_dir' => 0,
    'combinationImages' => 0,
    'combinationId' => 0,
    'combination' => 0,
    'image' => 0,
    'images' => 0,
    'combinations' => 0,
    'idCombination' => 0,
    'attributesCombinations' => 0,
    'aC' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0ec57a9b9_75671336',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0ec57a9b9_75671336')) {function content_51c1c0ec57a9b9_75671336($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.date_format.php';
?>

<script type="text/javascript">
// <![CDATA[
function initProductPage()
{
	// PrestaShop internal settings
	ProductFn.currencySign = '<?php echo html_entity_decode($_smarty_tpl->tpl_vars['currencySign']->value,2,"UTF-8");?>
';
	ProductFn.currencyRate = '<?php echo floatval($_smarty_tpl->tpl_vars['currencyRate']->value);?>
';
	ProductFn.currencyFormat = '<?php echo intval($_smarty_tpl->tpl_vars['currencyFormat']->value);?>
';
	ProductFn.currencyBlank = '<?php echo intval($_smarty_tpl->tpl_vars['currencyBlank']->value);?>
';
	ProductFn.taxRate = <?php echo floatval($_smarty_tpl->tpl_vars['tax_rate']->value);?>
;
	
	// Parameters
	ProductFn.id_product = '<?php echo intval($_smarty_tpl->tpl_vars['product']->value->id);?>
';
	<?php if (isset($_smarty_tpl->tpl_vars['groups']->value)){?>ProductFn.productHasAttributes = true;<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['display_qties']->value==1){?>ProductFn.quantitiesDisplayAllowed = true;<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['display_qties']->value==1&&$_smarty_tpl->tpl_vars['product']->value->quantity){?>ProductFn.quantityAvailable = <?php echo $_smarty_tpl->tpl_vars['product']->value->quantity;?>
;<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['allow_oosp']->value==1){?>ProductFn.allowBuyWhenOutOfStock = true<?php }?>;
		ProductFn.availableNowValue = '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->available_now, 'quotes', 'UTF-8');?>
';
		ProductFn.availableLaterValue = '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->available_later, 'quotes', 'UTF-8');?>
';
		ProductFn.productPriceTaxExcluded = <?php echo (($tmp = @$_smarty_tpl->tpl_vars['product']->value->getPriceWithoutReduct(true))===null||$tmp==='' ? 'null' : $tmp);?>
 - <?php echo $_smarty_tpl->tpl_vars['product']->value->ecotax;?>
;
	<?php if ($_smarty_tpl->tpl_vars['product']->value->specificPrice&&$_smarty_tpl->tpl_vars['product']->value->specificPrice['reduction']&&$_smarty_tpl->tpl_vars['product']->value->specificPrice['reduction_type']=='percentage'){?>
		ProductFn.reduction_percent = <?php echo $_smarty_tpl->tpl_vars['product']->value->specificPrice['reduction']*100;?>
;
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['product']->value->specificPrice&&$_smarty_tpl->tpl_vars['product']->value->specificPrice['reduction']&&$_smarty_tpl->tpl_vars['product']->value->specificPrice['reduction_type']=='amount'){?>
		ProductFn.reduction_price = <?php echo floatval($_smarty_tpl->tpl_vars['product']->value->specificPrice['reduction']);?>
;
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['product']->value->specificPrice&&$_smarty_tpl->tpl_vars['product']->value->specificPrice['price']){?>
		ProductFn.specific_price = <?php echo $_smarty_tpl->tpl_vars['product']->value->specificPrice['price'];?>
;
	<?php }?>
	<?php  $_smarty_tpl->tpl_vars['specific_price_value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['specific_price_value']->_loop = false;
 $_smarty_tpl->tpl_vars['key_specific_price'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['product']->value->specificPrice; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['specific_price_value']->key => $_smarty_tpl->tpl_vars['specific_price_value']->value){
$_smarty_tpl->tpl_vars['specific_price_value']->_loop = true;
 $_smarty_tpl->tpl_vars['key_specific_price']->value = $_smarty_tpl->tpl_vars['specific_price_value']->key;
?>
		ProductFn.product_specific_price['<?php echo $_smarty_tpl->tpl_vars['key_specific_price']->value;?>
'] = '<?php echo $_smarty_tpl->tpl_vars['specific_price_value']->value;?>
';
	<?php } ?>
	
	<?php if ($_smarty_tpl->tpl_vars['product']->value->specificPrice&&$_smarty_tpl->tpl_vars['product']->value->specificPrice['id_currency']){?>
		ProductFn.specific_currency = true;
	<?php }?>
	ProductFn.group_reduction = '<?php echo $_smarty_tpl->tpl_vars['group_reduction']->value;?>
';
	ProductFn.default_eco_tax = <?php echo $_smarty_tpl->tpl_vars['product']->value->ecotax;?>
;
	ProductFn.ecotaxTax_rate = <?php echo $_smarty_tpl->tpl_vars['ecotaxTax_rate']->value;?>
;
	ProductFn.currentDate = '<?php echo smarty_modifier_date_format(time(),'%Y-%m-%d %H:%M:%S');?>
';
	ProductFn.maxQuantityToAllowDisplayOfLastQuantityMessage = <?php echo $_smarty_tpl->tpl_vars['last_qties']->value;?>
;
	<?php if ($_smarty_tpl->tpl_vars['no_tax']->value==1){?>ProductFn.noTaxForThisProduct = true;<?php }?>
	ProductFn.displayPrice = <?php echo $_smarty_tpl->tpl_vars['priceDisplay']->value;?>
;
	ProductFn.productReference = '<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->reference, 'htmlall', 'UTF-8');?>
';
	ProductFn.productAvailableForOrder = <?php if ((isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&$_smarty_tpl->tpl_vars['restricted_country_mode']->value)||$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>'0'<?php }else{ ?>'<?php echo $_smarty_tpl->tpl_vars['product']->value->available_for_order;?>
'<?php }?>;
	<?php if (!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value){?>ProductFn.productShowPrice = '<?php echo $_smarty_tpl->tpl_vars['product']->value->show_price;?>
';<?php }?>
	ProductFn.productUnitPriceRatio = '<?php echo $_smarty_tpl->tpl_vars['product']->value->unit_price_ratio;?>
';
	<?php if (isset($_smarty_tpl->tpl_vars['cover']->value['id_image_only'])){?>ProductDisplay.idDefaultImage = <?php echo $_smarty_tpl->tpl_vars['cover']->value['id_image_only'];?>
;<?php }?>
	
	<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value||$_smarty_tpl->tpl_vars['priceDisplay']->value==2){?>
		<?php $_smarty_tpl->tpl_vars['productPrice'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value->getPrice(true,@constant('NULL'),2), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['productPriceWithoutReduction'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value->getPriceWithoutReduct(false,@constant('NULL')), null, 0);?>
	<?php }elseif($_smarty_tpl->tpl_vars['priceDisplay']->value==1){?>
		<?php $_smarty_tpl->tpl_vars['productPrice'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value->getPrice(false,@constant('NULL'),2), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['productPriceWithoutReduction'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value->getPriceWithoutReduct(true,@constant('NULL')), null, 0);?>
	<?php }?>
	
	ProductFn.productPriceWithoutReduction = '<?php echo $_smarty_tpl->tpl_vars['productPriceWithoutReduction']->value;?>
';
	ProductFn.productPrice = '<?php echo $_smarty_tpl->tpl_vars['productPrice']->value;?>
';
	
	// Customizable field
	ProductFn.img_ps_dir = '<?php echo $_smarty_tpl->tpl_vars['img_ps_dir']->value;?>
';
	<?php $_smarty_tpl->tpl_vars['imgIndex'] = new Smarty_variable(0, null, 0);?>
	<?php $_smarty_tpl->tpl_vars['textFieldIndex'] = new Smarty_variable(0, null, 0);?>
	<?php  $_smarty_tpl->tpl_vars['field'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customizationFields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['customizationFields']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['field']->key => $_smarty_tpl->tpl_vars['field']->value){
$_smarty_tpl->tpl_vars['field']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['customizationFields']['index']++;
?>
		<?php $_smarty_tpl->tpl_vars["key"] = new Smarty_variable("pictures_".((string)$_smarty_tpl->tpl_vars['product']->value->id)."_".((string)$_smarty_tpl->tpl_vars['field']->value['id_customization_field']), null, 0);?>
		ProductFn.customizationFields[<?php echo intval($_smarty_tpl->getVariable('smarty')->value['foreach']['customizationFields']['index']);?>
] = [];
		ProductFn.customizationFields[<?php echo intval($_smarty_tpl->getVariable('smarty')->value['foreach']['customizationFields']['index']);?>
][0] = '<?php if (intval($_smarty_tpl->tpl_vars['field']->value['type'])==0){?>img<?php echo $_smarty_tpl->tpl_vars['imgIndex']->value++;?>
<?php }else{ ?>textField<?php echo $_smarty_tpl->tpl_vars['textFieldIndex']->value++;?>
<?php }?>';
		ProductFn.customizationFields[<?php echo intval($_smarty_tpl->getVariable('smarty')->value['foreach']['customizationFields']['index']);?>
][1] = <?php if (intval($_smarty_tpl->tpl_vars['field']->value['type'])==0&&isset($_smarty_tpl->tpl_vars['pictures']->value[$_smarty_tpl->tpl_vars['key']->value])&&$_smarty_tpl->tpl_vars['pictures']->value[$_smarty_tpl->tpl_vars['key']->value]){?>2<?php }else{ ?><?php echo intval($_smarty_tpl->tpl_vars['field']->value['required']);?>
<?php }?>;
	<?php } ?>
	
	// Images
	ProductFn.img_prod_dir = '<?php echo $_smarty_tpl->tpl_vars['img_prod_dir']->value;?>
';
	
	<?php if (isset($_smarty_tpl->tpl_vars['combinationImages']->value)){?>
		<?php  $_smarty_tpl->tpl_vars['combination'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['combination']->_loop = false;
 $_smarty_tpl->tpl_vars['combinationId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['combinationImages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['combination']->key => $_smarty_tpl->tpl_vars['combination']->value){
$_smarty_tpl->tpl_vars['combination']->_loop = true;
 $_smarty_tpl->tpl_vars['combinationId']->value = $_smarty_tpl->tpl_vars['combination']->key;
?>
			ProductFn.combinationImages[<?php echo $_smarty_tpl->tpl_vars['combinationId']->value;?>
] = [];
			<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['combination']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f_combinationImage']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f_combinationImage']['index']++;
?>
				ProductFn.combinationImages[<?php echo $_smarty_tpl->tpl_vars['combinationId']->value;?>
][<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['f_combinationImage']['index'];?>
] = <?php echo intval($_smarty_tpl->tpl_vars['image']->value['id_image']);?>
;
			<?php } ?>
		<?php } ?>
	<?php }?>
	
	ProductFn.combinationImages[0] = [];
	<?php if (isset($_smarty_tpl->tpl_vars['images']->value)){?>
		<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['images']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f_defaultImages']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value){
$_smarty_tpl->tpl_vars['image']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['f_defaultImages']['index']++;
?>
			ProductFn.combinationImages[0][<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['f_defaultImages']['index'];?>
] = <?php echo $_smarty_tpl->tpl_vars['image']->value['id_image'];?>
;
		<?php } ?>
	<?php }?>
	
	// Translations
	ProductFn.doesntExist = '<?php echo smartyTranslate(array('s'=>'The combination does not exist for this product. Please choose another.','js'=>1),$_smarty_tpl);?>
';
	ProductFn.doesntExistNoMore = '<?php echo smartyTranslate(array('s'=>'This product is no longer in stock','js'=>1),$_smarty_tpl);?>
';
	ProductFn.doesntExistNoMoreBut = '<?php echo smartyTranslate(array('s'=>'with those attributes but is available with others','js'=>1),$_smarty_tpl);?>
';
	ProductFn.uploading_in_progress = '<?php echo smartyTranslate(array('s'=>'Uploading in progress, please wait...','js'=>1),$_smarty_tpl);?>
';
	ProductFn.fieldRequired = '<?php echo smartyTranslate(array('s'=>'Please fill in all required fields, then save your customization.','js'=>1),$_smarty_tpl);?>
';
	
	<?php if (isset($_smarty_tpl->tpl_vars['groups']->value)){?>
		// Combinations
		<?php  $_smarty_tpl->tpl_vars['combination'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['combination']->_loop = false;
 $_smarty_tpl->tpl_vars['idCombination'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['combinations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['combination']->key => $_smarty_tpl->tpl_vars['combination']->value){
$_smarty_tpl->tpl_vars['combination']->_loop = true;
 $_smarty_tpl->tpl_vars['idCombination']->value = $_smarty_tpl->tpl_vars['combination']->key;
?>
			var oSpecificPriceCombination = new SpecificPriceCombination();
			<?php if ($_smarty_tpl->tpl_vars['combination']->value['specific_price']&&$_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction']&&$_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction_type']=='percentage'){?>
				oSpecificPriceCombination.reduction_percent = <?php echo $_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction']*100;?>
;
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['combination']->value['specific_price']&&$_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction']&&$_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction_type']=='amount'){?>
				oSpecificPriceCombination.reduction_price = <?php echo $_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction'];?>
;
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['combination']->value['specific_price']&&$_smarty_tpl->tpl_vars['combination']->value['specific_price']['price']){?>
				oSpecificPriceCombination.price = <?php echo $_smarty_tpl->tpl_vars['combination']->value['specific_price']['price'];?>
;
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['combination']->value['specific_price']){?>
				oSpecificPriceCombination.reduction_type = '<?php echo $_smarty_tpl->tpl_vars['combination']->value['specific_price']['reduction_type'];?>
';
			<?php }?>
			var oCombination = new ProductCombination(<?php echo intval($_smarty_tpl->tpl_vars['idCombination']->value);?>
);
			oCombination.idsAttributes = new Array(<?php echo $_smarty_tpl->tpl_vars['combination']->value['list'];?>
);
			oCombination.quantity = <?php echo $_smarty_tpl->tpl_vars['combination']->value['quantity'];?>
;
			oCombination.price = <?php echo $_smarty_tpl->tpl_vars['combination']->value['price'];?>
;
			oCombination.ecotax = <?php echo $_smarty_tpl->tpl_vars['combination']->value['ecotax'];?>
;
			oCombination.idImage = <?php echo $_smarty_tpl->tpl_vars['combination']->value['id_image'];?>
;
			oCombination.reference = '<?php echo $_smarty_tpl->tpl_vars['combination']->value['reference'];?>
';
			oCombination.unitPrice = <?php echo $_smarty_tpl->tpl_vars['combination']->value['unit_impact'];?>
;
			oCombination.minimalQuantity = <?php echo $_smarty_tpl->tpl_vars['combination']->value['minimal_quantity'];?>
;
			oCombination.availableDate = '<?php echo $_smarty_tpl->tpl_vars['combination']->value['available_date'];?>
';
			oCombination.specific_price = oSpecificPriceCombination;
			ProductFn.combinations.push(oCombination);
			ProductFn.globalQuantity += oCombination.quantity;
		<?php } ?>
	<?php }?>
	
	<?php if (isset($_smarty_tpl->tpl_vars['attributesCombinations']->value)){?>
		// Combinations attributes informations
		<?php  $_smarty_tpl->tpl_vars['aC'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aC']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributesCombinations']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aC']->key => $_smarty_tpl->tpl_vars['aC']->value){
$_smarty_tpl->tpl_vars['aC']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['aC']->key;
?>
			var oAttributeInfos = new AttributeCombination('<?php echo intval($_smarty_tpl->tpl_vars['aC']->value['id_attribute']);?>
');
			oAttributeInfos.attribute = '<?php echo $_smarty_tpl->tpl_vars['aC']->value['attribute'];?>
';
			oAttributeInfos.group = '<?php echo $_smarty_tpl->tpl_vars['aC']->value['group'];?>
';
			oAttributeInfos.id_attribute_group = '<?php echo intval($_smarty_tpl->tpl_vars['aC']->value['id_attribute_group']);?>
';
			ProductFn.attributesCombinations.push(oAttributeInfos);
		<?php } ?>
	<?php }?>
}


//]]>
</script><?php }} ?>