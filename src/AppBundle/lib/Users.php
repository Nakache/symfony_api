<?php

namespace AppBundle\lib;

use AppBundle\Entity\User;

class Users {

	private $entityManager;
	private $props;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
		$this->entityManager = $entityManager;
		$this->props = [ "lastname", "firstname", "email", "password", "role" ];
	}

	public function checkIfValid(Array $user) {

		$props = $this->props;

		if (count($user) != count($props)) return false;
		foreach ($props as $prop) if (!isset($user[$prop])) return false;

		$em = $this->entityManager;
		$userRepo = $em->getRepository('AppBundle:User');

		if ( (bool) $userRepo->findOneByEmail($user['email']) ) return false;
		
		return true;
	}

	public function jsonBind(User &$entity, Array $user) {
		foreach ($this->props as $prop) $entity->{'set'.strtoupper($prop[0]).substr($prop, 1)}($user[$prop]);
	}
}