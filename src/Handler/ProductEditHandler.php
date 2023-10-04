<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Notification;
use App\Entity\Product;
use App\Helper\ValidatorHelper;
use App\Mailer\SizeAvailableMailer;
use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ProductEditHandler
{
    private ValidatorHelper $validator;

    private ProductRepository $productRepository;

    private NotificationRepository $notificationRepository;

    private NotificationHandler $notificationHandler;

    private SizeAvailableMailer $sizeAvailableMailer;

    public function __construct(
        ValidatorHelper $validator,
        ProductRepository $productRepository,
        NotificationRepository $notificationRepository,
        NotificationHandler $notificationHandler,
        SizeAvailableMailer $sizeAvailableMailer
    ) {
        $this->validator = $validator;
        $this->productRepository = $productRepository;
        $this->notificationRepository = $notificationRepository;
        $this->notificationHandler = $notificationHandler;
        $this->sizeAvailableMailer = $sizeAvailableMailer;
    }

    /**
     * @param Product $product
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return void
     */
    public function save(Product $product): void
    {
        $errors = $this->validator->validate($product, null, "SetProduct");

        $isNewProduct = null === $product->getId();

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (true === $isNewProduct) {
            $this->productRepository->persist($product);
        }

        $this->productRepository->flush();

        $this->sendSizeAvailableEmails($product, $isNewProduct);
    }

    /**
     * @param Product $product
     * @param int     $status
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeStatus(Product $product, int $status): void
    {
        $product->setStatus($status);

        $this->productRepository->flush();
    }

    /**
     * @param Product $product
     */
    public function remove(Product $product): void
    {
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

            if (false === $product->isSizeAvailable($size)) {
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
