<?php
class ilPartCertUserData {
	protected int $part_cert_usr_id;
	protected ?string $part_cert_firstname;
	protected ?string $part_cert_lastname;
	protected ?string $part_cert_gender;
	protected ?string $part_cert_salutation;
	protected string $part_cert_username;

	public function getPartCertUsrId(): int
    {
		return $this->part_cert_usr_id;
	}
	public function setPartCertUsrId(int $part_cert_usr_id): void
    {
		$this->part_cert_usr_id = $part_cert_usr_id;
	}
	public function getPartCertFirstname(): ?string
    {
		return $this->part_cert_firstname;
	}
	public function setPartCertFirstname(?string $part_cert_firstname): void
    {
		$this->part_cert_firstname = $part_cert_firstname;
	}
	public function getPartCertLastname(): ?string
    {
		return $this->part_cert_lastname;
	}
	public function setPartCertLastname(?string $part_cert_lastname): void
    {
		$this->part_cert_lastname = $part_cert_lastname;
	}
	public function getPartCertGender(): ?string
    {
		return $this->part_cert_gender;
	}
	public function setPartCertGender(?string $part_cert_gender): void
    {
		$this->part_cert_gender = $part_cert_gender;
	}
	public function getPartCertSalutation(): ?string
    {
		return $this->part_cert_salutation;
	}
	public function setPartCertSalutation(?string $part_cert_salutation): void
    {
		$this->part_cert_salutation = $part_cert_salutation;
	}
	public function getPartCertUserName(): string
    {
		return $this->part_cert_username;
	}
	public function setPartCertUserName(string $part_cert_username): void
    {
		$this->part_cert_username = $part_cert_username;
	}
}
