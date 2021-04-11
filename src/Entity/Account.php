<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $balance;

    /**
     * @ORM\OneToMany(targetEntity=Transfer::class, mappedBy="account")
     */
    private $transfers;

    /**
     * @ORM\ManyToOne(targetEntity=Banker::class, inversedBy="accounts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $banker;

    /**
     * @ORM\OneToOne(targetEntity=Customer::class, mappedBy="account", cascade={"persist", "remove"})
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $bank_account_id;

    public function __construct()
    {
        $this->transfers = new ArrayCollection();
        $this->balance = 0.0;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

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
            $transfer->setAccount($this);
        }

        return $this;
    }

    public function removeTransfer(Transfer $transfer): self
    {
        if ($this->transfers->removeElement($transfer)) {
            // set the owning side to null (unless already changed)
            if ($transfer->getAccount() === $this) {
                $transfer->setAccount(null);
            }
        }

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        // unset the owning side of the relation if necessary
        if ($customer === null && $this->customer !== null) {
            $this->customer->setAccount(null);
        }

        // set the owning side of the relation if necessary
        if ($customer !== null && $customer->getAccount() !== $this) {
            $customer->setAccount($this);
        }

        $this->customer = $customer;

        return $this;
    }

    public function getBankAccountId(): ?string
    {
        return $this->bank_account_id;
    }

    public function setBankAccountId(): self
    {
        // if id is set or bank_account_id already generate do nothing
        $this->id = 100235;
        if ( $this->id && ! $this->bank_account_id) {
            $begin = substr( date("ymd", time() ) , -5 );
            $end = substr("0000" . $this->id, -5 );
            $key = (int) ($begin . $end ) % 8;
            $this->bank_account_id = $begin . $end . $key;
        }

        return $this;
    }
}
