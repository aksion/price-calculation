<?php

namespace App\Controller;

use App\Form\PriceCalculationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PriceCalculationController extends AbstractController
{
    /**
     * @Route("/price-calculation", name="price_calculation")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(PriceCalculationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData()['product'];
            $taxNumber = $form->getData()['taxNumber'];
            $countryCode = substr($taxNumber, 0, 2);
            $taxRate = 0;
			
            switch ($countryCode) {
                case 'DE':
                    $taxRate = 0.19;
                    break;
                case 'IT':
                    $taxRate = 0.22;
                    break;
                case 'GR':
                    $taxRate = 0.24;
                    break;
            }

            $productPrice = 0;

            switch ($product) {
                case 'headphones':
                    $productPrice = 100;
                    break;
                case 'phone_case':
                    $productPrice = 20;
                    break;
            }

            $taxAmount = $productPrice * $taxRate;
            $totalPrice = $productPrice + $taxAmount;

            return $this->render('price_calculation/result.html.twig', [
                'product' => ucfirst(str_replace('_', ' ', $product)),
                'productPrice' => $productPrice,
                'taxRate' => $taxRate * 100,
                'taxAmount' => $taxAmount,
                'totalPrice' => $totalPrice,
            ]);
        }

        return $this->render('price_calculation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

?>