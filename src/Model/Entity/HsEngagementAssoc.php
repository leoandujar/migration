<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'hs_engagement_assoc')]
#[ORM\Index(name: '', columns: ['hs_engagement_assoc_id'])]
#[ORM\Entity]
class HsEngagementAssoc implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_engagement_assoc_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_engagement_assoc_id', type: 'bigint')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: HsEngagement::class, inversedBy: 'associations')]
	#[ORM\JoinColumn(name: 'hs_engagement_id', referencedColumnName: 'hs_engagement_id', nullable: false)]
	private HsEngagement $hsEngagement;

	#[ORM\ManyToOne(targetEntity: HsContactPerson::class)]
	#[ORM\JoinColumn(name: 'hs_contact_id', referencedColumnName: 'hs_contact_person_id', nullable: true)]
	private ?HsContactPerson $hsContact;

	#[ORM\ManyToOne(targetEntity: HsCustomer::class)]
	#[ORM\JoinColumn(name: 'hs_company_id', referencedColumnName: 'hs_customer_id', nullable: true)]
	private ?HsCustomer $hsCompany;

	#[ORM\ManyToOne(targetEntity: HsDeal::class)]
	#[ORM\JoinColumn(name: 'hs_deal_id', referencedColumnName: 'hs_deal_id', nullable: true)]
	private ?HsDeal $hsDeal;

	#[ORM\ManyToOne(targetEntity: InternalUser::class)]
	#[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'internal_user_id', nullable: true)]
	private ?InternalUser $owner;

	#[ORM\ManyToOne(targetEntity: Workflow::class)]
	#[ORM\JoinColumn(name: 'workflow_id', referencedColumnName: 'workflow_id', nullable: true)]
	private ?Workflow $workflow;

	/**
	 * @return mixed
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function getHsEngagement(): ?HsEngagement
	{
		return $this->hsEngagement;
	}

	/**
	 * @return mixed
	 */
	public function setHsEngagement(?HsEngagement $hsEngagement): self
	{
		$this->hsEngagement = $hsEngagement;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHsContact(): ?HsContactPerson
	{
		return $this->hsContact;
	}

	/**
	 * @return mixed
	 */
	public function setHsContact(?HsContactPerson $hsContact): self
	{
		$this->hsContact = $hsContact;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHsCompany(): ?HsCustomer
	{
		return $this->hsCompany;
	}

	/**
	 * @return mixed
	 */
	public function setHsCompany(?HsCustomer $hsCompany): self
	{
		$this->hsCompany = $hsCompany;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHsDeal(): ?HsDeal
	{
		return $this->hsDeal;
	}

	/**
	 * @return mixed
	 */
	public function setHsDeal(?HsDeal $hsDeal): self
	{
		$this->hsDeal = $hsDeal;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getOwner(): ?InternalUser
	{
		return $this->owner;
	}

	/**
	 * @return mixed
	 */
	public function setOwner(?InternalUser $owner): self
	{
		$this->owner = $owner;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getWorkflow(): ?Workflow
	{
		return $this->workflow;
	}

	/**
	 * @return mixed
	 */
	public function setWorkflow(?Workflow $workflow): self
	{
		$this->workflow = $workflow;

		return $this;
	}
}
