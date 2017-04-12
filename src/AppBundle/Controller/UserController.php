<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use JMS\Serializer\SerializerBuilder;



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
 
                //if not admin
        if ( !in_array('ROLE_ADMIN', $app_user->getRoles()) ) {
            return $this->deniedResponse();
        }
        $users = $this->get('app.lib_users');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findAll();

        $tbl=[];
        foreach ($user as $key => $value) {
           array_push($tbl, $this->UserToArray($value));
        }
        return $this->showResponse($tbl);
        die();
    }

    /**
     * Lists all user entities.
     *
     */
    public function searchAction(Request $request)
    {
        $app_user = $this->getUser();

        $name = $request->get('q');
        $number = $request->get('count');
        $em = $this->getDoctrine()->getManager();

        //$parameters = array('q' => $name,'i' => $number);

        $query = $em->createQueryBuilder('u')
        ->select('u.lastname, u.email')
        ->from('AppBundle:User','u')
        ->where('u.lastname LIKE :q OR u.email LIKE :q')
        ->setMaxResults($number)
        ->setParameters(['q' => $name])
        ->getQuery();

        dump($query->execute());

        die();
    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request)
    {
        $app_user = $this->getUser();

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
                dump($content);
                die();
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
    public function showAction(Request $request)
    {

        $app_user = $this->getUser();

        //if not admin
        if ( !in_array('ROLE_ADMIN', $app_user->getRoles()) ) {
            return $this->deniedResponse();
        }
        $users = $this->get('app.lib_users');

        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        $usr = $this->UserToArray($user);
        if (!$user) {
            return $this->errorResponse();
        } else {
            return $this->showResponse($usr);
        }
    }

    /**
     * Transforme un user en tableau.
     *
     */
    private function UserToArray($user){
        $id = $user->getId();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $role = $user->getRole();

        $usr = [
            'id' => $id,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'email' => $email,
            'role' => $role
        ];
        return $usr;
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $app_user = $this->getUser();

        //if not admin
        if ( !in_array('ROLE_ADMIN', $app_user->getRoles()) ) {
            return $this->deniedResponse();
        }
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        $content = json_decode($request->getContent(),1);

        if (!$content) {
            return $this->errorResponse();
        } else {
            foreach ($content as $key => $value) {
                $fct = "set".ucfirst($key);
                $user->$fct($value);
                $em->persist($user);
                $em->flush($user);
                return $this->modifiedResponse($content);
            }
        }
    }

    /**
     * Deletes a user entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
        $app_user = $this->getUser();

        //if not admin
        if ( !in_array('ROLE_ADMIN', $app_user->getRoles()) ) {
            return $this->deniedResponse();
        }
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        
        if (!$user) {
            return $this->errorResponse();
        } else {
            $em->remove($user);
            $em->flush();
            return $this->deleteResponse();
        }

    }

    private function showResponse($data) {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setData(array(
            'code' => '200',
            $data
        ));
        return $response;
    }


    private function deleteResponse() {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setData(array(
            'code' => '200',
            'message' => 'User successfully deleted',
        ));
        return $response;
    }

    private function modifiedResponse() {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setData(array(
            'code' => '200',
            'message' => 'User successfully modified',
        ));
        return $response;
    }

    private function createResponse($data) {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setData(array(
            $data
            // 'code' => '201',
            // 'message' => 'User successfully created',
        ));
        return $response;
    }

    private function deniedResponse() {
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
        $response->setData(array(
            'code' => '403',
            'message' => 'You must be admin',
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
