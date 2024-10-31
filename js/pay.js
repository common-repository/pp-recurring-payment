jQuery(document).ready( function( $ ) { 
    
    $('#frm_finance_changeType').click(function(e){
       fmr_rebal();
    })
});function fmr_rebal() {	 if( jQuery('#frm_finance_changeType').is(':checked')) {                        jQuery('input[name=cmd]').val('_xclick-subscriptions');            jQuery('#sum').prop('name', 'a3');                    } else {                         jQuery('input[name=cmd]').val('_xclick');             jQuery('#sum').prop('name', 'amount');        }  }