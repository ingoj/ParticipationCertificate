<?php
class ilPartCertUserData {

	/**
	 * @var int
	 */
	protected $part_cert_usr_id;
	/**
	 *
	 * @var string
	 */
	protected $part_cert_firstname;
	/**
	 * @var string
	 */
	protected $part_cert_lastname;
	/**
	 *
	 * @var string
	 */
	protected $part_cert_gender;
	/**
	 *
	 * @var string
	 */
	protected $part_cert_salutation;


	/**
	 * @return int
	 */
	public function getPartCertUsrId() {
		return $this->part_cert_usr_id;
	}


	/**
	 * @param int $part_cert_usr_id
	 */
	public function setPartCertUsrId($part_cert_usr_id) {
		$this->part_cert_usr_id = $part_cert_usr_id;
	}


	/**
	 * @return string
	 */
	public function getPartCertFirstname() {
		return $this->part_cert_firstname;
	}


	/**
	 * @param string $part_cert_firstname
	 */
	public function setPartCertFirstname($part_cert_firstname) {
		$this->part_cert_firstname = $part_cert_firstname;
	}


	/**
	 * @return string
	 */
	public function getPartCertLastname() {
		return $this->part_cert_lastname;
	}


	/**
	 * @param string $part_cert_lastname
	 */
	public function setPartCertLastname($part_cert_lastname) {
		$this->part_cert_lastname = $part_cert_lastname;
	}


	/**
	 * @return string
	 */
	public function getPartCertGender() {
		return $this->part_cert_gender;
	}


	/**
	 * @param string $part_cert_gender
	 */
	public function setPartCertGender($part_cert_gender) {
		$this->part_cert_gender = $part_cert_gender;
	}


	/**
	 * @return string
	 */
	public function getPartCertSalutation() {
		return $this->part_cert_salutation;
	}


	/**
	 * @param string $part_cert_salutation
	 */
	public function setPartCertSalutation($part_cert_salutation) {
		$this->part_cert_salutation = $part_cert_salutation;
	}
}
?>