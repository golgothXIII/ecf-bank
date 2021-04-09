<?php

namespace App\Entity;

use App\Repository\BankerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BankerRepository::class)
 */
class Banker
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $validation_code;

    /**
     * @ORM\OneToMany(targetEntity=Account::class, mappedBy="banker")
     */
    private $accounts;

    /**
     * @ORM\OneToMany(targetEntity=Beneficiary::class, mappedBy="banker")
     */
    private $beneficiaries;

    /**
     * @ORM\OneToOne(targetEntity=User::class, mappedBy="banker", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->beneficiaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValidationCode(): ?string
    {
        return $this->validation_code;
    }

    public function setValidationCode(string $validation_code): self
    {
        $this->validation_code = $validation_code;

        return $this;
    }

    /**
     * @return Collection|Account[]
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts[] = $account;
            $account->setBanker($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getBanker() === $this) {
                $account->setBanker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Beneficiary[]
     */
    public function getBeneficiaries(): Collection
    {
        return $this->beneficiaries;
    }

    public function addBeneficiary(Beneficiary $beneficiary): self
    {
        if (!$this->beneficiaries->contains($beneficiary)) {
            $this->beneficiaries[] = $beneficiary;
            $beneficiary->setBanker($this);
        }

        return $this;
    }

    public function removeBeneficiary(Beneficiary $beneficiary): self
    {
        if ($this->beneficiaries->removeElement($beneficiary)) {
            // set the owning side to null (unless already changed)
            if ($beneficiary->getBanker() === $this) {
                $beneficiary->setBanker(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setBanker(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getBanker() !== $this) {
            $user->setBanker($this);
        }

        $this->user = $user;

        return $this;
    }
}
