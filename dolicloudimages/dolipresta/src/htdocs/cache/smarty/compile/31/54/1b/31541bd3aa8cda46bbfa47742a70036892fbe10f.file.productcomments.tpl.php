<?php /* Smarty version Smarty-3.1.13, created on 2013-06-19 16:32:16
         compiled from "/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/productcomments/productcomments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:125436981451c1c0f027e937-54638049%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '31541bd3aa8cda46bbfa47742a70036892fbe10f' => 
    array (
      0 => '/home/nbraud/wwwroot/prestashop/htdocs/themes/default/modules/productcomments/productcomments.tpl',
      1 => 1371647532,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '125436981451c1c0f027e937-54638049',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'productcomments_controller_url' => 0,
    'secure_key' => 0,
    'productcomments_url_rewriting_activated' => 0,
    'comments' => 0,
    'comment' => 0,
    'logged' => 0,
    'too_early' => 0,
    'allow_guests' => 0,
    'product' => 0,
    'productcomment_cover' => 0,
    'link' => 0,
    'homeSize' => 0,
    'criterions' => 0,
    'criterion' => 0,
    'id_product_comment_form' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_51c1c0f0458840_77356408',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51c1c0f0458840_77356408')) {function content_51c1c0f0458840_77356408($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/nbraud/wwwroot/prestashop/htdocs/tools/smarty/plugins/modifier.escape.php';
?>
<script type="text/javascript">
var productcomments_controller_url = '<?php echo $_smarty_tpl->tpl_vars['productcomments_controller_url']->value;?>
';
var confirm_report_message = "<?php echo smartyTranslate(array('s'=>'Are you sure you want report this comment?','mod'=>'productcomments'),$_smarty_tpl);?>
";
var secure_key = "<?php echo $_smarty_tpl->tpl_vars['secure_key']->value;?>
";
var productcomments_url_rewrite = '<?php echo $_smarty_tpl->tpl_vars['productcomments_url_rewriting_activated']->value;?>
';
</script>

<div id="idTab5">
	<div id="product_comments_block_tab">
	<?php if ($_smarty_tpl->tpl_vars['comments']->value){?>
		<?php  $_smarty_tpl->tpl_vars['comment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['comment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['comments']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['comment']->key => $_smarty_tpl->tpl_vars['comment']->value){
$_smarty_tpl->tpl_vars['comment']->_loop = true;
?>
			<?php if ($_smarty_tpl->tpl_vars['comment']->value['content']){?>
			<div class="comment clearfix">
				<div class="comment_author">
					<span><?php echo smartyTranslate(array('s'=>'Grade','mod'=>'productcomments'),$_smarty_tpl);?>
&nbsp</span>
					<div class="star_content clearfix">
					<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['name'] = "i";
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = (int)0;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] = is_array($_loop=5) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] = ((int)1) == 0 ? 1 : (int)1;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']["i"]['total']);
?>
						<?php if ($_smarty_tpl->tpl_vars['comment']->value['grade']<=$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']){?>
							<div class="star"></div>
						<?php }else{ ?>
							<div class="star star_on"></div>
						<?php }?>
					<?php endfor; endif; ?>
					</div>
					<div class="comment_author_infos">
						<strong><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['comment']->value['customer_name'], 'html', 'UTF-8');?>
</strong><br/>
						<em><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>smarty_modifier_escape($_smarty_tpl->tpl_vars['comment']->value['date_add'], 'html', 'UTF-8'),'full'=>0),$_smarty_tpl);?>
</em>
					</div>
				</div>
				<div class="comment_details">
					<p class="title_block"><?php echo $_smarty_tpl->tpl_vars['comment']->value['title'];?>
</p>
					<p><?php echo nl2br(smarty_modifier_escape($_smarty_tpl->tpl_vars['comment']->value['content'], 'html', 'UTF-8'));?>
</p>
					<ul>
						<?php if ($_smarty_tpl->tpl_vars['comment']->value['total_advice']>0){?>
							<li><?php echo smartyTranslate(array('s'=>'%1$d out of %2$d people found this review useful.','sprintf'=>array($_smarty_tpl->tpl_vars['comment']->value['total_useful'],$_smarty_tpl->tpl_vars['comment']->value['total_advice']),'mod'=>'productcomments'),$_smarty_tpl);?>
</li>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['logged']->value==1){?>
							<?php if (!$_smarty_tpl->tpl_vars['comment']->value['customer_advice']){?>
							<li><?php echo smartyTranslate(array('s'=>'Was this comment useful to you?','mod'=>'productcomments'),$_smarty_tpl);?>
<button class="usefulness_btn" data-is-usefull="1" data-id-product-comment="<?php echo $_smarty_tpl->tpl_vars['comment']->value['id_product_comment'];?>
"><?php echo smartyTranslate(array('s'=>'yes','mod'=>'productcomments'),$_smarty_tpl);?>
</button><button class="usefulness_btn" data-is-usefull="0" data-id-product-comment="<?php echo $_smarty_tpl->tpl_vars['comment']->value['id_product_comment'];?>
"><?php echo smartyTranslate(array('s'=>'no','mod'=>'productcomments'),$_smarty_tpl);?>
</button></li>
							<?php }?>
							<?php if (!$_smarty_tpl->tpl_vars['comment']->value['customer_report']){?>
							<li><span class="report_btn" data-id-product-comment="<?php echo $_smarty_tpl->tpl_vars['comment']->value['id_product_comment'];?>
"><?php echo smartyTranslate(array('s'=>'Report abuse','mod'=>'productcomments'),$_smarty_tpl);?>
</span></li>
							<?php }?>
						<?php }?>
					</ul>
				</div>
			</div>
			<?php }?>
		<?php } ?>
	<?php }else{ ?>
		<?php if (($_smarty_tpl->tpl_vars['too_early']->value==false&&($_smarty_tpl->tpl_vars['logged']->value||$_smarty_tpl->tpl_vars['allow_guests']->value))){?>
		<p class="align_center">
			<a id="new_comment_tab_btn" class="open-comment-form" href="#new_comment_form"><?php echo smartyTranslate(array('s'=>'Be the first to write your review','mod'=>'productcomments'),$_smarty_tpl);?>
 !</a>
		</p>
		<?php }else{ ?>
		<p class="align_center"><?php echo smartyTranslate(array('s'=>'No customer comments for the moment.','mod'=>'productcomments'),$_smarty_tpl);?>
</p>
		<?php }?>
	<?php }?>	
	</div>
</div>

<!-- Fancybox -->
<div style="display: none;">
	<div id="new_comment_form">
		<form action="#">
			<h2 class="title"><?php echo smartyTranslate(array('s'=>'Write your review','mod'=>'productcomments'),$_smarty_tpl);?>
</h2>
			<div class="product clearfix">
				<img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['product']->value->link_rewrite,$_smarty_tpl->tpl_vars['productcomment_cover']->value,'home_default');?>
" height="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['height'];?>
" width="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['width'];?>
" alt="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['product']->value->name, 'html', 'UTF-8');?>
" />
				<div class="product_desc">
					<p class="product_name"><strong><?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
</strong></p>
					<?php echo $_smarty_tpl->tpl_vars['product']->value->description_short;?>

				</div>
			</div>

			<div class="new_comment_form_content">
				<h2><?php echo smartyTranslate(array('s'=>'Write your review','mod'=>'productcomments'),$_smarty_tpl);?>
</h2>

				<div id="new_comment_form_error" class="error" style="display: none;">
					<ul></ul>
				</div>

				<?php if (count($_smarty_tpl->tpl_vars['criterions']->value)>0){?>
					<ul id="criterions_list">
					<?php  $_smarty_tpl->tpl_vars['criterion'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['criterion']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['criterions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['criterion']->key => $_smarty_tpl->tpl_vars['criterion']->value){
$_smarty_tpl->tpl_vars['criterion']->_loop = true;
?>
						<li>
							<label><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['criterion']->value['name'], 'html', 'UTF-8');?>
:</label>
							<div class="star_content">
								<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="1" />
								<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="2" />
								<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="3" checked="checked" />
								<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="4" />
								<input class="star" type="radio" name="criterion[<?php echo round($_smarty_tpl->tpl_vars['criterion']->value['id_product_comment_criterion']);?>
]" value="5" />
							</div>
							<div class="clearfix"></div>
						</li>
					<?php } ?>
					</ul>
				<?php }?>

				<label for="comment_title"><?php echo smartyTranslate(array('s'=>'Title','mod'=>'productcomments'),$_smarty_tpl);?>
: <sup class="required">*</sup></label>
				<input id="comment_title" name="title" type="text" value=""/>

				<label for="content"><?php echo smartyTranslate(array('s'=>'Comment','mod'=>'productcomments'),$_smarty_tpl);?>
: <sup class="required">*</sup></label>
				<textarea id="content" name="content"></textarea>

				<?php if ($_smarty_tpl->tpl_vars['allow_guests']->value==true&&$_smarty_tpl->tpl_vars['logged']->value==0){?>
				<label><?php echo smartyTranslate(array('s'=>'Your name','mod'=>'productcomments'),$_smarty_tpl);?>
: <sup class="required">*</sup></label>
				<input id="commentCustomerName" name="customer_name" type="text" value=""/>
				<?php }?>

				<div id="new_comment_form_footer">
					<input id="id_product_comment_send" name="id_product" type="hidden" value='<?php echo $_smarty_tpl->tpl_vars['id_product_comment_form']->value;?>
'></input>
					<p class="fl required"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required fields','mod'=>'productcomments'),$_smarty_tpl);?>
</p>
					<p class="fr">
						<button id="submitNewMessage" name="submitMessage" type="submit"><?php echo smartyTranslate(array('s'=>'Send','mod'=>'productcomments'),$_smarty_tpl);?>
</button>&nbsp;
						<?php echo smartyTranslate(array('s'=>'or','mod'=>'productcomments'),$_smarty_tpl);?>
&nbsp;<a href="#" onclick="$.fancybox.close();"><?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'productcomments'),$_smarty_tpl);?>
</a>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</form><!-- /end new_comment_form_content -->
	</div>
</div>
<!-- End fancybox -->
<?php }} ?>