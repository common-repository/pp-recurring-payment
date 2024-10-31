<?php
function fmr_finance_customizer_init($wp_customize) {
    //Panels
    $wp_customize->add_panel('frm_panel_finace', array(
        'title' => esc_html__('fmr Finance', 'frm'),
        'description' => esc_html__('Settings of the main page', 'frm'),
         'priority' => 5,
    ));
    /*-------------------------------Payment ------------------------------*/
  $tmp_sectionname = "fmr_finace";
    $wp_customize->add_section($tmp_sectionname . '_section', array(
        'title' => esc_html__('Payments', 'frm'),
        'priority' => 29,
        'description' =>'Shortcodes: [fmr_balance] show balance
		[fmr_paymentform] display form
[fmr_transactions] transaction table',
        'panel' => 'frm_panel_finace'));
    $tmp_settingname = $tmp_sectionname . '_user_email';
    $wp_customize->add_setting($tmp_settingname, array('default' => '', 'sanitize_callback' => 'sanitize_email'));
    $wp_customize->add_control($tmp_settingname . '_control', array(
        'label' => esc_html__(' Enter your email Paypal', 'frm'),
        'section' => $tmp_sectionname . "_section",
        'settings' => $tmp_settingname,
        'type' => 'text'
    ));
    /**/
        $tmp_settingname = $tmp_sectionname . '_layout';
        $wp_customize->add_setting($tmp_settingname, array('default' => 's1',
                'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($tmp_settingname . '_control', array(
            'label' => esc_html__(' Payments type', 'mycity'),
            'section' =>  $tmp_sectionname . "_section",
            'settings' => $tmp_settingname,
            'type' => 'select',
            'choices' => array(
                's1' => esc_html__('Once', 'mycity'),
                's2' => esc_html__('Recurring', 'mycity'),
                's3' => esc_html__('Once+Recurring', 'mycity'),
                )));
     /*currency*/      
    $tmp_settingname = $tmp_sectionname . '_currency';
    $wp_customize->add_setting($tmp_settingname, array('default' => '', 'sanitize_callback' => 'esc_html'));
    $wp_customize->add_control($tmp_settingname . '_control', array(
        'label' => esc_html__('Currency (for example USD)', 'frm'),
        'section' => $tmp_sectionname . "_section",
        'settings' => $tmp_settingname,
        'type' => 'text'
    ));
    /*mode*/
    $tmp_settingname = $tmp_sectionname . '_mode';
    $wp_customize->add_setting($tmp_settingname, array('default' => 's1', 'sanitize_callback' => 'esc_html'));
    $wp_customize->add_control($tmp_settingname . '_control', array(
        'label' =>  esc_html__('mode of payment', 'frm'),
        'section' => $tmp_sectionname . "_section",
        'settings' => $tmp_settingname,
        'type' => 'select',
        'choices' => array(
                's1' => esc_html__('test', 'mycity'),
                's2' => esc_html__('live', 'mycity')              
    )));
         
    /*time period*
    <!-- time period (D=days, W=weeks, M=months, Y=years) --->*/
    
    $tmp_settingname = $tmp_sectionname . '_timeperiod';
    $wp_customize->add_setting($tmp_settingname, array('default' => 'M', 'sanitize_callback' => 'esc_html'));
    $wp_customize->add_control($tmp_settingname . '_control', array(
        'label' =>  esc_html__('Automatic payment periods', 'frm'),
        'section' => $tmp_sectionname . "_section",
        'settings' => $tmp_settingname,
        'type' => 'select',
        'choices' => array(
                'D' => esc_html__('days', 'mycity'),
                'W' => esc_html__('weeks', 'mycity'),
                'M' => esc_html__('months', 'mycity'),   
                'Y' => esc_html__('years', 'mycity')             
    )));
         
  
}
add_action('customize_register', 'fmr_finance_customizer_init');