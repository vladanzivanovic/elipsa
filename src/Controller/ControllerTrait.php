<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

trait ControllerTrait
{
    /**
     * @param Request $request
     *
     * @return Request
     */
    public function transformPutData(Request $request): Request
    {
        if ($request->getMethod() === $request::METHOD_PUT) {
            dd($request->request);
            $data = json_decode($request->getContent(), true);

            $request->request->add($data);
        }

        return $request;
    }
}