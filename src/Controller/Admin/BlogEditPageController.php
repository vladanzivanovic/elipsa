<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\Tags;
use App\Formatter\Admin\BlogEditResponseFormatter;
use App\Repository\TagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class BlogEditPageController extends AbstractController
{
    /**
     * @var BlogEditResponseFormatter
     */
    private $responseFormatter;
    /**
     * @var ParameterBagInterface
     */
    private $bag;
    /**
     * @var TagsRepository
     */
    private $tagsRepository;

    /**
     * @param BlogEditResponseFormatter $responseFormatter
     * @param ParameterBagInterface     $bag
     * @param TagsRepository            $tagsRepository
     */
    public function __construct(
        BlogEditResponseFormatter $responseFormatter,
        ParameterBagInterface $bag,
        TagsRepository $tagsRepository
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->bag = $bag;
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @Route("/add-blog", name="admin.add_blog_page", methods={"GET"})
     * @Template("Admin/Pages/blogEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [
            'tags' => $this->tagsRepository->getForOptions(Tags::TYPE_BLOG),
        ];
    }

    /**
     * @Route("/edit-blog/{id}", name="admin.edit_blog_page", methods={"GET"})
     * @Template("Admin/Pages/blogEdit.html.twig")
     *
     * @param Blog $blog
     *
     * @return array
     */
    public function update(Blog $blog): array
    {
        $blogData = $this->responseFormatter->formatResponse($blog);

        $options = [
            'tags' => $this->tagsRepository->getForOptions(Tags::TYPE_BLOG),
        ];

        return $blogData + $options;
    }
}