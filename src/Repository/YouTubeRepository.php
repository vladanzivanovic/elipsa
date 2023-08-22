<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Youtube;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class YouTubeRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Youtube::class);
    }
    public function getByAdsId($id, $getFromCache = true)
    {
        $query = $this->createQueryBuilder('y')
            ->select('
                y.id as Id,
                IDENTITY(y.adsid) as AdsId,
                y.title as YouTubeTitle,
                y.title AS Title,
                y.youtubeid as YouTubeId,
                y.channelid as ChannelId,
                y.chaneltitle as ChanelTitle,
                y.thumbnails as Thumbnails
            ')
            ->where( 'y.adsid = :adsId' )
            ->setParameter('adsId', $id)
            ->getQuery();

        return $query->getResult();
    }
}
