<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Entity\User;
use App\Formatter\Admin\BannerEditResponseFormatter;
use App\Formatter\Admin\UserEditResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UserEditPageController extends AbstractController
{
    /**
     * @var UserEditResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ParameterBagInterface     $bag
     * @param UserEditResponseFormatter $responseFormatter
     */
    public function __construct(
        ParameterBagInterface $bag,
        UserEditResponseFormatter $responseFormatter
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-user", name="admin.add_user_page", methods={"GET"})
     * @Template("Admin/Pages/userEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-user/{id}", name="admin.edit_user_page", methods={"GET"})
     * @Template("Admin/Pages/userEdit.html.twig")
     *
     * @param User $user
     *
     * @return array
     */
    public function update(User $user): array
    {
        return $this->responseFormatter->formatResponse($user);
    }
}