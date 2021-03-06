<?php

namespace Intern\Command;

use \Intern\InternshipFactory;
use \Intern\EmergencyContact;
use \Intern\EmergencyContactFactory;
use \Intern\DatabaseStorage;

class EmergencyContactRest {

	public function execute()
	{

		switch($_SERVER['REQUEST_METHOD']) {
            case 'DELETE':
                $this->delete();
                exit;
            case 'GET':
            	$this->get();
				exit;
			case 'POST':
				$this->post();
                exit;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
                exit;
        }
	}

    public function get()
	{
        // Check permissions
        if(!\Current_User::isLogged()){
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

		$internshipId = $_REQUEST['internshipId'];

        echo json_encode($this->getAllContacts($internshipId));
        exit;
	}

	public function post()
	{
        // Check permissions
        if(!\Current_User::isLogged()){
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

         // Get data from request
        $internshipId = $_REQUEST['internshipId'];
        $name         = $_REQUEST['emergency_contact_name'];
        $relation     = $_REQUEST['emergency_contact_relation'];
        $phone        = $_REQUEST['emergency_contact_phone'];
        $email        = $_REQUEST['emergency_contact_email'];

        // Sanity checking
        if (is_null($internshipId) || !isset($internshipId)) {
            throw new InvalidArgumentException('Missing internship ID.');
        }

        if (is_null($name) || !isset($name)) {
            throw new InvalidArgumentException('Missing contact name.');
        }

        if (is_null($relation) || !isset($relation)) {
            throw new InvalidArgumentException('Missing contact relationship.');
        }

        if (is_null($phone) || !isset($phone)) {
            throw new InvalidArgumentException('Missing contact phone number.');
        }

        if (is_null($email) || !isset($email)) {
            throw new InvalidArgumentException('Missing contact email.');
        }

        if ($_REQUEST['contactId'] != -1) {
            $contactId = $_REQUEST['contactId'];

            $contact = EmergencyContactFactory::getContactById($contactId);

            $contact->setName($name);
            $contact->setRelation($relation);
            $contact->setPhone($phone);
            $contact->setEmail($email);

            DatabaseStorage::save($contact);
        } else {
            // Get an Internship object based on the ID
            $internship = InternshipFactory::getInternshipById($internshipId);

            // Create the emergency contact
            $contact = new EmergencyContact($internship, $name, $relation, $phone, $email);

            // Save the emergency contact object
            DatabaseStorage::save($contact);

            //echo json_encode($contact);

        }

        echo json_encode($this->getAllContacts($internshipId));
        exit;
	}

	public function delete()
	{
		// Check permissions
        if(!\Current_User::isLogged()){
            header('HTTP/1.1 403 Forbidden');
            exit;
        }

        // Get the contactId parameter
        if(!isset($_REQUEST['contactId'])){
            throw new InvalidArgumentException('Missing contact id.');
        }

        if(!isset($_REQUEST['internshipId'])){
            throw new InvalidArgumentException('Missing internship id.');
        }

        $contactId = $_REQUEST['contactId'];
        $internshipId = $_REQUEST['internshipId'];

        $contact = EmergencyContactFactory::getContactById($contactId);

        EmergencyContactFactory::delete($contact);

        echo json_encode($this->getAllContacts($internshipId));
        exit;
	}

    private function getAllContacts($internshipId)
    {
        // Get an Internship object based on the ID
        $internship = InternshipFactory::getInternshipById($internshipId);
        $contacts = EmergencyContactFactory::getContactsForInternship($internship);

        return $contacts;
    }
}
