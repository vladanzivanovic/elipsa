<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class LocationEditRequestDto extends AbstractEditRequestDto
{
    public string $zipCode;
    public string $workingHours;
    public string $workingHoursSaturday;
    public null|string $workingHoursSunday;
    public null|string $email;
    public null|string $telephone;
    public string $lat;
    public string $lng;

    public function __construct(Request $request)
    {
        $payload = $request->request;

        $this->zipCode = $payload->get('zip_code');
        $this->workingHours = $payload->get('working_hours');
        $this->workingHoursSaturday = $payload->get('working_hours_saturday');
        $this->workingHoursSunday = $payload->get('working_hours_sunday');
        $this->email = $payload->get('email');
        $this->telephone = $payload->get('telephone');
        $this->lat = $payload->get('lat');
        $this->lng = $payload->get('lng');

        parent::__construct($request);
    }
}
