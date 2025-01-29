<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\SequenceGenerator;

#[ORM\Table(name: 'hs_marketing_email')]
#[ORM\Entity]
class HsMarketingEmail implements EntityInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'SEQUENCE')]
	#[SequenceGenerator(sequenceName: 'hs_marketing_email_id_sequence', initialValue: 1)]
	#[ORM\Column(name: 'hs_marketing_email_id', type: 'bigint')]
	private string $id;

	#[ORM\Column(name: 'hsmarketing_email_id', type: 'bigint', nullable: false)]
	private string $hsMarketingEmail;

	#[ORM\Column(name: 'name', type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(name: 'successful_delivery', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $successfulDelivery;

	#[ORM\Column(name: 'opt_in_out', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => '0.0'])]
	private ?int $optInOut;

	#[ORM\Column(name: 'archived', type: 'boolean', nullable: false)]
	private bool $archived;

	#[ORM\Column(name: 'created_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $createdDate;

	#[ORM\Column(name: 'publish_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $publishDate;

	#[ORM\Column(name: 'updated_date', type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $updatedDate;

	#[ORM\Column(name: 'sent_count', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $sentCount;

	#[ORM\Column(name: 'open_count', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $openCount;

	#[ORM\Column(name: 'delivered_count', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $deliveredCount;

	#[ORM\Column(name: 'bounce_count', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $bounceCount;

	#[ORM\Column(name: 'unsubscriber_count', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $unsubscriberCount;

	#[ORM\Column(name: 'click_count', type: 'integer', nullable: true, options: ['default' => 0])]
	private ?int $clickCount;

	#[ORM\Column(name: 'open_ratio', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => '0.0'])]
	private ?float $openRatio;

	#[ORM\Column(name: 'delivered_ratio', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => '0.0'])]
	private ?float $deliveredRatio;

	#[ORM\Column(name: 'bounce_ratio', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => '0.0'])]
	private ?float $bounceRatio;

	#[ORM\Column(name: 'unsubscribed_ratio', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => '0.0'])]
	private ?float $unsubscribedRatio;

	#[ORM\Column(name: 'click_ratio', type: 'decimal', precision: 16, scale: 2, nullable: true, options: ['default' => '0.0'])]
	private ?float $clickRatio;

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getHsMarketingEmail(): ?string
	{
		return $this->hsMarketingEmail;
	}

	/**
	 * @return mixed
	 */
	public function setHsMarketingEmail(string $hsMarketingEmail): self
	{
		$this->hsMarketingEmail = $hsMarketingEmail;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getSuccessfulDelivery(): ?int
	{
		return $this->successfulDelivery;
	}

	/**
	 * @return mixed
	 */
	public function setSuccessfulDelivery(?int $successfulDelivery): self
	{
		$this->successfulDelivery = $successfulDelivery;

		return $this;
	}

	public function getOptInOut(): ?string
	{
		return $this->optInOut;
	}

	/**
	 * @return mixed
	 */
	public function setOptInOut(?string $optInOut): self
	{
		$this->optInOut = $optInOut;

		return $this;
	}

	public function getArchived(): ?bool
	{
		return $this->archived;
	}

	/**
	 * @return mixed
	 */
	public function setArchived(bool $archived): self
	{
		$this->archived = $archived;

		return $this;
	}

	public function getCreatedDate(): ?\DateTimeInterface
	{
		return $this->createdDate;
	}

	/**
	 * @return mixed
	 */
	public function setCreatedDate(?\DateTimeInterface $createdDate): self
	{
		$this->createdDate = $createdDate;

		return $this;
	}

	public function getPublishDate(): ?\DateTimeInterface
	{
		return $this->publishDate;
	}

	/**
	 * @return mixed
	 */
	public function setPublishDate(?\DateTimeInterface $publishDate): self
	{
		$this->publishDate = $publishDate;

		return $this;
	}

	public function getUpdatedDate(): ?\DateTimeInterface
	{
		return $this->updatedDate;
	}

	/**
	 * @return mixed
	 */
	public function setUpdatedDate(?\DateTimeInterface $updatedDate): self
	{
		$this->updatedDate = $updatedDate;

		return $this;
	}

	public function getSentCount(): ?int
	{
		return $this->sentCount;
	}

	/**
	 * @return mixed
	 */
	public function setSentCount(?int $sentCount): self
	{
		$this->sentCount = $sentCount;

		return $this;
	}

	public function getOpenCount(): ?int
	{
		return $this->openCount;
	}

	/**
	 * @return mixed
	 */
	public function setOpenCount(?int $openCount): self
	{
		$this->openCount = $openCount;

		return $this;
	}

	public function getDeliveredCount(): ?int
	{
		return $this->deliveredCount;
	}

	/**
	 * @return mixed
	 */
	public function setDeliveredCount(?int $deliveredCount): self
	{
		$this->deliveredCount = $deliveredCount;

		return $this;
	}

	public function getBounceCount(): ?int
	{
		return $this->bounceCount;
	}

	/**
	 * @return mixed
	 */
	public function setBounceCount(?int $bounceCount): self
	{
		$this->bounceCount = $bounceCount;

		return $this;
	}

	public function getUnsubscriberCount(): ?int
	{
		return $this->unsubscriberCount;
	}

	/**
	 * @return mixed
	 */
	public function setUnsubscriberCount(?int $unsubscriberCount): self
	{
		$this->unsubscriberCount = $unsubscriberCount;

		return $this;
	}

	public function getClickCount(): ?int
	{
		return $this->clickCount;
	}

	/**
	 * @return mixed
	 */
	public function setClickCount(?int $clickCount): self
	{
		$this->clickCount = $clickCount;

		return $this;
	}

	public function getOpenRatio(): ?string
	{
		return $this->openRatio;
	}

	/**
	 * @return mixed
	 */
	public function setOpenRatio(?string $openRatio): self
	{
		$this->openRatio = $openRatio;

		return $this;
	}

	public function getDeliveredRatio(): ?string
	{
		return $this->deliveredRatio;
	}

	/**
	 * @return mixed
	 */
	public function setDeliveredRatio(?string $deliveredRatio): self
	{
		$this->deliveredRatio = $deliveredRatio;

		return $this;
	}

	public function getBounceRatio(): ?string
	{
		return $this->bounceRatio;
	}

	/**
	 * @return mixed
	 */
	public function setBounceRatio(?string $bounceRatio): self
	{
		$this->bounceRatio = $bounceRatio;

		return $this;
	}

	public function getUnsubscribedRatio(): ?string
	{
		return $this->unsubscribedRatio;
	}

	/**
	 * @return mixed
	 */
	public function setUnsubscribedRatio(?string $unsubscribedRatio): self
	{
		$this->unsubscribedRatio = $unsubscribedRatio;

		return $this;
	}

	public function getClickRatio(): ?string
	{
		return $this->clickRatio;
	}

	/**
	 * @return mixed
	 */
	public function setClickRatio(?string $clickRatio): self
	{
		$this->clickRatio = $clickRatio;

		return $this;
	}
}
