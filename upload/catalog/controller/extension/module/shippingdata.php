<?php
class ControllerExtensionModuleShippingData extends Controller {
	function getData() {
		$json = array(  );

		if (isset( $this->request->get['shipping'] )) {
			$shipping = $this->request->get['shipping'];
		}


		if (isset( $this->request->get['method'] )) {
			$method = $this->request->get['method'];
		}


		if (isset( $this->request->get['filter'] )) {
			$filter = $this->request->get['filter'];
		}

		switch ($shipping) {
		case 'novaposhta.novaposhta': {
				$this->load->helper( 'novaposhta' );
				$novaposhta = new NovaPoshta( $this->registry );

				if ($method == 'getCities') {
					$this->load->model( 'localisation/zone' );
					$zone_info = $this->model_localisation_zone->getZone( $filter );

					if ($zone_info) {
						$filter = $zone_info['code'];
					}

					$json = $novaposhta->getCities( $filter );
					break;
				}
				else {
					if ($method == 'getWarehouses') {
						$json = $novaposhta->getWarehouses( $filter, 'byDescr' );
					}
				}
			}
		}

		$this->response->addHeader( 'Content-Type: application/json' );
		$this->response->setOutput( json_encode( $json ) );
	}
}

?>
