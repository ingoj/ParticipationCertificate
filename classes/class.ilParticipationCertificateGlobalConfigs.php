<?php

/**
 * Class ilParticipationCertificateGlobalConfigs
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateGlobalConfigs {

	public function __construct() {

	}

	public function getAllConfigsAsArray() {
		return ilParticipationCertificateGlobalConfig::orderBy('order_by')->getArray();
	}


	/**
	 * @return ilParticipationCertificateGlobalConfig
	 */
	public function addNewConfig() {
			$gl_config = new ilParticipationCertificateGlobalConfig;
			$gl_config->setOrderBy($this->getUnreservedOrderByValue());
			$gl_config->store();
			return $gl_config;
	}


	/**
	 * @param array $arr_order_by
	 *
	 * @throws Exception
	 */
	public function saveAndRearangeOrderBy($arr_order_by = array()) {
		$arr_reserved = [];

		asort($arr_order_by);

		foreach($arr_order_by as $id => $order_by) {
			//$order_by = intval($order) / 10;

			if(in_array($order_by,$arr_reserved)) {
				$order_by = $order_by + 1;
			}

			$part_cert_config = new ilParticipationCertificateGlobalConfig($id);
			$part_cert_config->setOrderBy($order_by);
			$part_cert_config->store();
			$arr_reserved[] = $order_by;
		}

		$i = 1;
		foreach(ilParticipationCertificateGlobalConfig::orderBy('order_by')->get() as $global_config) {
			/**
			 * @var ilParticipationCertificateGlobalConfig $global_config
			 */
			$global_config->setOrderBy($i);
			if($i === 1) {
				$global_config->setActive(1);
			}
			$global_config->store();
			$i = $i + 1;
		}

	}


	/**
	 * @return int
	 */
	public function getUnreservedOrderByValue() {
		/**
		 * @var ilParticipationCertificateGlobalConfig $config
		 */
		$config = ilParticipationCertificateGlobalConfig::orderBy('order_by','DESC')->first();
		return $config->getOrderBy() + 1;
	}

	public function getSelectOptions() {

		$arr_select_options = [];

		foreach(ilParticipationCertificateGlobalConfig::where(['active' => 1])->orderBy('order_by')->get() as $global_config) {
			/**
			 * @var ilParticipationCertificateGlobalConfig $global_config
			 */
			$arr_select_options[$global_config->getId()] = $global_config->getTitle();
		}

		return $arr_select_options;
	}
}
