<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 * @ApiResource(
 *     subresourceOperations={
 *          "api_customers_invoices_get_subresource"={
 *              "normalization_context"={
 *                  "groups"={"invoices_subresource"}
 *              }
 *          }
 *     },
 *     itemOperations={
 *          "GET", "PUT", "DELETE", "PATCH", "increment"={
 *              "method"="post",
 *              "path"="/invoices/{id}/increment",
 *              "controller"="App\Controller\InvoiceIncrementationController",
 *              "openapi_context"={
 *                  "summary"="Increment the amount of an invoice.",
 *                  "description"="Increment the amount of an invoice. It's just to show how to make custom operations."
 *              }
 *          }
 *     },
 *     normalizationContext={
 *          "groups"={"invoices_read"}
 *     },
 *     denormalizationContext={
 *          "disable_type_enforcement"=true
 *     }
 * )
 */
class Invoice
{
    public const STATUS_SENT = "SENT";
    public const STATUS_PAID = "PAID";
    public const STATUS_CANCELLED = "CANCELLED";
    public const STATUS = [
        self::STATUS_SENT,
        self::STATUS_PAID,
        self::STATUS_CANCELLED
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The invoice's amount is mandatory.")
     * @Assert\Type(type="numeric", message="The invoice's amount must be numeric.")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The invoice's date is mandatory.")
     * @Assert\DateTime(message="The invoice's date format must be YYYY-MM-DD.")
     */
    private $sentAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The invoice's status is mandatory.")
     * @Assert\Choice(choices=Invoice::STATUS, message="Choose a valid status between SENT, PAID or CANCELLED.")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("invoices_read")
     * @Assert\NotBlank(message="The invoice's customer is mandatory.")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"invoices_read", "customers_read", "invoices_subresource"})
     * @Assert\NotBlank(message="The invoice's chrono is mandatory.")
     * @Assert\Type(type="integer", message="The invoice's chrono must be an integer.")
     */
    private $chrono;

    /**
     * Get the User related to the Customer of the Invoice.
     *
     * @Groups({"invoices_read", "invoices_subresource"})
     * @return User
     */
    public function getUser(): User
    {
        return $this->customer->getUser();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt($sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
