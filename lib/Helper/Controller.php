<?php
namespace Slim\Helper;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class Controller
 * @package Slim\Helper
 */
abstract class Controller
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     *
     * @return Controller
     */
    public function setData(array $data): Controller
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * slim invoke
     *
     * @param $request
     * @param $response
     * @param $args
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $args): Response
    {
        return $response;
    }

    /**
     * Controller constructor.
     *
     * @param ContainerInterface $container
     *
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        if ($this->container->response instanceof Response) {
            $this->response = $this->container->response;
        } else {
            throw new \Exception("instance of this->container->response doesn't match!", 1521892654315);
        }

        if ($this->container->request instanceof Request) {
            $this->request = $this->container->request;
        } else {
            throw new \Exception("instance of this->container->request doesn't match!", 1521892730718);
        }

        if ($this->container->view instanceof Twig) {
            $this->view = $this->container->view;
        } else {
            throw new \Exception("instance of this->container->view doesn't match!", 1521892735141);
        }

    }

    /**
     * Ajax array return
     *
     * @param bool $status
     * @param string $message
     * @param int $code
     *
     * @return array
     */
    protected function returnAjax(bool $status, string $message, int $code)
    {
        return [
            "status" => $status,
            "message" => $message,
            "code" => $code
        ];
    }

}
