<?
// require '../../../wp-load.php'; 
//require( dirname(__FILE__) . '/wp-load.php' );
$parse_uri = explode( 'plugins/fmrFinance/assets/paynotify.php', $_SERVER['SCRIPT_FILENAME'] );
preg_match_all('/\/.*?\//',$parse_uri[0], $math );
$wp_content = $math[0][( count($math[0]) -1)];
$pth = $parse_uri[0];
$pth = str_replace($wp_content,'',$pth);
require $pth .'/wp-load.php'; 
	global $wpdb;
$postdata="";
$file = 'paypal.log.txt';
if(!isset($_GET['auth'])) {
global $wpdb;
    foreach ($_REQUEST as $key => $value) $postdata .= $key . "=" . urlencode($value) . "&";
    $postdata .= "cmd=_notify-validate";
   // file_put_contents($file, '<!-- start -->', FILE_APPEND);
    $curl = curl_init("https://www.sandbox.paypal.com/cgi-bin/webscr");
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response != "VERIFIED") {
       file_put_contents($file, '<!-- error -->', FILE_APPEND);
        die;
    }
    // Check number 2 ------------------------------------------------------------------------------------------------------------
if ($_POST['payment_status'] != "Completed") {
    // Handle how you think you should if a payment is not complete yet, a few scenarios can cause a transaction to be incomplete
     file_put_contents($file, '<!-- error status -->', FILE_APPEND);
        die;
}
    if (isset($_POST['mc_gross'])) {
        fmr_finance_balance_change( (int)$_POST['custom'],$_POST['mc_gross'],"Reserve  #". $_POST['item_name']);
        $data = array(
            'amount' => $_POST['mc_gross'],
            'email'  => $_POST['payer_email'],
            'desc' => serialize($_POST)
        );
       $wpdb->insert( 'paypal', $data, $format );
         file_put_contents($file, '<!-- end -->', FILE_APPEND);
        }
    }
function fmr_finance_balance_change($user_id,$summ,$descr) {
	global $wpdb;
	$summ = (double)$summ;
	$cur = (double)get_user_meta($user_id,"balance",true);
	$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."transactions SET 
	    balance1 = '".$cur."',
		status = '1',
		descr='".esc_html($descr)."',
		data = '".time()."',
		summ = '".$summ."',
		user = '%s'
		",  $user_id ));
	$tid=$wpdb->insert_id;
	$newal = $cur+=$summ;
	update_user_meta( $user_id, "balance", $newal);
	$wpdb->query("UPDATE ".$wpdb->prefix."transactions SET balance2='".(double)get_user_meta($user_id,"balance",true)."' WHERE id = '$tid'");
	return $newal;
}
?>