<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Nelexa\RequestDtoBundle\Dto\ConstructRequestObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractEditRequestDto implements ConstructRequestObjectInterface
{
    public array $availableCountries = [];

    public array $translations = [];

    public null|array $images = null;

    public Session $session;

    public function __construct(Request $request)
    {
        $payload = $request->request;

        $this->availableCountries = $payload->all('available_countries');
        $this->translations = $payload->all('translations');
        $this->session = $request->getSession();

        if ($payload->has('images')) {
            $this->images = true === is_array($payload->all()['images']) ?
                $payload->all('images') :
                json_decode($payload->get('images'), true);
        }
    }

    protected function splitImagesByDevices(): void
    {
        $images = [];

        foreach ($this->images as $device => $image) {
            $images[$device][] = $image;
        }

        $this->images = $images;
    }
}
