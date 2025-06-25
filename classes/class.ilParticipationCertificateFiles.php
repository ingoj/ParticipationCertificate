<?php

use ILIAS\ResourceStorage\Identification\ResourceIdentification;

class ilParticipationCertificateFiles extends ActiveRecord
{
	const TABLE_NAME = 'dhbw_part_cert_files';

    /**
     * @var int
     *
     * @db_has_field    true
     * @db_fieldtype    integer
     * @db_is_primary   true
     * @db_sequence     true
     */
    protected ?int $id = 0;

    /**
     * @var string
     *
     * @db_has_field    true
     * @db_fieldtype    text
     * @con_is_notnull  true
     */
    protected string $type;

    /**
     * @var int
     *
     * @db_has_field    true
     * @db_fieldtype    integer
     * @con_is_notnull  true
     */
    protected int $config_id;

    /**
     * @var bool
     *
     * @db_has_field    true
     * @db_fieldtype    integer
     * @con_is_notnull  true
     * @db_length       1
     */
    protected bool $resource_storage;

	public function getConnectorContainerName(): string
    {
		return self::TABLE_NAME;
	}

	public static function returnDbTableName(): string
    {
		return self::TABLE_NAME;
	}

	public function __construct($primary_key = 0, arConnector $connector = null) {
		parent::__construct($primary_key, $connector);
	}

    /**
     * @param string $configId
     * @param string $fileType
     * @return ActiveRecord|null
     */
	static function getFile(
        string $configId,
        string $fileType
    ): ActiveRecord|null
    {
		/**
		 * @var ilParticipationCertificateFiles|null $config
		 */
		$file = self::where([
            'config_id' => $configId,
            'type' => $fileType,
		])->first();

        return $file ?? null;
	}

	static function setFile(
        string $configId,
        string $fileType,
        bool $resourceStorage
    ): void {
		/**
		 * @var ilParticipationCertificateFiles|null $file
		 */
		$file = self::where([
            'config_id' => $configId,
            'type' => $fileType,
		])->first();

		if (!empty($file)) {
            $file->setConfigId($configId);
            $file->setType($fileType);
            $file->setResourceStorage($resourceStorage);
            $file->update();
		} else {
            $file = new self();
            $file->setConfigId($configId);
            $file->setResourceStorage($resourceStorage);
            $file->setType($fileType);
            $file->create();
		}
	}

    public function setConfigId(string $groupRefId): void
    {
        $this->config_id = $groupRefId;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setResourceStorage(bool $resourceStorage): void
    {
        $this->resource_storage = $resourceStorage;
    }

	public function setId(int $id): void
    {
		$this->id = $id;
	}

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfigId(): string
    {
        return $this->config_id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function getResourceStorage(): bool
    {
        return $this->resource_storage;
    }

    /**
     * Files are stored under ./data/default/dhbw_part_cert in ILIAS8, while the resource storage is used in ILIAS9.
     *
     * @param        $value
     * @param string $configId
     * @param string $fileType
     * @return string
     */
    public function getFileSrcByStorageType (
        $value,
        string $configId,
        string $fileType
    ): string {
        global $DIC;

        $file = ilParticipationCertificateFiles::getFile($configId, $fileType);

        if (empty($file)) {
            return '';
        }
        $fileArray = $file->asArray();

        $src = '';
        if($fileArray['resource_storage']) {
            $resource = new ResourceIdentification($value);

            if ($DIC->resourceStorage()->manage()->find($resource)) {
                $src = $DIC->resourceStorage()->consume()
                           ->src($resource)
                           ->getSrc();
            }
        } else {
            $fileName = ilParticipationCertificateConfig::LOGO_FILE_NAME;
            if($fileType === 'page1_issuer_signature') {
                $fileName = ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME;
            }

            $filePath = ilParticipationCertificateConfig::returnPicturePath(
                'relative',
                $configId,
                $fileName
            );

            if (is_file($filePath)) {
                $stream = \ILIAS\Filesystem\Stream\Streams::ofResource(
                    fopen($filePath, 'r')
                );

                $src = $DIC->fileDelivery()->buildTokenURL(
                    $stream,
                    $fileName,
                    \ILIAS\FileDelivery\Delivery\Disposition::INLINE,
                    $DIC->user()->getId(),
                    6
                );
            }
        }
        return $src;
    }
}