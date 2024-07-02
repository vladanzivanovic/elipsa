<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;

class BannerEditRequestDto implements ConstructRequestObjectInterface
{
    public array $available_countries = [];

    public array $translations = [];

    public array $images = [];

    public int $type;

    public int $position;

    public function __construct(Request $request)
    {
        $payload = $request->request;

        $this->available_countries = $payload->all('available_countries');
        $this->translations = $payload->all('translations');
        $this->type = $payload->getInt('type');
        $this->position = $payload->getInt('position');

        foreach ($payload->all('images') as $device => $image) {
            $jsonDecodedImagePayload = json_decode($image, true);

            $this->images[$device] = $jsonDecodedImagePayload;
        }
    }
}
