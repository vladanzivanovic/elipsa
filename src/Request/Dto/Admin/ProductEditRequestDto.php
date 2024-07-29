<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class ProductEditRequestDto extends AbstractEditRequestDto
{
    /**
     * @var array<string, ProductOptionsRequestDto>
     */
    public array $options;

    public string $code;

    public array $categories;

    public array $tags;

    public array $cleaning;

    public array $youtubeUrl;

    public function __construct(Request $request)
    {
        $payload = $request->request;

        $this->code = $payload->get('code');
        $this->tags = $payload->all('tags');
        $this->youtubeUrl = $payload->all('youtubes');
        $this->categories = $payload->all('categories');
        $this->cleaning = $payload->all('cleaning');

        $this->setOptions($payload->all('options'));

        parent::__construct($request);
    }

    private function setOptions(array $options): void
    {
        foreach ($options as $countryCode => $option) {
            $this->options[$countryCode] = new ProductOptionsRequestDto($option);
        }
    }
}
