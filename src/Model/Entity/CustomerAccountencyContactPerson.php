<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'customer_accountency_contact_persons')]
#[ORM\UniqueConstraint(name: 'customer_accountency_contact_persons_customer_person_id_key', columns: ['customer_person_id'])]
#[ORM\Entity]
class CustomerAccountencyContactPerson implements EntityInterface
{
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'customerAccountencyPersons')]
	#[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id', nullable: false)]
	private Customer $customer;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: CustomerPerson::class, inversedBy: 'customersAccountecy')]
	#[ORM\JoinColumn(name: 'customer_person_id', referencedColumnName: 'contact_person_id', nullable: false)]
	private CustomerPerson $customerPerson;
}
