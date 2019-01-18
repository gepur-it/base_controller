<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 14.01.19
 */

namespace GepurIt\BaseController;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        return new JsonResponse($errors, 400, ['Content-type' => 'application/json'], true);
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

        return new JsonResponse($formattedErrors, $status, ['Content-type' => 'application/json'], true);
    }

    /**
     * @param int         $code
     * @param string|null $message
     *
     * @return Response
     */
    public function getCustomResponse(int $code, string $message = null)
    {
        $response = new JsonResponse(null, JsonResponse::HTTP_OK, ['Content-type' => 'application/json']);
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

        return new JsonResponse($data, JsonResponse::HTTP_OK, ['Content-type' => 'application/json'], true);
    }

    /**
     * @param $data
     *
     * @return Response
     */
    public function lightView($data)
    {
        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK,
            ['Content-type' => 'application/json'],
            false
        );
    }

    /**
     * @param $data
     *
     * @return Response
     */
    public function rawJsonView(string $data)
    {
        return new JsonResponse(
            $data,
            JsonResponse::HTTP_OK,
            ['Content-type' => 'application/json'],
            true
        );
    }

    /**
     * @return Response
     */
    public function emptyView()
    {
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT, ['Content-type' => 'application/json']);
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
