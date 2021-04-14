<?php

namespace App\Entity;

use App\Repository\BeneficiaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BeneficiaryRepository::class)
 * @ORM\Table(
 *      name="Beneficiary",
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"IBAN", "customer_id"})}
 * )
 * @UniqueEntity(
 *     fields={ "IBAN", "customer" },
 *     message= "IBAN déjà enregistré dans vos bénéficiaires."
 * )
 */
class Beneficiary
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\Iban(
     *     message = "Ce n'est pas un numéro de compte bancaire international (IBAN) valide"
     * )
     */
    private $IBAN;

    /**
     * @ORM\Column(type="string", length=11)
     * @Assert\Bic(
     *     message = "Ce n'est pas un code d'identification d'entreprise (BIC) valide"
     * )
     */
    private $BIC;

    /**
     * @ORM\OneToMany(targetEntity=Transfer::class, mappedBy="beneficiary")
     */
    private $transfers;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="Beneficiaries")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=Banker::class, inversedBy="beneficiaries")
     */
    private $banker;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isValidated;

    public function __construct()
    {
        $this->transfers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getIBAN(): ?string
    {
        return $this->IBAN;
    }

    public function setIBAN(string $IBAN): self
    {
        $this->IBAN = $IBAN;

        return $this;
    }

    public function getBIC(): ?string
    {
        return $this->BIC;
    }

    public function setBIC(string $BIC): self
    {
        $this->BIC = $BIC;

        return $this;
    }

    /**
     * @return Collection|Transfer[]
     */
    public function getTransfers(): Collection
    {
        return $this->transfers;
    }

    public function addTransfer(Transfer $transfer): self
    {
        if (!$this->transfers->contains($transfer)) {
            $this->transfers[] = $transfer;
            $transfer->setBeneficiary($this);
        }

        return $this;
    }

    public function removeTransfer(Transfer $transfer): self
    {
        if ($this->transfers->removeElement($transfer)) {
            // set the owning side to null (unless already changed)
            if ($transfer->getBeneficiary() === $this) {
                $transfer->setBeneficiary(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getBanker(): ?Banker
    {
        return $this->banker;
    }

    public function setBanker(?Banker $banker): self
    {
        $this->banker = $banker;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
}
