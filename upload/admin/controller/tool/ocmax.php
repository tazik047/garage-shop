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

class ControllerToolOCMax extends Controller {
	protected $error = array(  );

	function index($extension) {
		$this->load->language( 'tool/ocmax' );
		$data['text_about_license'] = $this->language->get( 'text_about_license' );
		$data['text_about_support'] = $this->language->get( 'text_about_support' );
		$data['entry_license'] = $this->language->get( 'entry_license' );
		$data['entry_email'] = $this->language->get( 'entry_email' );
		$data['entry_domain'] = $this->language->get( 'entry_domain' );
		$data['entry_market'] = $this->language->get( 'entry_market' );
		$data['entry_check'] = $this->language->get( 'entry_check' );
		$data['help_activate'] = $this->language->get( 'help_activate' );
		$data['help_license'] = $this->language->get( 'help_license' );
		$data['help_delete'] = $this->language->get( 'help_delete' );
		$data['help_email'] = $this->language->get( 'help_email' );
		$data['help_domain'] = $this->language->get( 'help_domain' );
		$data['help_market'] = $this->language->get( 'help_market' );
		$data['help_check'] = $this->language->get( 'help_check' );
		$data['help_send'] = $this->language->get( 'help_send' );
		$data['legend_license'] = $this->language->get( 'legend_license' );
		$data['legend_support'] = $this->language->get( 'legend_support' );
		$data['token'] = $this->session->data['token'];
		$data['extension'] = $extension;

		if (isset( $this->request->post[$extension . '[license]'] )) {
			$data['license'] = $this->request->post[$extension . '_license'];
		}
		else {
			$data['license'] = $this->config->get( $extension . '_license' );
		}

		$this->load->model( 'tool/ocmax' );
		$data['check_license'] = $this->model_tool_ocmax->checkLicense( $data['license'], $extension );
		return $this->load->view( 'tool/ocmax.tpl', $data );
	}

	function update() {
		$this->db->query( 'CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'novaposhta_references`
   			(`id` int(11) NOT NULL AUTO_INCREMENT,
   			`type` varchar(100) NOT NULL,
   			`value` mediumtext NOT NULL,
   			UNIQUE(`type`),
   			PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1' );
		echo 'Successfully updated!';
	}
}

?>
