<?php
class ModelToolOCMax extends Model {
	function purchase() {
		$this->load->language( 'tool/ocmax' );
		$extension = $this->request->get['extension'];
		switch ($this->request->get['action']) {
		case 'send': {
				if (isset( $this->request->get['email'] )) {
					$email = urlencode( $this->request->get['email'] );
				}


				if (isset( $this->request->get['domain'] )) {
					$domain = urlencode( $this->request->get['domain'] );
				}


				if (isset( $this->request->get['market'] )) {
					$market = urlencode( $this->request->get['market'] );
				}


				if (isset( $this->request->get['check'] )) {
					$check = urlencode( $this->request->get['check'] );
				}

				$ch = curl_init( 'http://oc-max.com/index.php?route=module/ocmax/addPurchase&extension=' . $extension . '&email=' . $email . '&domain=' . $domain . '&market=' . $market . '&check=' . $check );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: text/xml' ) );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				$response = curl_exec( $ch );
				curl_close( $ch );

				if ($response) {
					$json['success'] = $this->language->get( 'text_success_sent' );
				}
				else {
					$json['error'] = $this->language->get( 'error_sent' );
				}

				break;
			}

		case 'activate': {
				if ($this->checkLicense( $this->request->get['license'], $extension )) {
					$json['success'] = $this->language->get( 'text_success_activate' );
				}
				else {
					$json['error'] = $this->language->get( 'error_activate' );
				}

				break;
			}

		case 'delete': {
				if ($this->config->get( $extension . '_license' )) {
					$this->load->model( 'setting/setting' );
					$this->model_setting_setting->editSettingValue( $extension, $extension . '_license', '' );
					$json['success'] = $this->language->get( 'text_success_delete' );
					break;
				}
				else {
					$json['error'] = $this->language->get( 'error_delete' );
				}
			}
		}

		return $json;
	}

	function checkLicense($license, $extension) {
		/*$key = md5( 'PPNH' );
		$domain = str_replace( 'www.', '', $_SERVER['SERVER_NAME'] );		
		$license_check = md5( md5( md5( $key ) . $domain . $extension ) );

		if (md5( $license ) == $license_check) {
			return true;
		}

		return false;*/
		return true;
	}
}

?>
