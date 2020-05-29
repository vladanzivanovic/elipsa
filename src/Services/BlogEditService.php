<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Entity\Blog;
use App\Traits\ImageTrait;

class BlogEditService
{
    use ImageTrait;
    /**
     * @var string
     */
    private $uploadImageDir;

    /**
     * BlogEditService constructor.
     *
     * @param string $uploadImageDir
     */
    public function __construct(
        string $uploadImageDir
    ) {
        $this->uploadImageDir = $uploadImageDir;
    }

    /**
     * @param Blog $blog
     *
     * @return array
     */
    public function getImages(Blog $blog): array
    {
        return $this->imagesFormatter(DIRECTORY_SEPARATOR.$this->uploadImageDir, $blog->getMedia());
    }
}