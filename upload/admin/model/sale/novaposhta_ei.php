<?php

class ModelSaleNovaPoshtaEI extends Model {
	function getOrderProducts($order_id) {
        $query = $this->db->query( 'SELECT op.quantity, op.product_id, p.weight, p.weight_class_id, p.length, p.width, p.height FROM ' . DB_PREFIX . 'order_product AS op INNER JOIN ' . DB_PREFIX . 'product AS p ON op.product_id=p.product_id AND op.order_id=' . $order_id );
		return $query->rows;
	}

	function getCitiesAreasByString($filter) {
		$select = $this->descriptionSelect();
		$this->db->query( 'SELECT c.' . $select . ', z.name FROM ' . DB_PREFIX . 'novaposhta_cities AS c INNER JOIN ' . DB_PREFIX . 'zone AS z ON c.Area=z.code AND c.' . $select . ' LIKE \'' . $filter . '%\'' )->rows;
		$results = $data = array(  );
		foreach ($results as $result) {
			$data[] = array( 'Description' => $result[$select], 'CityArea' => $result[$select] . ', ' . $result['name'] . ' обл.' );
		}

		return $data;
	}

	function getAddressByString($address_name, $city_ref) {
		$select = $this->descriptionSelect();
        $result = $this->db->query( 'SELECT Ref FROM ' . DB_PREFIX . 'novaposhta_warehouses WHERE ' . $select . '=\'' . $address_name . '\' AND CityRef=\'' . $city_ref . '\'' )->row;
		return (isset( $result['Ref'] ) ? $result['Ref'] : false);
	}

	function descriptionSelect() {
		return ($this->language->get( 'code' ) == 'uk' ? 'Description' : 'DescriptionRu');
	}
}

?>
