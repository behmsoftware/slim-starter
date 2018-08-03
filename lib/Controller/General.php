<?php

namespace Slim\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Helper\Controller;

/**
 * Class General
 * @package Slim
 *
 * @author Eugen Behm
 */
class General extends Controller
{

    /**
     * start page controller
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->view->render($this->response, 'General/general.twig', []);
    }
}