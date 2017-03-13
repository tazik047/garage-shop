<?php
/**
 *
 * @ IonCube v8.3 Loader By DoraemonPT
 * @ PHP 5.3
 * @ Decoder version : 1.0.0.7
 * @ Author     : DoraemonPT
 * @ Release on : 09.05.2014
 * @ Website    : http://EasyToYou.eu
 *
 **/

class ControllerSaleNovaPoshtaEI extends Controller {
	protected $error = array(  );
	protected $license = null;

	function __construct($registry) {
		parent::__construct( $registry );
		$this->load->helper( 'novaposhta' );
		$this->load->model( 'tool/ocmax' );
		$this->novaposhta = new NovaPoshta($registry );
		$this->license = $this->model_tool_ocmax->checkLicense( $this->config->get( 'novaposhta_license' ), 'novaposhta' );

		if (!$this->license) {
			$this->novaposhta->key_api = '';
		}

	}

	function index() {
		$this->load->language( 'sale/novaposhta_ei' );
		$this->document->setTitle( $this->language->get( 'heading_title' ) );
		$this->getEIList(  );
	}

	function getEIList() {
		if (isset( $this->request->get['filter_ei_number'] )) {
			$filter_ei_number = $this->request->get['filter_ei_number'];
		}
		else {
			$filter_ei_number = null;
		}


		if (isset( $this->request->get['filter_recipient'] )) {
			$filter_recipient = $this->request->get['filter_recipient'];
		}
		else {
			$filter_recipient = null;
		}


		if (isset( $this->request->get['filter_shipment_date'] )) {
			$filter_shipment_date = $this->request->get['filter_shipment_date'];
		}
		else {
			$filter_shipment_date = null;
		}


		if (isset( $this->request->get['page'] )) {
			$page = $this->request->get['page'];
		}
		else {
			$page = 706;
		}

		$url = '';

		if (isset( $this->request->get['filter_ei_number'] )) {
			$url .= '&filter_ei_number=' . $this->request->get['filter_ei_number'];
		}


		if (isset( $this->request->get['filter_recipient'] )) {
			$url .= '&filter_recipient=' . $this->request->get['filter_recipient'];
		}


		if (isset( $this->request->get['filter_shipment_date'] )) {
			$url .= '&filter_shipment_date=' . $this->request->get['filter_shipment_date'];
		}


		if (isset( $this->request->get['page'] )) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['pdf'] = $this->url->link( 'sale/novaposhta_ei/doPDF', 'token=' . $this->session->data['token'], 'SSL' );
		$data['print'] = 'https://my.novaposhta.ua/orders';
		$data['back_to_orders'] = $this->url->link( 'sale/order', 'token=' . $this->session->data['token'], 'SSL' );
		$data['breadcrumbs'] = array(  );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_home' ), 'href' => $this->url->link( 'common/dashboard', 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_orders' ), 'href' => $this->url->link( 'sale/order', 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['token'] = $this->session->data['token'];

		if (isset( $this->session->data['success'] )) {
			$data['success'] = $this->session->data['success'];
			$data['ei'] = $this->session->data['ei'];
			unset( $this->session->data[success] );
			unset( $this->session->data[ei] );
		}
		else {
			$data['success'] = '';
			$data['ei'] = '';
		}

		$data['api_key'] = $this->novaposhta->key_api;
		$data['heading_title'] = $this->language->get( 'heading_title' );
		$data['text_list'] = $this->language->get( 'text_list' );
		$data['text_no_results'] = $this->language->get( 'text_no_results' );
		$data['column_ei_number'] = $this->language->get( 'column_ei_number' );
		$data['column_estimated_delivery_date'] = $this->language->get( 'column_estimated_delivery_date' );
		$data['column_recipient'] = $this->language->get( 'column_recipient' );
		$data['column_phone'] = $this->language->get( 'column_phone' );
		$data['column_city'] = $this->language->get( 'column_city' );
		$data['column_address'] = $this->language->get( 'column_address' );
		$data['column_state'] = $this->language->get( 'column_state' );
		$data['column_action'] = $this->language->get( 'column_action' );
		$data['entry_ei_number'] = $this->language->get( 'entry_ei_number' );
		$data['entry_recipient'] = $this->language->get( 'entry_recipient' );
		$data['entry_shipment_date'] = $this->language->get( 'entry_shipment_date' );
		$data['button_pdf'] = $this->language->get( 'button_pdf' );
		$data['button_print'] = $this->language->get( 'button_print' );
		$data['button_ei'] = $this->language->get( 'button_ei' );
		$data['button_mark'] = $this->language->get( 'button_mark' );
		$data['button_mark_zebra'] = $this->language->get( 'button_mark_zebra' );
		$data['button_back_to_orders'] = $this->language->get( 'button_back_to_orders' );
		$data['button_filter'] = $this->language->get( 'button_filter' );
		$data['documents'] = $this->novaposhta->getEIList( $filter_shipment_date );
		$documents_total = count( $data['documents'] );
		$pagination = new Pagination(  );
		$pagination->total = $documents_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get( 'config_limit_admin' );
		$pagination->url = $this->url->link( 'sale/novaposhta_ei', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL' );
		$data['pagination'] = $pagination->render(  );
		$data['results'] = sprintf( $this->language->get( 'text_pagination' ), ($documents_total ? ( $page - 1 ) * $this->config->get( 'config_limit_admin' ) + 1 : 0), ($documents_total - $this->config->get( 'config_limit_admin' ) < ( $page - 1 ) * $this->config->get( 'config_limit_admin' ) ? $documents_total : ( $page - 1 ) * $this->config->get( 'config_limit_admin' ) + $this->config->get( 'config_limit_admin' )), $documents_total, ceil( $documents_total / $this->config->get( 'config_limit_admin' ) ) );
		$data['filter_ei_number'] = $filter_ei_number;
		$data['filter_recipient'] = $filter_recipient;
		$data['filter_shipment_date'] = $filter_shipment_date;
		$data['header'] = $this->load->controller( 'common/header' );
		$data['column_left'] = $this->load->controller( 'common/column_left' );
		$data['footer'] = $this->load->controller( 'common/footer' );
		$this->response->setOutput( $this->load->view( 'sale/novaposhta_ei_list.tpl', $data ) );
	}

	function getForm() {
		$this->load->language( 'sale/novaposhta_ei' );

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$json = array(  );

			if ($this->validate(  )) {
				$json['success'] = $this->request->post;
			}
			else {
				$json = $this->error;
			}

			$this->response->addHeader( 'Content-Type: application/json' );
			$this->response->setOutput( json_encode( $json ) );
			return null;
		}

		$this->load->model( 'sale/order' );
		$this->load->model( 'sale/novaposhta_ei' );

		if (isset( $this->request->get['order_id'] )) {
			$order_id = $this->request->get['order_id'];
		}
		else {
			$order_id = 845;
		}

		$order_info = $this->model_sale_order->getOrder( $order_id );
		$order_totals = $this->model_sale_order->getOrderTotals( $order_id );
		$products = $this->model_sale_novaposhta_ei->getOrderProducts( $order_id );
		$this->document->setTitle( $this->language->get( 'heading_title' ) );
		$data['ei_list'] = $this->url->link( 'sale/novaposhta_ei', 'token=' . $this->session->data['token'], 'SSL' );
		$data['cancel'] = $this->url->link( 'sale/order', 'token=' . $this->session->data['token'], 'SSL' );
		$data['breadcrumbs'] = array(  );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_home' ), 'href' => $this->url->link( 'common/dashboard', 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_orders' ), 'href' => $this->url->link( 'sale/order', 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['breadcrumbs'][] = array( 'text' => $this->language->get( 'text_order' ), 'href' => $this->url->link( 'sale/order/info&order_id=' . $order_id, 'token=' . $this->session->data['token'], 'SSL' ) );
		$data['token'] = $this->session->data['token'];
		$data['heading_title'] = $this->language->get( 'heading_title' );
		$data['button_ei_list'] = $this->language->get( 'button_ei_list' );
		$data['button_cancel'] = $this->language->get( 'button_cancel' );
		$data['button_create'] = $this->language->get( 'button_create' );
		$data['text_select'] = $this->language->get( 'text_select' );
		$data['text_form'] = $this->language->get( 'text_form' );
		$data['text_sender'] = $this->language->get( 'text_sender' );
		$data['text_recipient'] = $this->language->get( 'text_recipient' );
		$data['text_shipment'] = $this->language->get( 'text_shipment' );
		$data['text_payment'] = $this->language->get( 'text_payment' );
		$data['text_additionally'] = $this->language->get( 'text_additionally' );
		$data['text_no_backward_delivery'] = $this->language->get( 'text_no_backward_delivery' );
		$data['entry_sender'] = $this->language->get( 'entry_sender' );
		$data['entry_recipient'] = $this->language->get( 'entry_recipient' );
		$data['entry_city'] = $this->language->get( 'entry_city' );
		$data['entry_address'] = $this->language->get( 'entry_address' );
		$data['entry_contact_person'] = $this->language->get( 'entry_contact_person' );
		$data['entry_phone'] = $this->language->get( 'entry_phone' );
		$data['entry_cargo_type'] = $this->language->get( 'entry_cargo_type' );
		$data['entry_width'] = $this->language->get( 'entry_width' );
		$data['entry_length'] = $this->language->get( 'entry_length' );
		$data['entry_height'] = $this->language->get( 'entry_height' );
		$data['entry_weight'] = $this->language->get( 'entry_weight' );
		$data['entry_volume_weight'] = $this->language->get( 'entry_volume_weight' );
		$data['entry_volume_general'] = $this->language->get( 'entry_volume_general' );
		$data['entry_seats_amount'] = $this->language->get( 'entry_seats_amount' );
		$data['entry_announced_price'] = $this->language->get( 'entry_announced_price' );
		$data['entry_cargo_description'] = $this->language->get( 'entry_cargo_description' );
		$data['entry_payer'] = $this->language->get( 'entry_payer' );
		$data['entry_payment_type'] = $this->language->get( 'entry_payment_type' );
		$data['entry_backward_delivery'] = $this->language->get( 'entry_backward_delivery' );
		$data['entry_backward_delivery_total'] = $this->language->get( 'entry_backward_delivery_total' );
		$data['entry_backward_delivery_payer'] = $this->language->get( 'entry_backward_delivery_payer' );
		$data['entry_shipment_date'] = $this->language->get( 'entry_shipment_date' );
		$data['entry_service_type'] = $this->language->get( 'entry_service_type' );
		$data['entry_sales_order_number'] = $this->language->get( 'entry_sales_order_number' );
		$data['entry_additional_information'] = $this->language->get( 'entry_additional_information' );
		$data['novaposhta_sender'] = $this->config->get( 'novaposhta_sender' );
		$data['novaposhta_sender_address'] = $this->config->get( 'novaposhta_sender_address' );
		$data['novaposhta_sender_contact_person'] = $this->config->get( 'novaposhta_sender_contact_person' );
		$data['novaposhta_service_type'] = $this->config->get( 'novaposhta_service_type' );
		$data['ei'] = $this->novaposhta->getReferences(  );
		$address = $this->novaposhta->getAddress( $this->config->get( 'novaposhta_sender' ) );
		$warehouses = $this->novaposhta->getWarehouses( $this->config->get( 'novaposhta_sender_city' ), 'byRef' );
		$data['ei']['sender_address'] = array_merge( $address, $warehouses );
		$data['recipient'] = trim( mb_convert_case( $order_info['shipping_lastname'] . ' ' . $order_info['shipping_firstname'], MB_CASE_TITLE, 'UTF-8' ) );
		$data['recipient_city'] = $order_info['shipping_city'];
		$data['recipient_address'] = $order_info['shipping_address_1'];
		$data['recipient_contact_person'] = $data['recipient'];
		$data['recipient_contact_person_phone'] = preg_replace( '/[^0-9]/', '', $order_info['telephone'] );
		$data['width'] = '';
		$data['length'] = '';
		$data['height'] = '';
		$data['weight'] = $this->getProductsWeight( $products );
		$data['volume_general'] = $this->getProductsVolume( $products );
		$data['volume_weight'] = $data['volume_general'] * 250;
		$data['seats_amount'] = 1;
		$data['announced_price'] = $this->getProductsAnnouncedPrice( $order_totals );
		$data['cargo_description'] = $this->config->get( 'novaposhta_cargo_description' );
		$data['backward_delivery_total'] = $data['announced_price'];
		$data['payer'] = (( $this->config->get( 'novaposhta_free_shipping' ) <= $data['announced_price'] && $this->config->get( 'novaposhta_free_shipping' ) ) ? 'Sender' : 'Recipient');
		$data['shipment_date'] = date( 'd.m.Y' );
		$data['sales_order_number'] = $order_id;
		$data['header'] = $this->load->controller( 'common/header' );
		$data['column_left'] = $this->load->controller( 'common/column_left' );
		$data['footer'] = $this->load->controller( 'common/footer' );
		$this->response->setOutput( $this->load->view( 'sale/novaposhta_ei_form.tpl', $data ) );
	}

	function addEI() {
		$this->load->language( 'sale/novaposhta_ei' );

		if (( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate(  ) )) {
			$ei_cache = $this->cache->get( 'novaposhta_ei' );
			$recipient = '';
			$recipient_city = $this->novaposhta->getCities( $this->request->post['recipient_city'], 'ref' );
			$recipient_address = '';
			$recipient_contact_person = '';
			$recipient_contact_person_phone = '';
			$recipients = array_values( $this->novaposhta->getCounterparties( 'Recipient', $recipient_city, $this->request->post['recipient'] ) );

			if (isset( $recipients[0]['Ref'] )) {
				$recipient = $recipients[0]['Ref'];
				$recipient_contact_persons = $this->novaposhta->getContactPerson( $recipient );
				foreach ($recipient_contact_persons as $v) {

					if (( preg_match( '/^(' . $this->request->post['recipient'] . ')/iu', $v['Description'] ) && stripos( $v['Phones'], $this->request->post['recipient_contact_person_phone'] ) !== false )) {
						$person = $v;
						break;
					}
				}


				if (isset( $person )) {
					$recipient_contact_person = $person['Ref'];
					$recipient_contact_person_phone = $person['Phones'];
				}
				else {
					$full_name = explode( ' ', preg_replace( '/ {2,}/', ' ', mb_convert_case( trim( $this->request->post['recipient_contact_person'] ), MB_CASE_TITLE, 'UTF-8' ) ) );
					$properties_c_p = array( 'CounterpartyRef' => $recipient, 'LastName' => $full_name[0], 'FirstName' => $full_name[1], 'MiddleName' => (isset( $full_name[2] ) ? $full_name[2] : ''), 'Phone' => $this->request->post['recipient_contact_person_phone'], 'Email' => '' );
					$result = $this->novaposhta->saveContactPerson( $properties_c_p );

					if ($result) {
						$recipient_contact_person = $result['Ref'];
						$recipient_contact_person_phone = $result['Phones'];
					}
					else {
						$this->error['warnings']['recipient_contact_person'] = $this->language->get( 'error_recipient_contact_person' );
					}
				}
			}
			else {
				$full_name = explode( ' ', preg_replace( '/ {2,}/', ' ', mb_convert_case( trim( $this->request->post['recipient'] ), MB_CASE_TITLE, 'UTF-8' ) ) );
				$properties_r = array( 'CityRef' => $recipient_city, 'LastName' => $full_name[0], 'FirstName' => $full_name[1], 'MiddleName' => (isset( $full_name[2] ) ? $full_name[2] : ''), 'Phone' => $this->request->post['recipient_contact_person_phone'], 'Email' => '', 'CounterpartyType' => 'PrivatePerson', 'CounterpartyProperty' => 'Recipient' );
				$result = $this->novaposhta->saveCounterparties( $properties_r );

				if ($result) {
					$recipient = $result[0]['Ref'];
					$recipient_contact_person = $result[0]['ContactPerson']['data'][0]['Ref'];
					$recipient_contact_person_phone = $this->request->post['recipient_contact_person_phone'];
				}
				else {
					$this->error['warnings']['recipient'] = $this->language->get( 'error_recipient' );
				}
			}

			$this->load->model( 'sale/novaposhta_ei' );
			$recipient_address = $this->model_sale_novaposhta_ei->getAddressByString( $this->request->post['recipient_address'], $recipient_city );
			$properties_ei = array( 'Sender' => $this->config->get( 'novaposhta_sender' ), 'CitySender' => $this->config->get( 'novaposhta_sender_city' ), 'SenderAddress' => $this->config->get( 'novaposhta_sender_address' ), 'ContactSender' => $this->config->get( 'novaposhta_sender_contact_person' ), 'SendersPhone' => $this->request->post['sender_contact_person_phone'], 'Recipient' => $recipient, 'CityRecipient' => $recipient_city, 'RecipientAddress' => $recipient_address, 'ContactRecipient' => $recipient_contact_person, 'RecipientsPhone' => $recipient_contact_person_phone, 'CargoType' => $this->request->post['cargo_type'], 'SeatsAmount' => $this->request->post['seats_amount'], 'Cost' => $this->request->post['announced_price'], 'Description' => $this->request->post['cargo_description'], 'PayerType' => $this->request->post['payer'], 'PaymentMethod' => $this->request->post['payment_type'], 'DateTime' => $this->request->post['shipment_date'], 'ServiceType' => $this->request->post['service_type'] );

			if (preg_match( '/поштомат|почтомат/ui', $this->request->post['recipient_address'] )) {
				$properties_ei['OptionsSeat'][] = array( 'volumetricVolume' => $this->request->post['volume_general'], 'volumetricWidth' => $this->request->post['width'], 'volumetricLength' => $this->request->post['length'], 'volumetricHeight' => $this->request->post['height'], 'weight' => $this->request->post['weight'] );
			}
			else {
				if (isset( $this->request->post['weight'] )) {
					$properties_ei['Weight'] = $this->request->post['weight'];
				}


				if (isset( $this->request->post['volume_weight'] )) {
					$properties_ei['VolumeWeight'] = $this->request->post['volume_weight'];
				}


				if (isset( $this->request->post['volume_general'] )) {
					$properties_ei['VolumeGeneral'] = $this->request->post['volume_general'];
				}
			}


			if (!empty( $this->request->post['backward_delivery'] )) {
				$backward_delivery = array(  );
				switch ($this->request->post['backward_delivery']) {
				case 'Money': {
						$backward_delivery[] = array( 'CargoType' => $this->request->post['backward_delivery'], 'PayerType' => $this->request->post['backward_delivery_payer'], 'RedeliveryString' => $this->request->post['backward_delivery_total'] );
					}
				}

				$properties_ei['BackwardDeliveryData'] = $backward_delivery;
			}


			if (!empty( $this->request->post['sales_order_number'] )) {
				$properties_ei['InfoRegClientBarcodes'] = $this->request->post['sales_order_number'];
			}


			if (!empty( $this->request->post['additional_information'] )) {
				$properties_ei['AdditionalInformation'] = $this->request->post['additional_information'];
			}

			$data = $this->novaposhta->saveEI( $properties_ei );

			if (!$data) {
				$this->error['warnings']['error_ei_add'] = $this->language->get( 'error_ei_add' );
			}
		}


		if ($this->error) {
			$json = $this->error;
		}
		else {
			$this->session->data['success'] = $this->language->get( 'text_ei_success_add' );
			$this->session->data['ei'] = $data[0]['IntDocNumber'];
			$json['redirect'] = $this->url->link( 'sale/novaposhta_ei&filter_shipment_date=' . $this->request->post['shipment_date'], 'token=' . $this->session->data['token'], 'SSL' );
		}

		$this->response->addHeader( 'Content-Type: application/json' );
		$this->response->setOutput( json_encode( $json ) );
	}

	function doPDF() {
		$documents = array(  );

		if (isset( $this->request->post['selected'] )) {
			$documents = $this->request->post['selected'];
		}
		else {
			if (isset( $this->request->get['order'] )) {
				$documents[] = $this->request->get['order'];
			}
		}


		if (isset( $this->request->get['type'] )) {
			$type = $this->request->get['type'];
		}
		else {
			$type = 'printDocument';
		}

		$name = implode( '-', $documents );
		$this->response->addheader( 'Pragma: public' );
		$this->response->addheader( 'Expires: 0' );
		$this->response->addheader( 'Content-Description: File Transfer' );
		$this->response->addheader( 'Content-Type: application/octet-stream' );
		$this->response->addheader( 'Content-Disposition: attachment; filename=Nova_Poshta_' . $name . '_' . date( 'Y-m-d_H-i-s' ) . '.pdf' );
		$this->response->addheader( 'Content-Transfer-Encoding: binary' );
		$data = $this->novaposhta->printDocument( $documents, $type, 'pdf' );
		$this->response->setOutput( $data );
	}

	function getProductsWeight($products) {
		$weight = 225;
		foreach ($products as $product) {
			$weight += $this->weight->convert( $product['weight'] * $product['quantity'], $product['weight_class_id'], $this->config->get( 'config_weight_class_id' ) );
		}


		if (!$weight) {
			$weight = $this->config->get( 'novaposhta_weight' );
		}

		return $weight;
	}

	function getProductsVolume($products) {
		$volume = 230;
		foreach ($products as $product) {
			$volume += $product['length'] * $product['width'] * $product['height'] * $product['quantity'];
		}


		if ($volume <= 0) {
			$volume = $this->config->get( 'novaposhta_dimensions_l' ) * $this->config->get( 'novaposhta_dimensions_w' ) * $this->config->get( 'novaposhta_dimensions_h' );
		}

		return $volume / 1000000;
	}

	function getProductsAnnouncedPrice($totals) {
		$announced_price = 226;
		foreach ($totals as $total) {

			if ($total['code'] == 'total') {
				$announced_price += $total['value'];
			}


			if ($total['code'] == 'shipping') {
				$announced_price -= $total['value'];
				continue;
			}
		}

		return round( $this->currency->convert( $announced_price, $this->config->get( 'config_currency' ), 'UAH' ) );
	}

	function validate() {
		$json = array(  );
		$array_matches = array(  );

		if (!$this->user->hasPermission( 'modify', 'sale/novaposhta_ei' )) {
			$this->error['warnings']['permission'] = $this->language->get( 'error_permission' );
		}


		if (isset( $this->request->post['recipient'] )) {
			if (!preg_match( '/[А-яҐґЄєIіЇї]{2,}\s[А-яҐґЄєIіЇї]{2,}/iu', $this->request->post['recipient'], $array_matches['recipient'] )) {
				$this->error['errors']['recipient'] = $this->language->get( 'error_full_name_correct' );
			}
			else {
				if (preg_match( '/[^А-яҐґЄєIіЇї\s]+/iu', $this->request->post['recipient'], $array_matches['recipient'] )) {
					$this->error['errors']['recipient'] = $this->language->get( 'error_full_name_characters' );
				}
			}
		}


		if (( isset( $this->request->post['recipient_city'] ) && !$this->novaposhta->getCities( $this->request->post['recipient_city'], 'ref' ) )) {
			$this->error['errors']['recipient_city'] = $this->language->get( 'error_city' );
		}


		if (isset( $this->request->post['recipient_address'] )) {
			if (isset( $this->request->post['filter'] )) {
				$filter = $this->request->post['filter'];
			}
			else {
				$filter = $this->request->post['recipient_city'];
			}

			$warehouses = $this->novaposhta->getWarehouses( $filter, 'byDescr' );
			$found = false;

			if ($warehouses) {
				foreach ($warehouses as $w) {

					if ($this->request->post['recipient_address'] == $w['Description']) {
						$found = true;
						break;
					}
				}


				if (!$found) {
					$this->error['errors']['recipient_address_list'] = $warehouses;
					$this->error['errors']['recipient_address'] = $this->language->get( 'error_address' );
				}
			}
			else {
				$this->error['errors']['recipient_address'] = $this->language->get( 'error_address_city' );
			}
		}


		if (isset( $this->request->post['recipient_contact_person'] )) {
			if (!preg_match( '/[А-яҐґЄєIіЇї]{2,}\s[А-яҐґЄєIіЇї]{2,}/iu', $this->request->post['recipient_contact_person'], $array_matches['recipient_contact_person'] )) {
				$this->error['errors']['recipient_contact_person'] = $this->language->get( 'error_full_name_correct' );
			}
			else {
				if (preg_match( '/[^А-яҐґЄєIіЇї\s]+/iu', $this->request->post['recipient_contact_person'], $array_matches['recipient_contact_person'] )) {
					$this->error['errors']['recipient_contact_person'] = $this->language->get( 'error_full_name_characters' );
				}
			}
		}


		if (( isset( $this->request->post['recipient_contact_person_phone'] ) && !preg_match( '/^(380|0)[0-9]{9}$/', $this->request->post['recipient_contact_person_phone'], $array_matches['recipient_contact_person_phone'] ) )) {
			$this->error['errors']['recipient_contact_person_phone'] = $this->language->get( 'error_phone' );
		}


		if (( isset( $this->request->post['width'] ) && ( !preg_match( '/^[1-9]{1}[0-9]*$/', $this->request->post['width'], $array_matches['width'] ) || 35 < $this->request->post['width'] ) )) {
			$this->error['errors']['width'] = $this->language->get( 'error_width' );
		}


		if (( isset( $this->request->post['length'] ) && ( !preg_match( '/^[1-9]{1}[0-9]*$/', $this->request->post['length'], $array_matches['length'] ) || 61 < $this->request->post['length'] ) )) {
			$this->error['errors']['length'] = $this->language->get( 'error_length' );
		}


		if (( isset( $this->request->post['height'] ) && ( !preg_match( '/^[1-9]{1}[0-9]*$/', $this->request->post['height'], $array_matches['width'] ) || 37 < $this->request->post['height'] ) )) {
			$this->error['errors']['height'] = $this->language->get( 'error_height' );
		}


		if (( isset( $this->request->post['weight'] ) && !preg_match( '/^[0-9]+(\.|\,)?[0-9]*$/', $this->request->post['weight'], $array_matches['total_weight'] ) )) {
			$this->error['errors']['weight'] = $this->language->get( 'error_weight' );
		}


		if (( isset( $this->request->post['volume_general'] ) && !preg_match( '/^[0-9]+(\.|\,)?[0-9]*$/', $this->request->post['volume_general'], $array_matches['volume_general'] ) )) {
			$this->error['errors']['volume_general'] = $this->language->get( 'error_volume' );
		}


		if (( isset( $this->request->post['seats_amount'] ) && !preg_match( '/^[1-9]{1}[0-9]*$/', $this->request->post['seats_amount'], $array_matches['seats_amount'] ) )) {
			$this->error['errors']['seats_amount'] = $this->language->get( 'error_seats_amount' );
		}


		if (( isset( $this->request->post['announced_price'] ) && !preg_match( '/^[0-9]+(\.|\,)?[0-9]{1,2}$/', $this->request->post['announced_price'], $array_matches['announced_price'] ) )) {
			$this->error['errors']['announced_price'] = $this->language->get( 'error_announced_price' );
		}


		if (( isset( $this->request->post['cargo_description'] ) && utf8_strlen( $this->request->post['cargo_description'] ) < 3 )) {
			$this->error['errors']['cargo_description'] = $this->language->get( 'error_cargo_description' );
		}


		if (( isset( $this->request->post['backward_delivery_total'] ) && !preg_match( '/^[0-9]+(\.|\,)?[0-9]{1,2}$/', $this->request->post['backward_delivery_total'], $array_matches['backward_delivery_total'] ) )) {
			$this->error['errors']['backward_delivery_total'] = $this->language->get( 'error_backward_delivery_total' );
		}


		if (( isset( $this->request->post['shipment_date'] ) && !preg_match( '/(0[1-9]|1[0-9]|2[0-9]|3[01])\.(0[1-9]|1[012])\.(20)\d\d/', $this->request->post['shipment_date'], $array_matches['shipment_date'] ) )) {
			$this->error['errors']['shipment_date'] = $this->language->get( 'error_date' );
		}
		else {
			if (( isset( $this->request->post['shipment_date'] ) && $this->novaposhta->dateDiff( $this->request->post['shipment_date'] ) < 0 )) {
				$this->error['errors']['shipment_date'] = $this->language->get( 'error_date_past' );
			}
		}


		if (( isset( $this->request->post['additional_information'] ) && 100 < utf8_strlen( $this->request->post['additional_information'] ) )) {
			$this->error['errors']['additional_information'] = $this->language->get( 'error_additional_information' );
		}

		return !$this->error;
	}

	function autocomplete() {
		$json = array(  );

		if (( isset( $this->request->get['recipient_city_filter'] ) && !empty( $this->request->get['recipient_city_filter'] ) )) {
			$this->load->model( 'sale/novaposhta_ei' );
			$json = $this->model_sale_novaposhta_ei->getCitiesAreasByString( $this->request->get['recipient_city_filter'] );
		}


		if (( isset( $this->request->get['cargo_description_filter'] ) && !empty( $this->request->get['cargo_description_filter'] ) )) {
			$data = $this->cache->get( 'novaposhta_ei' );

			if (!empty( $data['cargo_description'] )) {
				$description = $data['cargo_description'];
			}
			else {
				$description = $this->novaposhta->getCargoDescriptionList(  );
			}

			$select = ($this->language->get( 'code' ) == 'uk' ? 'Description' : 'DescriptionRu');
			foreach ($description as $descr) {

				if (preg_match( '/^(' . $this->request->get['cargo_description_filter'] . ').+/iu', $descr[$select] )) {
					$json[] = array( 'Ref' => $descr['Ref'], 'Description' => $descr[$select] );
					continue;
				}
			}
		}

		$this->response->addHeader( 'Content-Type: application/json' );
		$this->response->setOutput( json_encode( $json ) );
	}
}

?>
