<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;

class InvoiceIncrementationController
{
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(Invoice $data)
    {
        $this->invoiceRepository->incrementAmount($data);

        return $data;
    }
}
