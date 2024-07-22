<?php

namespace App\Controller;

use App\Entity\Facturation;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/create-checkout-session/{ticketId}', name: 'create_checkout_session')]
    public function createCheckoutSession($ticketId)
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id ' . $ticketId);
        }

        Stripe::setApiKey('sk_test_51PY4LwDzMg6ykNYFOM27Sh0Y1KylOwPYqlSiOIf0az66NgCq00E1l2npAApETaO4K4ZNZYaAq3zdaxWqiXZkCpy500JPL5qTMv');

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Ticket #' . $ticket->getId(),
                    ],
                    'unit_amount' => $this->calculateAmount($ticket),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', ['ticketId' => $ticket->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        $ticketId = $request->get('ticketId');
        $ticket = $this->entityManager->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id ' . $ticketId);
        }

        // Update the ticket status to 'paid'
        $ticket->setStatus('paid');

        // Prepare the Facturation data
        $facturationData = [
            'ticket_id' => $ticket->getId(),
            'technician_id' => $ticket->getTechnicien()->getId(),
            'creation_date' => $ticket->getDateStart()->format('d/m/Y H:i'),
            'closing_date' => $ticket->getDateEnd()->format('d/m/Y H:i'),
            'stocks_used' => []
        ];

        foreach ($ticket->getIntervention()->getInterventionStocks() as $interventionStock) {
            $facturationData['stocks_used'][] = [
                'stock_label' => $interventionStock->getStock()->getLabel(),
                'quantity_used' => $interventionStock->getQuantityUsed(),
                'description' => $interventionStock->getDescription(),
                'used_at' => $interventionStock->getUsedAt()->format('d/m/Y H:i')
            ];
        }

        // Create Facturation entity
        $facturation = new Facturation();
        $facturation->setValue(json_encode($facturationData));
        $facturation->setDescription('Payment successful for ticket #' . $ticket->getId());
        $facturation->setInvoiceNumber('INV' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT));
        $facturation->setInvoiceDate(new \DateTime());
        $facturation->setDueDate((new \DateTime())->modify('+30 days'));
        $facturation->setTotalAmount(100); // Set your total amount
        $facturation->setTaxAmount(10); // Set your tax amount
        $facturation->setClient($ticket->getUser()); // Updated to getUser

        $this->entityManager->persist($facturation);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_tickets');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Payment was cancelled.');
        return $this->redirectToRoute('app_tickets');
    }

    private function calculateAmount(Ticket $ticket): int
    {
        // Calculate the amount based on your pricing logic
        return 1000; // For example, 10 USD
    }
}



