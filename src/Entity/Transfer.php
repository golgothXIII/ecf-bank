<?php

namespace App\Entity;

use App\Repository\TransferRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TransferRepository::class)
 */
class Transfer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var string A "Y-m-d H:i:s" formatted value
     */
    private $transfer_date;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive(
     *     message = "Le montant doit etre positif"
     * )
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transfers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity=Beneficiary::class, inversedBy="transfers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $beneficiary;

    /**
     * @ORM\Column(type="string", length=35)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=140, nullable=true)
     */
    private $label;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransferDate(): ?\DateTimeInterface
    {
        return $this->transfer_date;
    }

    public function setTransferDate(\DateTimeInterface $transfer_date): self
    {
        $this->transfer_date = $transfer_date;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getBeneficiary(): ?Beneficiary
    {
        return $this->beneficiary;
    }

    public function setBeneficiary(?Beneficiary $beneficiary): self
    {
        $this->beneficiary = $beneficiary;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
