<?php
/*
Plugin Name: Paypal Recurring Payment
Plugin URI: 
Description: Paypal gateway and recurring payment
Version: 1.01
Author: Victor Lerner
Author URI: 
License: 
*/
/* 
[fmr_balance] show balance <br>
[fmr_paymentform] display form <br>
[fmr_transactions] transaction table
*/
define("FRM_FINACE_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
define("FRM_FINACE_PLUGIN_DIR_URL", plugin_dir_url( __FILE__ ));
require FRM_FINACE_PLUGIN_DIR_PATH . '/assets/customizer.php';

register_activation_hook( __FILE__, 'fmr_finance_inslall');
function fmr_finance_inslall() {
	global $wpdb;
	$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."transactions` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `data` int(11) NOT NULL,
              `json` text NOT NULL,
              `descr` varchar(300) NOT NULL,
              `user` int(11) NOT NULL,
              `summ` double NOT NULL,
			  `balance1` double NOT NULL,
			  `balance2` double NOT NULL,
              `status` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	 $wpdb->query($sql);
}

///enqueue script style
add_action('wp_enqueue_scripts', 'fmr_finace_print_my_inline_script', 500);
function fmr_finace_print_my_inline_script() {
    wp_enqueue_script('fmr_finace_pay', plugins_url( '/js/pay.js', __FILE__ ) , array('jquery'), 1, true);
  wp_enqueue_style('fmr_finace_pay_css', plugins_url( '/css/fmrFinance.css', __FILE__ ));

}


///shortcode
add_shortcode('fmr_balance', 'fmr_finance_balance');
add_shortcode('fmr_paymentform', 'fmr_finance_paymentform');
add_shortcode('fmr_transactions', 'fmr_finance_transactions');
function fmr_finance_balance($atts){
    //get curent user balance
    $balance = get_user_meta(get_current_user_id(),"balance",true);
	if (!isset($balance)) update_user_meta(get_current_user_id(),"balance",0);
	?>
	<h1><?php esc_html_e("Your balance","mycity"); ?>: <span><?php echo (double)get_user_meta(get_current_user_id(),"balance",true); ?> USD</span></h1>
  <?php
}
function fmr_finance_paymentform($atts){
	global $_GET;
    ?>
<?php
/* 
 's1'=> 'Once'
 's2' => 'Recurring'
 's3' =>'Once+Recurring'
*/ 
 $Payments_type  = sanitize_text_field(get_theme_mod('fmr_finace_layout','s1'));
 $mode_payment =  (get_theme_mod('fmr_finace_mode','s1') == 's1') ? 'sandbox.' : '';

 ?>
<div class="fill_up clearfix">
						<div class="fill-up-left">
							<div class="row">
<form  id="fmfill" name="_xclick" id="paypal" action="https://www.<?php  echo esc_html($mode_payment); ?>paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="<?php echo ($Payments_type == 's2' || (isset($_GET['type']) && $_GET['type'] == 'recurring')) ? '_xclick-subscriptions' :  '_xclick'; ?>">
<input type="hidden" name="business" value="<?php echo esc_html(get_theme_mod('fmr_finace_user_email','i448539-facilitator@gmail.com')); ?> "> <!-- Вот здесь указать свой ящик --->
<input type="hidden" name="currency_code" value="<?php echo esc_html(get_theme_mod('fmr_finace_currency','RUB')); ?>"><!-- Валюта --->
<input type="hidden" name="no_shipping" value="1">
<input type="hidden" value="<?php echo esc_url(get_the_permalink().'?payment=ok');  ?>" name="return" />
<input type="hidden" name="cancel_return" value="<?php echo esc_url(FRM_FINACE_PLUGIN_DIR_URL.'assets');?>/paycancel.php" />
<input type="hidden" name="notify_url" value="<?php  echo esc_url(FRM_FINACE_PLUGIN_DIR_URL.'assets');?>/paynotify.php" />
<!--id users-->
<input type="hidden" name="custom" value="<?php echo esc_html(get_current_user_id()); ?>"/>
<input name="item_name" type="hidden" value="<?php echo $_SERVER['HTTP_HOST'];?> payment" /> <!-- description  --->
	<input type="number" id="sum" name="<?php echo ($Payments_type == 's2') ? 'a3' :  'amount'; ?>"
     value="<?php echo (isset($_GET['summ'])) ? esc_attr($_GET['summ']) : "20"; ?>"  class="amount">
<br />
<?php 
if ( $Payments_type != 's1'): ?>
<input id="frm_finance_changeType" style="display: inline-block ;" type="checkbox"  
<?php  echo ($Payments_type == 's2' || (isset($_GET['type']) && $_GET['type'] == 'recurring')) ? 'checked="checked" disabled="disabled"': ""; ?> />
<label for="frm_finance_changeType">- <?php echo esc_html_e('recurrent payment','fr.'); ?></label>  
<?php endif; ?>
<br />
<input type='submit' class='' name='' value='<?php esc_html_e("Pay","fmr");?>'>
<input type="hidden" name="p3" value="1"> <!-- ck months will cost (1 = all)-->
<input type="hidden" name="t3" value="<?php echo esc_attr(get_theme_mod('fmr_finace_timeperiod','M')) ?>"> <!-- time period (D=days, W=weeks, M=months, Y=years) --->
<input type="hidden" name="src" value="1"> 
<input type="hidden" name="sra" value="1">
</form>

<script>
jQuery(document).ready(function(){
	<?php 
	
	if (isset($_GET['autopay'])) {
		?>
		fmr_rebal();
		jQuery("#fmfill").submit();<?php
	}
	?>
});
</script>
								
							</div>
						</div>
					</div>
    <?php
}
function fmr_finance_transactions($atts){
    ?>
    <div class="" >
    <h3> <?php echo esc_html_e('Latest actions','frm'); ?></h3>
				    <table class='table table-condensed'>
						<thead>
							<td class="t_id">#</td>
							<td> <?php echo esc_html_e('Date','frm'); ?></td>
							<td> <?php echo esc_html_e('Amount','frm'); ?></td>
							<td> <?php echo esc_html_e('Balance','frm'); ?></td>
							<td class="t_descr"> <?php echo esc_html_e('Descriptions','frm'); ?></td>
							<td class="t_stat"> <?php echo esc_html_e('Status','frm'); ?></td>
						</thead>
						<!---->
						<?php 
                        global $wpdb;
                        $tb = $wpdb->prefix."transactions";
						$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM  $tb WHERE user = '%s'",    
						 	get_current_user_id()
							));
							foreach ($results as $v) {
							?>
							<tr>
								<td><?php echo  esc_html($v->id);?></td>
								<td><?php echo date(get_option('date_format'),$v->data);?></td>
								<td><strong><?php echo  esc_html($v->summ); ?></strong></td>
								<td><strong><?php echo  esc_html($v->balance2); ?></strong></td>
								<td><?php echo esc_html($v->descr); ?></td>
								<td class="t_statys<?php  echo esc_attr($v->status); ?>"><i class="fa fa-circle"></i><strong><?php echo esc_attr($v->status); ?></strong></td>
							</tr>
							<?php } ?>
					</table>
                </div>
					<?php
}