<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

trait ControllerTrait
{
    
    public function transformPutData(Request $request): Request
    {
        if ($request->getMethod() === $request::METHOD_PUT) {
            $data = json_decode($request->getContent(), true);

            $request->request->add($data);
        }

        return $request;
    }
}
