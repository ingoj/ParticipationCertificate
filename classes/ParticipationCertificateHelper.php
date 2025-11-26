<?php

use ILIAS\Container\InternalDomainService;

class ParticipationCertificateHelper
{
    /**
     * @param int $courseRefId
     * @return int|null
     * @throws ilDatabaseException
     * @throws ilObjectNotFoundException
     */
    public static function getGroupRefId(int $courseRefId): ?int
    {
        global $DIC;

        $domain = $DIC->container()
                      ->internal()
                      ->domain();

        $containerRefId = $DIC->repositoryTree()->getParentId($courseRefId);

        return self::getGroupOfContainer($containerRefId, $domain);
    }

    /**
     * @param int                   $containerRefId
     * @param InternalDomainService $domain
     * @return int|null
     * @throws ilDatabaseException
     * @throws ilObjectNotFoundException
     */

    private static function getGroupOfContainer(
        int $containerRefId,
        InternalDomainService $domain
    ): int|null {
        $containerObjectFactory = \ilObjectFactory::getInstanceByRefId($containerRefId);

        $itemPresentation = $domain
            ->content()
            ->itemPresentation(
                $containerObjectFactory, // TODO replace it with container ???
                null,
                false
            );

        $items = $itemPresentation->getAllRefIds();
        $groupRefId = null;
        foreach ($items as $key => $itemRefId) {
            $itemObject = \ilObjectFactory::getInstanceByRefId($itemRefId);

            if ($itemObject->getType() === 'grp') {
                $groupRefId = (int) $itemRefId;

                break;
            }
        }
        return $groupRefId;
    }
}
