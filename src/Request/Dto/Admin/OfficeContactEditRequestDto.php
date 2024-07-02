<?php

declare(strict_types=1);

namespace App\Request\Dto\Admin;

use Symfony\Component\HttpFoundation\Request;

final class OfficeContactEditRequestDto extends AbstractEditRequestDto
{
    public string $telephone;

    public bool $showInFooter;

    public bool $useInEmail;

    public function __construct(Request $request)
    {
        $body = $request->request;

        $this->telephone = $body->get('telephone');
        $this->showInFooter = $body->getBoolean('show_in_footer');
        $this->useInEmail = $body->getBoolean('use_in_email');

        parent::__construct($request);
    }
}
