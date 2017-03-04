<?php

class ControllerExtensionShippingNovaPoshta extends Controller {
	protected $error = array(  );
	protected $license = null;

	function install() {
		$this->db->query( 'ALTER TABLE `' . DB_PREFIX . 'zone` CHANGE `code` `code` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL' );
		$this->db->query( 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'novaposhta_cities`
   			(`id` int(11) NOT NULL AUTO_INCREMENT,
   			`Description` varchar(200) NOT NULL,
   			`DescriptionRu` varchar(200) NOT NULL,
   			`Ref` varchar(100) NOT NULL,
   			`Area` varchar(100) NOT NULL,
   			`Delivery1` tinyint(1) NOT NULL,
   			`Delivery2` tinyint(1) NOT NULL,
   			`Delivery3` tinyint(1) NOT NULL,
   			`Delivery4` tinyint(1) NOT NULL,
   			`Delivery5` tinyint(1) NOT NULL,
   			`Delivery6` tinyint(1) NOT NULL,
   			`Delivery7` tinyint(1) NOT NULL,
   			`Conglomerates` text NOT NULL,
   			`CityID` int(11) NOT NULL,
   			UNIQUE(`id`),
   			PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1' );
		$this->db->query( 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'novaposhta_warehouses`
   			(`id` int(11) NOT NULL AUTO_INCREMENT,
   			`Description` varchar(500) NOT NULL,
   			`DescriptionRu` varchar(500) NOT NULL,
   			`Phone` varchar(100) NOT NULL,
   			`TypeOfWarehouse` varchar(100) NOT NULL,
   			`Ref` varchar(100) NOT NULL,
   			`Number` varchar(100) NOT NULL,
   			`CityRef` varchar(100) NOT NULL,
   			`CityDescription` varchar(200) NOT NULL,
   			`CityDescriptionRu` varchar(200) NOT NULL,
   			`Longitude` varchar(100) NOT NULL,
   			`Latitude` varchar(100) NOT NULL,
   			`TotalMaxWeightAllowed` varchar(100) NOT NULL,
   			`PlaceMaxWeightAllowed` varchar(100) NOT NULL,
   			`Reception` text NOT NULL,
   			`Delivery` text NOT NULL,
   			`Schedule` text NOT NULL,
   			UNIQUE(`id`),
   			PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1' );
		$this->db->query( 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'novaposhta_references`
   			(`id` int(11) NOT NULL AUTO_INCREMENT,
   			`type` varchar(100) NOT NULL,
   			`value` mediumtext NOT NULL,
   			UNIQUE(`type`),
   			PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1' );
	}

	function uninstall() {
		$this->db->query( 'DELETE FROM ' . DB_PREFIX . 'zone WHERE LENGTH(`code`) = \'36\'' );
		$this->db->query( 'ALTER TABLE `' . DB_PREFIX . 'zone` CHANGE `code` `code` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL' );
		$this->db->query( 'UPDATE `' . DB_PREFIX . 'zone` SET `status`= \'1\' WHERE `country_id` = \'220\' ' );
		$this->db->query( 'DROP TABLE `' . DB_PREFIX . 'novaposhta_cities`,  `' . DB_PREFIX . 'novaposhta_warehouses`, `' . DB_PREFIX . 'novaposhta_references`' );
		$this->cache->delete( 'zone' );
	}

	function __construct($registry) {
		parent::__construct( $registry );
		$this->load->helper( 'novaposhta' );
		$this->load->model( 'tool/ocmax' );
		$this->novaposhta = new NovaPoshta( $registry );
		$this->license = $this->model_tool_ocmax->checkLicense( $this->config->get( 'novaposhta_license' ), 'novaposhta' );

		if (!$this->license) {
			$this->novaposhta->key_api = '';
		}

	}

	function index() {
		$this->load->language( 'extension/shipping/novaposhta' );
		$this->document->setTitle( $this->language->get( 'heading_title' ) );

		if (( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate(  ) )) {
			$this->load->model( 'setting/setting' );
			$this->model_setting_setting->editSetting( 'novaposhta', $this->request->post );
			$this->session->data['success'] = $this->language->get( 'text_success' );
			$this->response->redirect( $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'], 'SSL' ) );
		}

		$data['action'] = $this->url->link( 'extension/shipping/novaposhta', 'token=' . $this->session->data['token'], 'SSL' );
		$data['cancel'] = $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'], 'SSL' );
		$data['breadcrumbs'] = array(  );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_home' ), 'href' => $this->url->link( 'common/dashboard', 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_shipping' ), 'href' => $this->url->link( 'extension/extension', 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'heading_title' ), 'href' => $this->url->link( 'extension/shipping/novaposhta', 'token=' . $this->session->data['token'], 'SSL' ) );

		if (isset( $this->error['warning'] )) {
			$data['error_warning'] = $this->error['warning'];
		}
		else {
			$data['error_warning'] = '';
		}

		$data['token'] = $this->session->data['token'];
		$data['heading_title'] = $this->language->get( 'heading_title' );
		$data['button_save_and_stay'] = $this->language->get( 'button_save_and_stay' );
		$data['button_save'] = $this->language->get( 'button_save' );
		$data['button_cancel'] = $this->language->get( 'button_cancel' );
		$data['tab_general'] = $this->language->get( 'tab_general' );
		$data['tab_database'] = $this->language->get( 'tab_database' );
		$data['tab_sending'] = $this->language->get( 'tab_sending' );
		$data['tab_support'] = $this->language->get( 'tab_support' );
		$data['column_type'] = $this->language->get( 'column_type' );
		$data['column_date'] = $this->language->get( 'column_date' );
		$data['column_amount'] = $this->language->get( 'column_amount' );
		$data['column_description'] = $this->language->get( 'column_description' );
		$data['column_action'] = $this->language->get( 'column_action' );
		$data['text_edit_p'] = $this->language->get( 'text_edit_p' );
		$data['text_enabled'] = $this->language->get( 'text_enabled' );
		$data['text_disabled'] = $this->language->get( 'text_disabled' );
		$data['text_all_zones'] = $this->language->get( 'text_all_zones' );
		$data['text_yes'] = $this->language->get( 'text_yes' );
		$data['text_no'] = $this->language->get( 'text_no' );
		$data['text_select'] = $this->language->get( 'text_select' );
		$data['text_none'] = $this->language->get( 'text_none' );
		$data['text_update'] = $this->language->get( 'text_update' );
		$data['error_update'] = $this->language->get( 'error_update' );
		$data['entry_status'] = $this->language->get( 'entry_status' );
		$data['entry_sort_order'] = $this->language->get( 'entry_sort_order' );
		$data['entry_geo_zone'] = $this->language->get( 'entry_geo_zone' );
		$data['entry_tax_class'] = $this->language->get( 'entry_tax_class' );
		$data['entry_key_api'] = $this->language->get( 'entry_key_api' );
		$data['entry_cost'] = $this->language->get( 'entry_cost' );
		$data['entry_tariff_calculation'] = $this->language->get( 'entry_tariff_calculation' );
		$data['entry_free_shipping'] = $this->language->get( 'entry_free_shipping' );
		$data['entry_delivery_period'] = $this->language->get( 'entry_delivery_period' );
		$data['entry_update_areas'] = $this->language->get( 'entry_update_areas' );
		$data['entry_update_cities'] = $this->language->get( 'entry_update_cities' );
		$data['entry_update_warehouses'] = $this->language->get( 'entry_update_warehouses' );
		$data['entry_update_references'] = $this->language->get( 'entry_update_references' );
		$data['entry_sender'] = $this->language->get( 'entry_sender' );
		$data['entry_contact_person'] = $this->language->get( 'entry_contact_person' );
		$data['entry_address'] = $this->language->get( 'entry_address' );
		$data['entry_service_type'] = $this->language->get( 'entry_service_type' );
		$data['entry_cargo_description'] = $this->language->get( 'entry_cargo_description' );
		$data['entry_weight'] = $this->language->get( 'entry_weight' );
		$data['entry_dimensions'] = $this->language->get( 'entry_dimensions' );
		$data['help_status'] = $this->language->get( 'help_status' );
		$data['help_sort_order'] = $this->language->get( 'help_sort_order' );
		$data['help_geo_zone'] = $this->language->get( 'help_geo_zone' );
		$data['help_tax_class'] = $this->language->get( 'help_tax_class' );
		$data['help_key_api'] = $this->language->get( 'help_key_api' );
		$data['help_cost'] = $this->language->get( 'help_cost' );
		$data['help_tariff_calculation'] = $this->language->get( 'help_tariff_calculation' );
		$data['help_free_shipping'] = $this->language->get( 'help_free_shipping' );
		$data['help_delivery_period'] = $this->language->get( 'help_delivery_period' );
		$data['help_update_areas'] = $this->language->get( 'help_update_areas' );
		$data['help_update_cities'] = $this->language->get( 'help_update_cities' );
		$data['help_update_warehouses'] = $this->language->get( 'help_update_warehouses' );
		$data['help_update_references'] = $this->language->get( 'help_update_references' );
		$data['help_sender'] = $this->language->get( 'help_sender' );
		$data['help_contact_person'] = $this->language->get( 'help_contact_person' );
		$data['help_address'] = $this->language->get( 'help_address' );
		$data['help_service_type'] = $this->language->get( 'help_service_type' );
		$data['help_cargo_description'] = $this->language->get( 'help_cargo_description' );
		$data['help_weight'] = $this->language->get( 'help_weight' );
		$data['help_dimensions'] = $this->language->get( 'help_dimensions' );
		$this->load->model( 'localisation/geo_zone' );
		$this->load->model( 'localisation/tax_class' );
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones(  );
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses(  );

		if (isset( $this->request->post['novaposhta_status'] )) {
			$data['novaposhta_status'] = $this->request->post['novaposhta_status'];
		}
		else {
			$data['novaposhta_status'] = $this->config->get( 'novaposhta_status' );
		}


		if (isset( $this->request->post['novaposhta_sort_order'] )) {
			$data['novaposhta_sort_order'] = $this->request->post['novaposhta_sort_order'];
		}
		else {
			$data['novaposhta_sort_order'] = $this->config->get( 'novaposhta_sort_order' );
		}


		if (isset( $this->request->post['novaposhta_geo_zone_id'] )) {
			$data['novaposhta_geo_zone_id'] = $this->request->post['novaposhta_geo_zone_id'];
		}
		else {
			$data['novaposhta_geo_zone_id'] = $this->config->get( 'novaposhta_geo_zone_id' );
		}


		if (isset( $this->request->post['novaposhta_tax_class_id'] )) {
			$data['novaposhta_tax_class_id'] = $this->request->post['novaposhta_tax_class_id'];
		}
		else {
			$data['novaposhta_tax_class_id'] = $this->config->get( 'novaposhta_tax_class_id' );
		}


		if (isset( $this->request->post['novaposhta_key_api'] )) {
			$data['novaposhta_key_api'] = $this->request->post['novaposhta_key_api'];
		}
		else {
			$data['novaposhta_key_api'] = $this->config->get( 'novaposhta_key_api' );
		}


		if (isset( $this->request->post['novaposhta_tariff_calculation'] )) {
			$data['novaposhta_tariff_calculation'] = $this->request->post['novaposhta_tariff_calculation'];
		}
		else {
			$data['novaposhta_tariff_calculation'] = $this->config->get( 'novaposhta_tariff_calculation' );
		}


		if (isset( $this->request->post['novaposhta_cost'] )) {
			$data['novaposhta_cost'] = $this->request->post['novaposhta_cost'];
		}
		else {
			$data['novaposhta_cost'] = $this->config->get( 'novaposhta_cost' );
		}


		if (isset( $this->request->post['novaposhta_free_shipping'] )) {
			$data['novaposhta_free_shipping'] = $this->request->post['novaposhta_free_shipping'];
		}
		else {
			$data['novaposhta_free_shipping'] = $this->config->get( 'novaposhta_free_shipping' );
		}


		if (isset( $this->request->post['novaposhta_delivery_period'] )) {
			$data['novaposhta_delivery_period'] = $this->request->post['novaposhta_delivery_period'];
		}
		else {
			$data['novaposhta_delivery_period'] = $this->config->get( 'novaposhta_delivery_period' );
		}


		if (isset( $this->request->post['novaposhta_sender'] )) {
			$data['novaposhta_sender'] = $this->request->post['novaposhta_sender'];
		}
		else {
			$data['novaposhta_sender'] = $this->config->get( 'novaposhta_sender' );
		}


		if (isset( $this->request->post['novaposhta_sender_city'] )) {
			$data['novaposhta_sender_city'] = $this->request->post['novaposhta_sender_city'];
		}
		else {
			$data['novaposhta_sender_city'] = $this->config->get( 'novaposhta_sender_city' );
		}


		if (isset( $this->request->post['novaposhta_sender_contact_person'] )) {
			$data['novaposhta_sender_contact_person'] = $this->request->post['novaposhta_sender_contact_person'];
		}
		else {
			$data['novaposhta_sender_contact_person'] = $this->config->get( 'novaposhta_sender_contact_person' );
		}


		if (isset( $this->request->post['novaposhta_sender_address'] )) {
			$data['novaposhta_sender_address'] = $this->request->post['novaposhta_sender_address'];
		}
		else {
			$data['novaposhta_sender_address'] = $this->config->get( 'novaposhta_sender_address' );
		}


		if (isset( $this->request->post['novaposhta_service_type'] )) {
			$data['novaposhta_service_type'] = $this->request->post['novaposhta_service_type'];
		}
		else {
			$data['novaposhta_service_type'] = $this->config->get( 'novaposhta_service_type' );
		}


		if (isset( $this->request->post['novaposhta_cargo_description'] )) {
			$data['novaposhta_cargo_description'] = $this->request->post['novaposhta_cargo_description'];
		}
		else {
			$data['novaposhta_cargo_description'] = $this->config->get( 'novaposhta_cargo_description' );
		}


		if (isset( $this->request->post['novaposhta_weight'] )) {
			$data['novaposhta_weight'] = $this->request->post['novaposhta_weight'];
		}
		else {
			$data['novaposhta_weight'] = $this->config->get( 'novaposhta_weight' );
		}


		if (isset( $this->request->post['novaposhta_dimensions_w'] )) {
			$data['novaposhta_dimensions_w'] = $this->request->post['novaposhta_dimensions_w'];
		}
		else {
			$data['novaposhta_dimensions_w'] = $this->config->get( 'novaposhta_dimensions_w' );
		}


		if (isset( $this->request->post['novaposhta_dimensions_l'] )) {
			$data['novaposhta_dimensions_l'] = $this->request->post['novaposhta_dimensions_l'];
		}
		else {
			$data['novaposhta_dimensions_l'] = $this->config->get( 'novaposhta_dimensions_l' );
		}


		if (isset( $this->request->post['novaposhta_dimensions_h'] )) {
			$data['novaposhta_dimensions_h'] = $this->request->post['novaposhta_dimensions_h'];
		}
		else {
			$data['novaposhta_dimensions_h'] = $this->config->get( 'novaposhta_dimensions_h' );
		}

		$references = $this->novaposhta->getReferences(  );

		if ($references) {
			$data['database'] = $references['database'];
			$data['senders'] = $references['senders'];
			$data['service_types'] = $references['service_types'];
		}
		else {
			$data['database'] = '';
			$data['senders'] = '';
			$data['service_types'] = '';
		}

		$data['license'] = $this->license;
		$this->document->addScript( 'https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js' );
		$this->document->addStyle( 'https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css' );
		$data['header'] = $this->load->controller( 'common/header' );
		$data['column_left'] = $this->load->controller( 'common/column_left' );
		$data['footer'] = $this->load->controller( 'common/footer' );
		$data['ocmax'] = $this->load->controller( 'tool/ocmax', 'novaposhta' );
		$this->response->setOutput( $this->load->view( 'extension/shipping/novaposhta.tpl', $data ) );
	}

	function purchase() {
		if (isset( $this->request->get['action'] )) {
			$this->load->model( 'tool/ocmax' );
			$json = $this->model_tool_ocmax->purchase(  );
			$this->response->addHeader( 'Content-Type: application/json' );
			$this->response->setOutput( json_encode( $json ) );
		}

	}

	function update() {
		if (isset( $this->request->get['type'] )) {
			$type = $this->request->get['type'];
		}

		$this->load->language( 'extension/shipping/novaposhta' );
		$amount = $this->novaposhta->update( $type );

		if ($amount) {
			$json['success'] = $this->language->get( 'text_update_success' );
			$json['amount'] = $amount;
		}
		else {
			if ($amount === false) {
				$json['error'] = $this->language->get( 'error_update' );
			}
		}

		$this->response->addHeader( 'Content-Type: application/json' );
		$this->response->setOutput( json_encode( $json ) );
	}

	function getData() {
		if (isset( $this->request->get['method'] )) {
			$method = $this->request->get['method'];
		}


		if (isset( $this->request->get['filter'] )) {
			$filter = $this->request->get['filter'];
		}

		switch ($method) {
		case 'getAddress': {
				$senders = $this->novaposhta->getReferences( 'senders' );
				$address = $this->novaposhta->getAddress( $filter );
				$warehouses = $this->novaposhta->getWarehouses( $senders[$filter]['City'], 'byRef' );
				$data = array_merge( $address, $warehouses );
				break;
			}

		case 'getContactPerson': {
				$data = $this->novaposhta->getContactPerson( $filter );
				foreach ($data as $k => $person) {
					$data[$k]['Description'] = $person['Description'] . ', ' . $person['Phones'];
				}
			}
		}

		$this->response->addHeader( 'Content-Type: application/json' );
		$this->response->setOutput( json_encode( $data ) );
	}

	function validate() {
		if (!$this->user->hasPermission( 'modify', 'extension/shipping/novaposhta' )) {
			$this->error['warning'] = $this->language->get( 'error_permission' );
		}

		return !$this->error;
	}
}

?>
