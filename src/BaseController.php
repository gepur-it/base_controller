<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 14.01.19
 */

namespace GepurIt\BaseController;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class BaseController
 */
class BaseController extends AbstractFOSRestController
{
    const DEFAULT_TEMPLATE = 'base.html.twig';

    /**
     * @param ConstraintViolationListInterface $errors
     * @param string                           $template
     *
     * @return Response
     */
    public function getErrorsResponse(
        ConstraintViolationListInterface $errors,
        $template = self::DEFAULT_TEMPLATE
    ): Response {
        return $this->handleView($this->view($errors)->setTemplate($template)->setStatusCode(400));
    }

    /**
     * @param array  $errors is array of custom errors
     * @param int    $status
     * @param string $template
     *
     * @return Response
     */
    public function getCustomErrorsResponse(
        array $errors,
        $status = 400,
        $template = self::DEFAULT_TEMPLATE
    ): Response {
        $formattedErrors = [];

        foreach ($errors as $field => $message) {
            $formattedErrors[] = ['field' => $field, 'message' => $message];
        }

        return $this->handleView($this->view($formattedErrors)->setTemplate($template)->setStatusCode($status));
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
        $view = $this->view($data)->setTemplate(self::DEFAULT_TEMPLATE);
        if (null !== $serializationGroups) {
            $view->getContext()->setGroups($serializationGroups);
        }

        return $this->handleView($view);
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
        $view = $this->view(null, 204)->setTemplate(self::DEFAULT_TEMPLATE);

        return $this->handleView($view);
    }
}
