<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class ProductEditRequestDto extends AbstractEditRequestDto
{
    public array $options;

    public string $code;

    public array $categories;

    public array $tags;

    public array $cleaning;

    public array $youtubeUrl;

    public function __construct(Request $request)
    {
        $payload = $request->request;

        $this->options = $payload->all('options');
        $this->code = $payload->get('code');
        $this->tags = $payload->all('tags');
        $this->youtubeUrl = $payload->all('youtubeUrl');
        $this->categories = $payload->all('categories');
        $this->cleaning = $payload->all('cleaning');

        parent::__construct($request);
    }
}
