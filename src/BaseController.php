<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 14.01.19
 */

namespace GepurIt\BaseController;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class BaseController
 */
class BaseController extends AbstractController
{
    const DEFAULT_TEMPLATE = 'base.html.twig';

    /**
     * @param ConstraintViolationListInterface $errors
     *
     * @return Response
     */
    public function getErrorsResponse(
        ConstraintViolationListInterface $errors
    ): Response {
        $errors = $this->get('jms_serializer')->serialize($errors, 'json');

        return new Response($errors, 400, ['content-type' => 'application/json']);
    }

    /**
     * @param array $errors is array of custom errors
     * @param int   $status
     *
     * @return Response
     */
    public function getCustomErrorsResponse(
        array $errors,
        $status = 400
    ): Response {
        $formattedErrors = [];

        foreach ($errors as $field => $message) {
            $formattedErrors[] = ['field' => $field, 'message' => $message];
        }

        return new Response($formattedErrors, $status, ['content-type' => 'application/json']);
    }

    /**
     * @param int         $code
     * @param string|null $message
     *
     * @return Response
     */
    public function getCustomResponse(int $code, string $message = null)
    {
        $response = new Response();
        $response->setStatusCode($code, $message);

        return $response;
    }

    /**
     * @param mixed $data
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function simpleView($data, array $serializationGroups = null)
    {
        $context = new SerializationContext();
        if (null !== $serializationGroups) {
            $context->setGroups($serializationGroups);
        }

        $data = $this->get('jms_serializer')->serialize($data, 'json', $context);

        return new Response($data, Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    /**
     * @param $data
     *
     * @return Response
     */
    public function lightView($data)
    {
        return new Response(
            json_encode($data),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * @param $data
     *
     * @return Response
     */
    public function rawJsonView(string $data)
    {
        return new Response(
            $data,
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * @return Response
     */
    public function emptyView()
    {
        return new Response('',Response::HTTP_NO_CONTENT);
    }

    /**
     * @return array
     */
    public static function getSubscribedServices()
    {
        $base  = parent::getSubscribedServices();
        $base['jms_serializer'] = '?'.SerializerInterface::class;
        return $base;
    }
}
