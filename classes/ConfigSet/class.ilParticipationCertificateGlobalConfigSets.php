<?php

/**
 * Class ilParticipationCertificateGlobalConfigs
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateGlobalConfigSets {

	public function __construct() {

	}

	public function getAllConfigsAsArray() {
		return ilParticipationCertificateGlobalConfigSet::orderBy('order_by')->getArray();
	}


	/**
	 * @return ilParticipationCertificateGlobalConfigSet
	 */
	public function addNewConfig() {
			$gl_config = new ilParticipationCertificateGlobalConfigSet();
			$gl_config->setOrderBy($this->getUnreservedOrderByValue());
			$gl_config->store();
			return $gl_config;
	}


	/**
	 * @param int $id
	 *
	 * @return ilParticipationCertificateGlobalConfigSet
	 */
	public function getConfigSetById($id) {
		/**
		 * @var ilParticipationCertificateGlobalConfigSet $gl_config
		 */
		$gl_config = ilParticipationCertificateGlobalConfigSet::where(['id' => $id])->first();
		return $gl_config;
	}

	/**
	 * @return ilParticipationCertificateGlobalConfigSet
	 */
	public function getDefaultConfig() {
		/**
		 * @var ilParticipationCertificateGlobalConfigSet $gl_config
		 */
		$gl_config = ilParticipationCertificateGlobalConfigSet::where(['order_by' => 1])->first();
		return $gl_config;
	}


	/**
	 * @param $config_key
	 *
	 * @return bool|string
	 */
	public function getDefaultConfigSetValue($config_key) {
		$gl_config = $this->getDefaultConfig();

		$participation_configs = new ilParticipationCertificateConfigs();

		if(is_object($participation_configs->getParticipationTemplateConfigValueByKey($gl_config->getId(),$config_key))) {
			return $participation_configs->getParticipationTemplateConfigValueByKey($gl_config->getId(),$config_key)->getConfigValue();
		}
		return false;
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

			$part_cert_config = new ilParticipationCertificateGlobalConfigSet($id);
			$part_cert_config->setOrderBy($order_by);
			$part_cert_config->store();
			$arr_reserved[] = $order_by;
		}

		$i = 1;
		foreach(ilParticipationCertificateGlobalConfigSet::orderBy('order_by')->get() as $global_config) {
			/**
			 * @var ilParticipationCertificateGlobalConfigSet $global_config
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
		 * @var ilParticipationCertificateGlobalConfigSet $config
		 */
		$config = ilParticipationCertificateGlobalConfigSet::orderBy('order_by','DESC')->first();
		return $config->getOrderBy() + 1;
	}

	public function getSelectOptions() {

		$arr_select_options = [0 => '--'];

		foreach(ilParticipationCertificateGlobalConfigSet::where(['active' => 1])->orderBy('order_by')->get() as $global_config) {
			/**
			 * @var ilParticipationCertificateGlobalConfigSet $global_config
			 */
			$arr_select_options[$global_config->getId()] = $global_config->getTitle();
		}

		return $arr_select_options;
	}
}
