<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     */
    public function indexAction()
    {
        echo 'indexAction'; die();

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        die();
    }

    /**
     * Lists all user entities.
     *
     */
    public function badAction()
    {
        return $this->errorResponse();
    }

    /**
     * Lists all user entities.
     *
     */
    public function searchAction()
    {
        echo 'searchAction'; die();
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        die();
    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request)
    {
        // if not connect
        if (false) {
            $response = new JsonResponse();
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $response->setData(array(
                'code' => '401',
                'message' => 'Must be connected',
                //'missingfields' => '',
            ));
        }

        $user = new User();
        $content = json_decode($request->getContent(),1);

        if (!$content) {
            return $this->errorResponse();
        } else {
            $users = $this->get('app.lib_users');
            $is_valid = $users->checkIfValid($content);

            if ($is_valid) {

                $users->jsonBind($user, $content);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush($user);

                $response = new JsonResponse();
                $response->setStatusCode(Response::HTTP_CREATED);
                $response->setData(array(
                    $content
                ));
            } else {
                return $this->errorResponse();
            }
        }



        /*
            const HTTP_OK = 200;
            const HTTP_CREATED = 201;
            const HTTP_ACCEPTED = 202;
            const HTTP_BAD_REQUEST = 400;
            const HTTP_UNAUTHORIZED = 401;
            const HTTP_FORBIDDEN = 403;
            const HTTP_NOT_FOUND = 404;
        */

        die();

        return $this->redirectToRoute('user_show', array('id' => $user->getId()));
    }

    /**
     * Finds and displays a user entity.
     *
     */
    public function showAction(User $user)
    {
        echo 'showAction';
        dump($user);
        die();
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        echo 'editAction';
        dump($user);
        die();

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Deletes a user entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
        echo 'deleteAction';
        dump($user);
        die();

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush($user);

        return $this->redirectToRoute('user_index');
    }

    private function errorResponse() {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $response->setData(array(
            'code' => '400',
            'message' => 'ErrorResponse',
        ));

        return $response;
    }
}
