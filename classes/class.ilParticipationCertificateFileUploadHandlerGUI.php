<?php

use ILIAS\FileUpload\Handler\AbstractCtrlAwareUploadHandler;
use ILIAS\FileUpload\Handler\FileInfoResult;
use ILIAS\FileUpload\Handler\HandlerResult;
use ILIAS\ResourceStorage\Services;
use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\FileUpload\Handler\BasicHandlerResult;
use ILIAS\FileUpload\Handler\BasicFileInfoResult;


/**
 * @ilCtrl_isCalledBy ilParticipationCertificateFileUploadHandlerGUI: ilUIPluginRouterGUI
 */
class ilParticipationCertificateFileUploadHandlerGUI extends AbstractCtrlAwareUploadHandler
{
    /**
     * @var Services
     */
    private Services $storage;

    public function __construct()
    {
        parent::__construct();
        global $DIC;
        $this->storage = $DIC->resourceStorage();
    }


    /**
     * @inheritDoc
     */
    public function getUploadURL(): string
    {
        return $this->ctrl->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_UPLOAD);
    }


    /**
     * @inheritDoc
     */
    public function getExistingFileInfoURL(): string
    {
        return $this->ctrl->getLinkTargetByClass(
            [ilUIPluginRouterGUI::class, self::class],
            self::CMD_INFO
        );
    }


    /**
     * @inheritDoc
     */
    public function getFileRemovalURL(): string
    {
        return $this->ctrl->getLinkTargetByClass(
            [ilUIPluginRouterGUI::class, self::class],
            self::CMD_REMOVE,
            null,
            false
        );
    }


    /**
     * @inheritDoc
     */
    public function getFileIdentifierParameterName(): string
    {
        return 'my_file_id';
    }


    /**
     * @inheritDoc
     */
    protected function getUploadResult(): HandlerResult
    {
        $status = HandlerResult::STATUS_OK;
        $identifier = md5(random_bytes(65));
        $message = 'Everything ok';

        return new BasicHandlerResult($this->getFileIdentifierParameterName(), $status, $identifier, $message);
    }


    protected function getRemoveResult(string $identifier): HandlerResult
    {
        $status = HandlerResult::STATUS_OK;
        $message = 'File Deleted';

        return new BasicHandlerResult($this->getFileIdentifierParameterName(), $status, $identifier, $message);
    }


    public function getInfoResult(string $identifier): ?FileInfoResult
    {
        /*return new BasicFileInfoResult(
            $this->getFileIdentifierParameterName(),
            $identifier,
            "My funny Testfile $identifier.txt",
            64,
            ""
        );*/


        $id = $this->storage->manage()->find($identifier);

        if ($id === null) {
            return new BasicFileInfoResult($this->getFileIdentifierParameterName(), 'unknown', 'unknown', 0, 'unknown');
        }

        $result = $this->storage->manage()->getCurrentRevision($id)->getInformation();

        return new BasicFileInfoResult(
            $this->getFileIdentifierParameterName(),
            $identifier,
            $result->getTitle(),
            $result->getSize(),
            $result->getMimeType()
        );

    }


    public function getInfoForExistingFiles(array $file_ids): array
    {
        /*$infos = [];
        foreach ($file_ids as $file_id) {
            $infos[] = new BasicFileInfoResult(
                $this->getFileIdentifierParameterName(),
                $file_id,
                "Name $file_id.txt",
                rand(1000, 2000),
                "text/plain"
            );
        }

        return $infos;*/

        $infos = [];
        foreach ($file_ids as $file_id) {

            $id = $this->storage->manage()->find($file_id);

            if ($id === null) {
                continue;
            }

            $result = $this->storage->manage()->getCurrentRevision($id)->getInformation();

            $infos[] = new BasicFileInfoResult(
                $this->getFileIdentifierParameterName(),
                $file_id,
                $result->getTitle(),
                $result->getSize(),
                $result->getMimeType()
            );
        }

        return $infos;

    }
}