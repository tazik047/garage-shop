<?php
class NovaPoshta {
	protected $registry = null;
	protected $api_url = 'https://api.novaposhta.ua/v2.0/json/';
	var $key_api = '';

	function __construct($registry) {
		$this->registry = $registry;
		$this->key_api = $this->config->get( 'novaposhta_key_api' );
	}

	function __get($name) {
		return $this->registry->get( $name );
	}

	function apiRequest($model, $method, $properties = array(  )) {
		$array = array( 'apiKey' => $this->key_api, 'modelName' => $model, 'calledMethod' => $method );

		if (!empty( $properties )) {
			$array['methodProperties'] = $properties;
		}

		$options = array( CURLOPT_HTTPHEADER => array( 'Content-Type: application/json' ), CURLOPT_HEADER => 0, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 10, CURLOPT_POST => 1, CURLOPT_POSTFIELDS => json_encode( $array ), CURLOPT_RETURNTRANSFER => 1 );
		$ch = curl_init( $this->api_url );
		curl_setopt_array( $ch, $options );
		$response = curl_exec( $ch );
		curl_close( $ch );

		if (( $method == 'printDocument' || $method == 'printMarkings' )) {
			$data = $response;
		}
		else {
			$response = json_decode( $response, true );

			if ($response['success']) {
				$data = $response['data'];
			}
			else {
				$this->catchError( $response );
				$data = false;
			}
		}

		return $data;
	}

	function catchError($response) {
		if (is_array( $response['errors'] )) {
			foreach ($response['errors'] as $error) {
				$this->log->write( 'Nova Poshta error: ' . $error );
			}
		}


		if (is_array( $response['warnings'] )) {
			foreach ($response['warnings'] as $warning) {
				$this->log->write( 'Nova Poshta warning: ' . $warning );
			}
		}


		if (is_array( $response['info'] )) {
			foreach ($response['info'] as $info) {
				$this->log->write( 'Nova Poshta info: ' . $info );
			}
		}

	}

	function update($type) {
		$data = array(  );
		switch ($type) {
		case 'areas': {
				$data = $this->apiRequest( 'Address', 'getAreas' );

				if ($data) {
					$values = '';
					foreach ($data as $area) {

						if ($values) {
							$values .= ',';
						}

						$values .= ' (\'1\',
							\'' . $this->db->escape( $area['Description'] ) . '\',
							\'' . $this->db->escape( $area['Ref'] ) . '\',
							\'220\')';
					}

					$this->db->query( 'UPDATE `' . DB_PREFIX . 'zone` SET `status`= \'0\' WHERE `country_id` = \'220\' ' );
					$this->db->query( 'DELETE FROM `' . DB_PREFIX . 'zone` WHERE LENGTH(`code`) = \'36\'' );
					$this->db->query( 'INSERT INTO `' . DB_PREFIX . 'zone` (`status`, `name`, `code`, `country_id`) VALUES ' . $values );
					$this->cache->delete( 'zone' );
				}

				break;
			}

		case 'cities': {
				$data = $this->apiRequest( 'Address', 'getCities' );

				if ($data) {
					$this->db->query( 'TRUNCATE `' . DB_PREFIX . 'novaposhta_cities`' );
					foreach ($data as $city) {
						$this->db->query( 'INSERT INTO `' . DB_PREFIX . 'novaposhta_cities` (`Description`, `DescriptionRu`, `Ref`, `Area`,  `Delivery1`, `Delivery2`, `Delivery3`, `Delivery4`, `Delivery5`, `Delivery6`, `Delivery7`, `Conglomerates`, `CityID`) VALUES
							(\'' . $this->db->escape( $city['Description'] ) . '\',
							\'' . $this->db->escape( $city['DescriptionRu'] ) . '\',
							\'' . $this->db->escape( $city['Ref'] ) . '\',
							\'' . $this->db->escape( $city['Area'] ) . '\',
							\'' . $this->db->escape( $city['Delivery1'] ) . '\',
							\'' . $this->db->escape( $city['Delivery2'] ) . '\',
							\'' . $this->db->escape( $city['Delivery3'] ) . '\',
							\'' . $this->db->escape( $city['Delivery4'] ) . '\',
							\'' . $this->db->escape( $city['Delivery5'] ) . '\',
							\'' . $this->db->escape( $city['Delivery6'] ) . '\',
							\'' . $this->db->escape( $city['Delivery7'] ) . '\',
							\'' . $this->db->escape( serialize( $city['Conglomerates'] ) ) . '\',
							\'' . $this->db->escape( $city['CityID'] ) . '\')' );
					}
				}

				break;
			}

		case 'warehouses': {
				$data = $this->apiRequest( 'Address', 'getWarehouses' );

				if ($data) {
					$this->db->query( 'TRUNCATE `' . DB_PREFIX . 'novaposhta_warehouses`' );
					foreach ($data as $w) {
						$this->db->query( 'INSERT INTO `' . DB_PREFIX . 'novaposhta_warehouses` (`Description`, `DescriptionRu`, `Phone`, `TypeOfWarehouse`,  `Ref`, `Number`, `CityRef`, `CityDescription`, `CityDescriptionRu`, `Longitude`, `Latitude`, `TotalMaxWeightAllowed`, `PlaceMaxWeightAllowed`, `Reception`, `Delivery`, `Schedule`) VALUES
							(\'' . htmlspecialchars( $this->db->escape( $w['Description'] ) ) . '\',
							\'' . htmlspecialchars( $this->db->escape( $w['DescriptionRu'] ) ) . '\',
							\'' . $this->db->escape( $w['Phone'] ) . '\',
							\'' . $this->db->escape( $w['TypeOfWarehouse'] ) . '\',
							\'' . $this->db->escape( $w['Ref'] ) . '\',
							\'' . $this->db->escape( $w['Number'] ) . '\',
							\'' . $this->db->escape( $w['CityRef'] ) . '\',
							\'' . $this->db->escape( $w['CityDescription'] ) . '\',
							\'' . $this->db->escape( $w['CityDescriptionRu'] ) . '\',
							\'' . $this->db->escape( $w['Longitude'] ) . '\',
							\'' . $this->db->escape( $w['Latitude'] ) . '\',
							\'' . $this->db->escape( $w['TotalMaxWeightAllowed'] ) . '\',
							\'' . $this->db->escape( $w['PlaceMaxWeightAllowed'] ) . '\',
							\'' . $this->db->escape( serialize( $w['Reception'] ) ) . '\',
							\'' . $this->db->escape( serialize( $w['Delivery'] ) ) . '\',
							\'' . $this->db->escape( serialize( $w['Schedule'] ) ) . '\')' );
					}
				}

				break;
			}

		case 'references': {
				$data['senders'] = $this->novaposhta->getCounterparties( 'Sender' );
				foreach ($data['senders'] as $k => $v) {
					$data['sender_contact_person'][$k] = $this->novaposhta->getContactPerson( $k );
				}

				$data['warehouse_types'] = $this->novaposhta->getWarehouseTypes(  );
				$data['cargo_types'] = $this->novaposhta->getCargoTypes(  );
				$data['payer_types'] = $this->novaposhta->getPayersTypes(  );
				$data['payment_types'] = $this->novaposhta->getPaymentForm(  );
				$data['backward_delivery_types'] = $this->novaposhta->getBackwardDeliveryCargoTypes(  );
				$data['backward_delivery_payers'] = $this->novaposhta->getRedeliveryPayersTypes(  );
				$data['service_types'] = $this->novaposhta->getServiceTypes(  );
				$data['cargo_description'] = $this->novaposhta->getCargoDescriptionList(  );
				foreach ($data as $k => $v) {
					$this->db->query( 'INSERT INTO `' . DB_PREFIX . 'novaposhta_references` (`type`, `value`) VALUES (\'' . $k . '\', \'' . $this->db->escape( serialize( $v ) ) . '\') ON DUPLICATE KEY UPDATE `value`=\'' . $this->db->escape( serialize( $v ) ) . '\'' );
				}
			}
		}

		$database = $this->getReferences( 'database' );
		$database[$type]['update_datetime'] = date( 'd-m-Y H:i:s' );
		$database[$type]['amount'] = ($data ? count( $data ) : 0);
		$this->db->query( 'INSERT INTO `' . DB_PREFIX . 'novaposhta_references` (`type`, `value`) VALUES (\'database\', \'' . serialize( $database ) . '\') ON DUPLICATE KEY UPDATE `value`=\'' . serialize( $database ) . '\'' );
		return ($data ? $database[$type]['amount'] : $data);
	}

	function getAreas() {
		$data = array(  );
		$areas = $this->db->query( 'SELECT `name`, `code` FROM `' . DB_PREFIX . 'zone` WHERE `country_id` = 220 AND LENGTH(`code`) = 36 ORDER BY `name`' )->rows;
		foreach ($areas as $area) {
			$data[] = array( 'Description' => $area['name'], 'Ref' => $area['code'] );
		}

		return $data;
	}

	function getCities($filter, $flag = '') {
		$select = $this->descriptionSelect(  );
		$data = array(  );

		if ($flag == 'ref') {
			$result = $this->db->query( 'SELECT `Ref` FROM `' . DB_PREFIX . 'novaposhta_cities` WHERE `' . $select . '` = \'' . $filter . '\'' )->row;
			$data = ($result ? $result['Ref'] : '');
		}
		else {
			if ($flag == 'descr') {
				$result = $this->db->query( 'SELECT `' . $select . '` FROM `' . DB_PREFIX . 'novaposhta_cities` WHERE `Ref` = \'' . $filter . '\'' )->row;
				$data = ($result ? $result[$select] : '');
			}
			else {
				$results = $this->db->query( 'SELECT `' . $select . '`, `Ref` FROM `' . DB_PREFIX . 'novaposhta_cities` WHERE `Area` = \'' . $filter . '\'' )->rows;
				$data = $this->arrayPrep( $results, $select, 'Description' );
			}
		}

		return $data;
	}

	function getWarehouses($city, $flag) {
		$select = $this->descriptionSelect(  );

		if ($flag == 'byDescr') {
			$city_description = ($this->language->get( 'code' ) == 'uk' ? 'CityDescription' : 'CityDescriptionRu');
			$results = $this->db->query( 'SELECT `' . $select . '`, `Ref` FROM `' . DB_PREFIX . 'novaposhta_warehouses` WHERE `' . $city_description . '` = \'' . $city . '\'' )->rows;
		}
		else {
			if ($flag == 'byRef') {
				$results = $this->db->query( 'SELECT `' . $select . '`, `Ref` FROM `' . DB_PREFIX . 'novaposhta_warehouses` WHERE `CityRef` = \'' . $city . '\'' )->rows;
			}
		}

		return $this->arrayPrep( $results, $select, 'Description' );
	}

	function getWarehouseType($warehouse, $flag) {
		if ($flag == 'byDescr') {
			$select = $this->descriptionSelect(  );
			$result = $this->db->query( 'SELECT `TypeOfWarehouse` FROM `' . DB_PREFIX . 'novaposhta_warehouses` WHERE `' . $select . '` = \'' . $warehouse . '\'' )->row;
		}
		else {
			if ($flag == 'byRef') {
				$result = $this->db->query( 'SELECT `TypeOfWarehouse` FROM `' . DB_PREFIX . 'novaposhta_warehouses` WHERE `Ref` = \'' . $warehouse . '\'' )->row;
			}
		}

		return (isset( $result['TypeOfWarehouse'] ) ? $result['TypeOfWarehouse'] : '');
	}

	function getReferences($type = '') {
		$data = '';

		if ($type) {
			$result = $this->db->query( 'SELECT `value` FROM `' . DB_PREFIX . 'novaposhta_references` WHERE `type` = \'' . $type . '\'' )->row;
			$data = (isset( $result['value'] ) ? unserialize( $result['value'] ) : false);
		}
		else {
			$results = $this->db->query( 'SELECT `type`, `value` FROM `' . DB_PREFIX . 'novaposhta_references`' )->rows;

			if (is_array( $results )) {
				foreach ($results as $r) {
					$data[$r['type']] = unserialize( $r['value'] );
				}
			}
		}

		return $data;
	}

	function getWarehouseTypes() {
		$data = $this->apiRequest( 'Address', 'getWarehouseTypes' );
		return $data;
	}

	function getCargoTypes() {
		$data = $this->apiRequest( 'Common', 'getCargoTypes' );
		return $data;
	}

	function getServiceTypes() {
		$data = $this->apiRequest( 'Common', 'getServiceTypes' );
		return $data;
	}

	function getPayersTypes() {
		$data = $this->apiRequest( 'Common', 'getTypesOfPayers' );
		return $data;
	}

	function getPaymentForm() {
		$data = $this->apiRequest( 'Common', 'getPaymentForms' );
		return $data;
	}

	function getBackwardDeliveryCargoTypes() {
		$data = $this->apiRequest( 'Common', 'getBackwardDeliveryCargoTypes' );
		return $data;
	}

	function getRedeliveryPayersTypes() {
		$data = $this->apiRequest( 'Common', 'getTypesOfPayersForRedelivery' );
		return $data;
	}

	function getCargoDescriptionList() {
		$data = $this->apiRequest( 'Common', 'getCargoDescriptionList' );
		return $data;
	}

	function getDocumentPrice($properties) {
		$data = $this->apiRequest( 'InternetDocument', 'getDocumentPrice', $properties );
		return $data[0]['Cost'];
	}

	function getDocumentDeliveryDate($properties) {
		$data = $this->apiRequest( 'InternetDocument', 'getDocumentDeliveryDate', $properties );
		return ($data ? $this->dateDiff( $data[0]['DeliveryDate']['date'] ) : 0);
	}

	function getCounterparties($counterparty, $city_ref = '', $string = '') {
		$data = array(  );
		$properties = array( 'CounterpartyProperty' => $counterparty, 'CityRef' => $city_ref, 'FindByString' => $string );
		$counterparties = $this->apiRequest( 'Counterparty', 'getCounterparties', $properties );

		if ($counterparties) {
			foreach ($counterparties as $v) {
				$data[$v['Ref']] = $v;
				$data[$v['Ref']]['CityDescription'] = $this->novaposhta->getCities( $v['City'], 'descr' );
			}
		}

		return $data;
	}

	function getAddress($ref) {
		$properties = array( 'Ref' => $ref );
		$data = $this->apiRequest( 'Counterparty', 'getCounterpartyAddresses', $properties );
		return $data;
	}

	function getContactPerson($ref) {
		$data = array(  );
		$properties = array( 'Ref' => $ref );
		$contact_persons = $this->apiRequest( 'Counterparty', 'getCounterpartyContactPersons', $properties );

		if ($contact_persons) {
			foreach ($contact_persons as $person) {
				$data[$person['Ref']] = $person;
			}
		}

		return $data;
	}

	function saveCounterparties($properties) {
		$data = $this->apiRequest( 'Counterparty', 'save', $properties );
		return $data;
	}

	function saveContactPerson($properties) {
		$data = $this->apiRequest( 'ContactPerson', 'save', $properties );
		return $data;
	}

	function saveEI($properties) {
		$data = $this->apiRequest( 'InternetDocument', 'save', $properties );
		return $data;
	}

	function getEIList($date = '') {
		$properties = array( 'DateTime' => $date );
		$data = $this->apiRequest( 'InternetDocument', 'getDocumentList', $properties );
		return $data;
	}

	function printDocument($orders, $type, $format) {
		$properties = array( 'DocumentRefs' => $orders, 'Type' => $format );
		$data = $this->apiRequest( 'InternetDocument', $type, $properties );
		return $data;
	}

	function tariffCalculation($service_type, $weight, $sub_total) {
		$tariffs = array( 'DoorsDoors' => array( 1 => 50, 2 => 55, 5 => 60, 10 => 65, 15 => 80, 30 => 100 ), 'DoorsWarehouse' => array( 1 => 40, 2 => 45, 5 => 50, 10 => 55, 15 => 70, 30 => 90 ), 'WarehouseDoors' => array( 1 => 40, 2 => 45, 5 => 50, 10 => 55, 15 => 70, 30 => 90 ), 'WarehouseWarehouse' => array( 1 => 25, 2 => 30, 5 => 40, 10 => 50, 15 => 65, 30 => 85 ) );

		if ($weight <= 1) {
			$cost = $tariffs[$service_type][1];
		}
		else {
			if ($weight <= 2) {
				$cost = $tariffs[$service_type][2];
			}
			else {
				if ($weight <= 5) {
					$cost = $tariffs[$service_type][5];
				}
				else {
					if ($weight <= 10) {
						$cost = $tariffs[$service_type][10];
					}
					else {
						if ($weight <= 15) {
							$cost = $tariffs[$service_type][15];
						}
						else {
							$cost = $tariffs[$service_type][30];
						}
					}
				}
			}
		}


		if (300 < $sub_total) {
			$cost += $sub_total * 0.00500000000000000010408341;
		}

		return round( $cost );
	}

	function arrayPrep($array, $search, $replace) {
		$new_array = array(  );
		foreach ($array as $k => $v) {

			if (is_array( $v )) {
				foreach ($v as $_k => $_v) {

					if ($_k == $search) {
						$new_array[$k][$replace] = $_v;
						continue;
					}

					$new_array[$k][$_k] = $_v;
				}

				continue;
			}
		}

		return $new_array;
	}

	function descriptionSelect() {
		return ($this->language->get( 'code' ) == 'uk' ? 'Description' : 'DescriptionRu');
	}

	function dateDiff($string_time) {
		return ceil( ( strtotime( $string_time ) - time(  ) ) / 86400 );
	}
}

?>
