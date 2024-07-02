<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use App\Entity\Notification;
use App\Entity\Product;
use App\Helper\ValidatorHelper;
use App\Mailer\SizeAvailableMailer;
use App\Parser\ImageParser;
use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ProductEditHandler
{
    public function __construct(
        private readonly ValidatorHelper $validator,
        private readonly ProductRepository $productRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly NotificationHandler $notificationHandler,
        private readonly SizeAvailableMailer $sizeAvailableMailer,
        private readonly ImageParser $imageParser
    ) {}

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     */
    public function save(Product $product): void
    {
        $errors = $this->validator->validate($product, null, "SetProduct");

        $isNewProduct = null === $product->getId();

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if ($isNewProduct) {
            $this->productRepository->persist($product);
        }

        $this->productRepository->flush();

        $this->sendSizeAvailableEmails($product, $isNewProduct);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeStatus(Product $product, int $status): void
    {
        $product->setStatus($status);

        $this->productRepository->flush();
    }

    public function setHomePagePosition(Product $product, int $status): void
    {
        $product->setShowHomePage($status);

        $this->productRepository->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function remove(Product $product): void
    {
        foreach ($product->getProductHasImages() as $productHasImage) {
            $image = $productHasImage->getImage();

            $imageArray = [
                'id' => $image->getId(),
                'fileName' => $image->getOriginalName(),
                'deleted' => true,
            ];

            $image = $this->imageParser->parse($imageArray);

            $this->imageParser->delete($image);
        }

        $this->productRepository->delete($product);
        $this->productRepository->flush();
    }

    private function sendSizeAvailableEmails(Product $product, $isNewProduct): void
    {
        if (true === $isNewProduct) {
            return;
        }

        $notifications = $this->notificationRepository->getSizeAvailableNotifications(
            Notification::TYPE_SIZE_AVAILABLE,
            $product->getId()
        );

        foreach ($notifications as $notification) {
            $size = $notification->getPayload()['size'];
            $productOption = $product->getOptionsByCountry($notification->getCountry());

            if (false === $productOption->isSizeAvailable($size)) {
                continue;
            }

            $this->notificationHandler->sendNotificationEmails(
                $notification,
                function () use($notification): void {
                    $this->sizeAvailableMailer->sendEmail($notification);
                }
            );
        }
    }
}
