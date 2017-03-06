<?php

include_once(DIR_SYSTEM . 'library/simple/simple_controller.php');

class ControllerExtensionModuleSimple extends SimpleController{
    protected $_error = array(  );
    protected $_settings = null;
    protected $_language = null;
    protected $_templateData = array(  );
    protected $_opencartVersion = false;
    protected $simple = null;
    private $settingName = 'simple';

    function install() {
        $this->load->model( 'setting/setting' );
        $this->load->model( 'setting/store' );
        $this->load->model( 'extension/module/simple' );
        $stores = $this->getStores(  );

        $this->loadDefaultSettings();

        $settings = array(
            'simple_settings' => $this->_settings,
            'simple_address_format' => '{firstname} {lastname}, {city}, {address_1}',
            'simple_replace_cart' => false,
            'simple_replace_checkout' => false,
            'simple_replace_register' => false,
            'simple_replace_edit' => false,
            'simple_replace_address' => false,
            'simple_module' => array(  ),
            'simple_license' => '' );
        foreach ($stores as $key => $value) {
            $this->model_setting_setting->editSetting($this->settingName, $settings, $value['store_id']);
        }

        $this->db->query( 'ALTER TABLE `' . DB_PREFIX . 'setting` CHANGE `value` `value` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL' );
        $this->model_extension_module_simple->createTableForCustomerFields(  );
        $this->model_extension_module_simple->createTableForAddressFields(  );
        $this->model_extension_module_simple->createTableForOrderFields(  );
        return ;
    }

    function index()
    {
        $this->loadCore();
        $storeId = $this->getStoreId();
        $this->_templateData['store_id'] = $storeId;
        $this->loadLanguage('localisation/country');
        $this->loadLanguage('extension/module/simple');

        $this->_templateData['success'] = '';

        if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate())) {


            if(!empty($this->request->files['import']) && $this->request->files['import']['size']>0){
                print '<pre>';
                print_r($this->request->files);
                die('Implement file loading');
            }
            else {

                $data = array();
                if (!empty($this->request->post['simple_settings'])) {
                    $data['simple_settings'] = htmlspecialchars_decode($this->request->post['simple_settings']);
                }

                if (!empty($this->request->post['simple_address_format'])) {
                    $data['simple_address_format'] = $this->request->post['simple_address_format'];
                }

                if (!empty($this->request->post['simple_replace_cart'])) {
                    $data['simple_replace_cart'] = $this->request->post['simple_replace_cart'];
                }

                if (!empty($this->request->post['simple_replace_checkout'])) {
                    $data['simple_replace_checkout'] = $this->request->post['simple_replace_checkout'];
                }

                if (!empty($this->request->post['simple_replace_register'])) {
                    $data['simple_replace_register'] = $this->request->post['simple_replace_register'];
                }

                if (!empty($this->request->post['simple_replace_edit'])) {
                    $data['simple_replace_edit'] = $this->request->post['simple_replace_edit'];
                }

                if (!empty($this->request->post['simple_replace_address'])) {
                    $data['simple_replace_address'] = $this->request->post['simple_replace_address'];
                }

                $this->load->model( 'setting/setting' );
                $settings = $this->model_setting_setting->getSetting( $this->settingName, $this->getStoreId() );

                $data = array_merge($settings, $data);

                /*print '<pre>';
                print_r($data);
                die('Implement saving');*/

                $this->model_setting_setting->editSetting($this->settingName, $data, $this->getStoreId());
                $this->_settings = null;
                $this->loadSavedSettings();
            }
            $this->_templateData['success'] = $this->language->get('text_success');


            /*json_decode;

            if (!) {
                @htmlspecialchars_decode($this->request->post['simple_settings']);
                @((true ?: ), true );
                $decoded = ;

                if (!) {
                    htmlspecialchars_decode($this->request->post['simple_settings']);
                    array('simple_settings' => (true ?: ));

                    if (!) {
                        $this->request->post['simple_address_format'];
                        array('simple_address_format' => (true ?: ));

                        if (!) {
                            array('simple_replace_cart' => (true ?: ));

                            if (!) {
                                array('simple_replace_checkout' => (true ?: ));

                                if (!) {
                                    array('simple_replace_register' => (true ?: ));

                                    if (!) {
                                        array('simple_replace_edit' => (true ?: ));

                                        if (!) {
                                            array('simple_replace_address' => (true ?: ));

                                            if (!) {
                                                $decoded['modules'];
                                                array();
                                                $settings = array('simple_module' => (true ?: ), 'simple_license' => $this->config->get('simple_license'));
                                                ($this->files['import']['tmp_name']);
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }*/
        }


        $this->_templateData['breadcrumbs'] = array(  );
        $this->_templateData['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_home' ), 'href' => $this->url->link( 'common/home', 'token=' . $this->session->data['token'], 'SSL' ), 'separator' => false );
        $this->_templateData['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_module' ), 'href' => $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'], 'SSL' ), 'separator' => $this->language->get('text_separator') );
        $this->_templateData['breadcrumbs'][] = array( 'text' => $this->language->get( 'heading_title' ), 'href' => $this->url->link( 'extension/module/simple', 'token=' . $this->session->data['token'], 'SSL' ), 'separator' => $this->language->get('text_separator') );
        $this->_templateData['heading_title'] = $this->language->get( 'heading_title' );
        $this->_templateData['old'] = false;

        $this->_templateData['l'] = $this->_language;
        $this->_templateData['languages'] = $this->getLanguages();
        $this->_templateData['current_language'] = $this->getCurrentLanguage();
        $this->_templateData['action_language_helper'] = 'action_language_helper';

        $this->_templateData['store_url'] = HTTP_CATALOG;
        $this->_templateData['simple_settings'] = $this->_settings;
        $this->_templateData['layouts'] = $this->getLayoutes();
        $this->_templateData['information_pages'] = $this->getInformationPages();
        $this->_templateData['shipping_methods'] = $this->getShippingMethods();
        $this->_templateData['payment_methods'] = $this->getPaymentMethods();
        $this->_templateData['groups'] = $this->getCustomerGroupes();
        $this->_templateData['error_warning'] = '';
        $this->_templateData['stores'] = $this->getStores();
        $this->_templateData['action_without_store'] = 'action_without_store';
        $this->_templateData['action_cancel'] = $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'], true );
        $this->_templateData['action_main'] = $this->url->link( 'extension/module/simple', 'token=' . $this->session->data['token'], true );

        $this->_templateData['empty_shipping_methods'] = count($this->_templateData['shipping_methods'])>0 ? '' : $this->language->get('error_empty_shipping_methods');
        $this->_templateData['empty_payment_methods'] = count($this->_templateData['payment_methods'])>0 ? '' : $this->language->get('error_empty_payment_methods');
        $this->_templateData['styles_path'] = 'todo:';
        $this->_templateData['header_path'] = 'todo:';
        $this->_templateData['header_template'] = 'todo:';
        $this->_templateData['header_save_link'] = 'todo:';
        $this->_templateData['footer_path'] = 'todo:';
        $this->_templateData['footer_template'] = 'todo:';
        $this->_templateData['footer_save_link'] = 'todo:';
        $this->_templateData['action_backup'] = $this->url->link( 'extension/module/simple/backup', 'token=' . $this->session->data['token'], true );;
        $this->_templateData['entry_address_format'] = '{firstname} {lastname}, {city}, {address_1}';

        $this->_templateData['simple_tab_pages'] = $this->getTab('pages');
        $this->_templateData['simple_tab_fields'] = $this->getTab('fields');
        $this->_templateData['simple_tab_headers'] = $this->getTab('headers');
        $this->_templateData['simple_tab_integration'] = $this->getTab('integration');
        $this->_templateData['simple_tab_backup'] = $this->getTab('backup');
        $this->_templateData['simple_tab_modules'] = $this->getTab('modules');
        $this->_templateData['simple_tab_address_formats'] = $this->getTab('address_formats');


        $childrens = array();

        if (!$this->simple->isAjaxRequest()) {
            $childrens = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header',
            );

            $this->_templateData['simple_header'] = $this->simple->getLinkToHeaderTpl();
            $this->_templateData['simple_footer'] = $this->simple->getLinkToFooterTpl();
        }

        $this->setOutputContent($this->renderPage('extension/module/simple.tpl', $this->_templateData, $childrens));
    }

    function getLayoutes(){

        $this->load->model('design/layout');
        $layout_total = $this->model_design_layout->getLayouts();

        return $layout_total;
    }

    function getCustomerGroupes(){
        $this->load->model('customer/customer_group');
        $results = $this->model_customer_customer_group->getCustomerGroups();

        return $results;
    }

    function getTab($name) {

        if ($this->_opencartVersion < 200) {
            $template = new Template();
            $template->data = $this->_templateData;
            return $template->fetch( 'module/simple_tab_' . $name . '.tpl' );
        }

        return $this->load->view('extension/module/simple_tab_' . $name . '.tpl', $this->_templateData);
    }

    /*function custom() {
        $this->load->model( 'extension/module/simplecustom' );

        if (!) {
            $this->request->get['set'];
            $set = (true ?  : );

            if (!) {
                $this->request->get['id'];
                $id = (true ?  : );

                if (!) {
                    $this->request->get['object'];
                    $object = (true ?  : );
                    $this->url->link( 'module/simple/custom', 'token=' . $this->session->data['token'] . '&set=' . $set . '&object=' . $object . '&id=' . $id, 'SSL' );
                    $this->_templateData;
                }
            }

            ['action'] = ;
            if ($this->request->server['REQUEST_METHOD']  = 'POST') = ;
                                                            $this->_templateData['form_id'] = $object . '_' . str_replace( ',', '_', $set );
                                                            $this->_templateData['download'] = $this->createLink( 'module/simple/download', 'token=' . $this->session->data['token'] . '&name=', 'SSL' );
                                                            (  && isset( $this->request->server['HTTPS'] ) );
                                                            $this->request;
                                                        }

        $this->request->server['HTTPS']  = '1';
        ( $this->server['HTTPS']  = 'on' ||  );

        if ((bool)) {
            $this->_templateData['store_url'] = HTTPS_CATALOG;
        }

        jmp;
        return ;
    }*/

    function language() {
        $this->loadCore(  );

        if (isset($this->request->post['code'])) {
            $code = $this->request->post['code'];
        } else{
            $code = '';
        }

        if (isset($this->request->post['id'])) {
            $id = $this->request->post['id'];
        } else {
            $id = '';
        }

        if (isset($this->request->post['text'])) {
            $text = $this->request->post['text'];
        } else {
            $text = '';
        }

        if (( ( $code &&  $id) && $text )) {
            $this->_language->set( $code, $id, $text );
        }

        $this->_language->save(  );
        $this->response->setOutput( $text );
        return ;
    }

    function backup() {
        $this->load->model( 'setting/setting' );
        $this->load->model( 'setting/store' );
        $this->loadSavedSettings(  );
        $this->response->addHeader( 'Pragma: public' );
        $this->response->addHeader( 'Expires: 0' );
        $this->response->addHeader( 'Content-Description: File Transfer' );
        $this->response->addHeader( 'Content-Type: application/octet-stream' );
        $this->response->addHeader( 'Content-Disposition: attachment; filename=' . 'simple_store_' . $this->getStoreId(  ) . '.settings' );
        $this->response->addHeader( 'Content-Transfer-Encoding: binary' );
        $this->response->setOutput( $this->_settings );
        return ;
    }

    function header() {
        $header_content = $this->getTemplateForHeader( DIR_CATALOG . 'view/theme/' . $this->getThemeName(  ) . '/template/account/forgotten.tpl', '<form' );
        $this->response->addHeader( 'Pragma: public' );
        $this->response->addHeader( 'Expires: 0' );
        $this->response->addHeader( 'Content-Description: File Transfer' );
        $this->response->addHeader( 'Content-Type: application/octet-stream' );
        $this->response->addHeader( 'Content-Disposition: attachment; filename=simple_header.tpl' );
        $this->response->addHeader( 'Content-Transfer-Encoding: binary' );
        $this->response->setOutput( $header_content );
        return ;
    }

    function footer() {
        $footer_content = $this->getTemplateForFooter( DIR_CATALOG . 'view/theme/' . $this->getThemeName(  ) . '/template/account/forgotten.tpl', '</form>' );
        $this->response->addHeader( 'Pragma: public' );
        $this->response->addHeader( 'Expires: 0' );
        $this->response->addHeader( 'Content-Description: File Transfer' );
        $this->response->addHeader( 'Content-Type: application/octet-stream' );
        $this->response->addHeader( 'Content-Disposition: attachment; filename=simple_footer.tpl' );
        $this->response->addHeader( 'Content-Transfer-Encoding: binary' );
        $this->response->setOutput( $footer_content );
        return ;
    }

    /*function download() {
        if (isset( $this->request->get['name'] )) {
            $this->request->get['name'];
            $name = (true ?  : );

            if ($name) {
                $file = DIR_DOWNLOAD . $name;
                $mask = basename( utf8_substr( $name, 0, utf8_strrpos( $name, '.' ) ) );

                if (!) {
                    if (file_exists( $file )) {
                        header( 'Content-Type: application/octet-stream' );
                        header( 'Content-Description: File Transfer' );
                        header;

                        if ($mask) {
                            ;
                            basename( $file );
                        }
                    }
                }

                ( 'Content-Disposition: attachment; filename="' .  . '"' );
                header( 'Content-Transfer-Encoding: binary' );
                header( 'Expires: 0' );
                header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
                header( 'Pragma: public' );
            }
        }

        header( 'Content-Length: ' . filesize( $file ) );
        readfile( $file, 'rb' );
        exit(  );
    }*/

    function createLink($route, $params, $ssl = 'SSL') {
        return ;
    }

    function getStoreId() {
        $storeId = $this->config->get('config_store_id');
        if(!isset($storeId)){
            $storeId = 0;
        }
        return $storeId;
    }

    function loadCore() {
        $this->loadLanguage( 'extension/module/simple' );
        $this->load->helper( 'simple/simple' );
        $this->load->helper( 'simple/language_helper' );

        $this->_language = new LanguageHelper( $this->getLanguages(), $this->getCurrentLanguage(  ) );
        $this->simple = Simple::getInstance($this->registry, true);
        $this->_opencartVersion = $this->simple->getOpencartVersion();
        $this->loadSavedSettings();

        return true;
    }

    function loadSavedSettings() {
        if (empty( $this->_settings )) {
            $this->load->model( 'setting/setting' );
            $settings = $this->model_setting_setting->getSetting( $this->settingName, $this->getStoreId() );

            if (isset($settings)) {
                $this->_settings = $settings['simple_settings'];
            }
        }

        return ;
    }

    /*function initSettings()
    {
        if (isset($this->request->post['simple_settings'])) {
            $this->_settings = $this->loadSavedSettings();
        }

        return;
    }*/

    function getThemeName() {
        $this->load->model( 'setting/setting' );
        $config = $this->model_setting_setting->getSetting( 'config', $this->getStoreId(  ) );

        if (isset( $config['config_template'] )) {
            $config['config_template'];
        }

        return ;
    }

    function validate() {
        if (!$this->user->hasPermission( 'modify', 'extension/module/simple' )) {
            $this->_error['warning'] = $this->language->get( 'error_permission' );
        }

        return !$this->_error;
    }

    function loadLanguage($path) {
        $this->load->language($path);
    }

    function cleanTags($value) {
        if ($value) {
            $value = preg_replace( '~<script[^>]*>.*?</script>~si', '', $value );
            return strip_tags( $value );
        }
    }

    /*function getPaymentMethods() {
        while ($this->_opencartVersion < 200) {
            $this->load->model( 'setting/extension' );
        }

        jmp;
        return ;
    }*/

    function getPaymentMethods(){
        $this->load->model('extension/extension');

        $extensions = $this->model_extension_extension->getInstalled('payment');
        $results = array();

        foreach ($extensions as $extension){
            $this->loadLanguage('extension/payment/' . $extension );
            $results[] = array(
                'code' => $extension,
                'title' => array($this->getCurrentLanguage() => $this->language->get('heading_title'))
            );
        }

        return $results;
    }

    function getShippingMethods(){

        $this->load->model('extension/extension');

        $extensions = $this->model_extension_extension->getInstalled('shipping');
        $results = array();

        foreach ($extensions as $extension){
            $this->loadLanguage('extension/shipping/' . $extension );
            $results[] = array(
                'code' => $extension,
                'methods' => '',
                'title' => array($this->getCurrentLanguage() => $this->language->get('heading_title'))
            );
        }

        /*print '<pre>';
        print_r($results);
        die();

        foreach ($extensions as $key => $value) {
            if (!is_file(DIR_APPLICATION . 'controller/extension/shipping/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/shipping/' . $value . '.php')) {
                $this->model_extension_extension->uninstall('shipping', $value);

                unset($extensions[$key]);
            }
        }



        // Compatibility code for old extension folders
        $files = glob(DIR_APPLICATION . 'controller/{extension/shipping,shipping}/*.php', GLOB_BRACE);

        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('extension/shipping/' . $extension);

               $results[] = array(
                    'name'       => $this->language->get('heading_title'),
                    'status'     => $this->config->get($extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'sort_order' => $this->config->get($extension . '_sort_order'),
                    'install'    => $this->url->link('extension/extension/shipping/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
                    'uninstall'  => $this->url->link('extension/extension/shipping/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, true),
                    'installed'  => in_array($extension, $extensions),
                    'edit'       => $this->url->link('extension/shipping/' . $extension, 'token=' . $this->session->data['token'], true)
                );
            }
        }

        print '<pre>';
        print_r($results);
        die();*/

        return $results;
    }

    /*function getShippingMethods() {
        while (true) {
            while (true) {
                while (true) {
                    while (true) {
                        while (true) {
                            while (true) {
                                while ($this->_opencartVersion < 200) {
                                    $this->load->model( 'setting/extension' );
                                }

                                jmp;

                                while (true) {
                                    if (( $tmp )) {
                                        $this->loadLanguage( 'shipping/' . $extension );
                                        $result[$extension] = array( 'code' => $extension, 'title' => array( $lc => $this->language->get( 'heading_title' ) ), 'methods' => array(  ) );
                                        break;
                                    }
                                }

                                $real = $this->cache->get( 'simple_shipping_methods' );
                                $this->cache->set( 'simple_shipping_methods', $real );
                                is_array( $real );

                                if (( ! &&  )) {
                                    foreach ($real as $info) {
                                        !;

                                        if (( ! &&  )) {
                                            $result[$info['code']] = array( 'code' => $extension, 'title' => array( $lc => $this->cleanTags( $info['title'] ) ), 'methods' => array(  ) );

                                            if (!) {
                                                $result[$info['code']]['methods'] = array(  );
                                                is_array( $info['quote'] );

                                                if (( ! &&  )) {
                                                    foreach ($info['quote'] as ) {
                                                    }
                                                }

                                                break;
                                            }

                                            break 4;
                                        }
                                    }
                                }

                                $quote = ;
                                $result[$info['code']]['methods'][$quote['code']] = array( 'code' => $quote['code'], 'title' => array( $lc => $this->cleanTags( $quote['title'] ) ) );
                            }
                        }
                    }

                    $tmp = @json_decode( $this->_templateData['simple_settings'], true );
                    is_array( $tmp['checkout'] );

                    if (( ! &&  )) {
                        foreach ($tmp['checkout'] as $id => $data) {
                            is_array( $data['payment']['methods'] );

                            if (( isset( $data['payment']['methods'] ) &&  )) {
                                foreach ($data['payment']['methods'] as $info) {

                                    while (true) {
                                        is_array( $info['forMethods'] );

                                        if (( isset( $info['forMethods'] ) &&  )) {
                                            foreach ($info['forMethods'] as $key => $value) {
                                                $sid = explode( '.', $key );
                                                count( $sid )  = 2;
                                                isset( $result[$sid[0]]['methods'] );

                                                if (( ( is_array( $sid ) &&  ) &&  )) {
                                                }

                                                break 4;
                                            }

                                            break 4;
                                        }
                                    }
                                }
                            }

                            break;
                        }

                        break 3;
                    }

                    $result[$sid[0]]['methods'][$key] = array( 'code' => $key, 'title' => array( $lc => $this->cleanTags( $key ) ) );
                }
            }
        }

        return ;
    }*/

    function getStores() {
        $this->load->model('setting/store');

        $stores = array();

        $stores[] = array(
            'store_id' => 0,
            'name'     => $this->language->get('text_default')
        );

        $results = $this->model_setting_store->getStores();

        foreach ($results as $result) {
            $stores[] = array(
                'store_id' => $result['store_id'],
                'name'     => $result['name']
            );
        }

        return $stores;
    }

    function getLanguages() {
        $this->load->model( 'localisation/language' );
        $languages = $this->model_localisation_language->getLanguages(  );
        $result = array(  );
        foreach ($languages as $language) {
            if(empty($language['directory'])){
                $language['directory'] = $language['code'];
            }
            if(empty($language['image'])){
                $language['image'] = $language['code'] . '.png';
            }
            //$language['code'] = trim( str_replace( '-', '_', strtolower( $language['code'] ) ), '.' );
            $result[] = $language;
        }

        return $result;
    }

    function getCurrentLanguage() {
        return $this->config->get('config_language');
    }

    function getCountries() {
        $this->load->model( 'localisation/country' );
        return ;
    }

    function getZones($countryId) {
        $this->load->model( 'localisation/zone' );

        if ($countryId) {
            return $this->model_localisation_zone->getZonesByCountryId($countryId);
        }
        return array(  );
    }

    function getCustomerGroups() {
        $this->load->model( 'sale/customer_group' );
        return ;
    }

    function getInformationPages()
    {
        $this->load->model('catalog/information');
        $result = array();
        foreach ($this->model_catalog_information->getInformations() as $info) {
            $result[] = array(
                    'id' => $info['information_id'],
                    'title' => $info['title']);
        }

        return $result;
    }

    function getTemplateForFooter($file, $after) {
        $tpl = '';
        if (file_exists( $file )) {
            $tpl = file_get_contents( $file );
        }
        return $tpl;
    }

    function loadDefaultSettings() {
        if (empty( $this->_settings )) {
            $this->_settings = array(  );

            if (file_exists( DIR_SYSTEM . 'library/simple/simple.settings' )) {
                $this->_settings = file_get_contents( DIR_SYSTEM . 'library/simple/simple.settings' );
            }
        }

        return ;
    }
}

?>