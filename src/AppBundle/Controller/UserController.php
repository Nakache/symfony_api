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
    // if not connect
    /*if (false) {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        $response->setData(array(
            'code' => '401',
            'message' => 'Must be connected',
            //'missingfields' => '',
        ));
    }*/

    //$em = $this->getDoctrine()->getManager();
    //$users = $em->getRepository('AppBundle:User')->findAll();

    // $this->getDoctrine()->getManager()->flush();

    /*
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush($user);

        return $this->redirectToRoute('user_index');
    */

    /**
     * Lists all user entities.
     *
     */
    public function indexAction()
    {
        $app_user = $this->getUser();
        dump($app_user);
        die();
    }

    /**
     * Lists all user entities.
     *
     */
    public function searchAction()
    {
        $app_user = $this->getUser();
        die();
    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request)
    {
        $app_user = $this->getUser();
        dump($app_user);
        die();

        // if not admin
        if ( !in_array('ROLE_ADMIN', $app_user->getRoles()) ) {
            return $this->deniedResponse();
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
                return $this->createResponse($content);
            } else {
                return $this->errorResponse();
            }
        }
    }

    /**
     * Finds and displays a user entity.
     *
     */
    public function showAction(User $user)
    {
        $app_user = $this->getUser();
        die();
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $app_user = $this->getUser();
        die();
    }

    /**
     * Deletes a user entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
        $app_user = $this->getUser();
        //dump($user);
        die();
    }

    private function createResponse($data) {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setData(array(
            $data
        ));
        return $response;
    }

    private function deniedResponse() {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        $response->setData(array(
            'code' => '403',
            'message' => 'Must be admin',
        ));

        return $response;
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

    /**
     * Lists all user entities.
     *
     */
    public function badAction()
    {
        return $this->errorResponse();
    }
}
