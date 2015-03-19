<?php

namespace Ipeer\ApiUtilityBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidationErrorListener {

    public function onKernelController(FilterControllerEvent $event)
    {
        $validationErrors = $event->getRequest()->attributes->get('validationErrors');



        if(null !== $validationErrors && count($validationErrors) > 0) {
            throw new BadRequestHttpException($validationErrors);
        }
    }
}
