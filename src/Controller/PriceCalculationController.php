<?php

namespace App\Controller;

use App\Form\PriceCalculationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PriceCalculationController extends AbstractController
{
    const TAX_RATES = [
        'DE' => 0.19,
        'IT' => 0.22,
        'GR' => 0.24,
    ];

    const PRODUCT_PRICES = [
        'headphones' => 100,
        'phone_case' => 20,
    ];
	
    public function index(Request $request)
    {
        $form = $this->createForm(PriceCalculationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData()['product'];
            $taxNumber = $form->getData()['taxNumber'];
            $countryCode = substr($taxNumber, 0, 2);
            
            $taxRate = $this->getTaxRate($countryCode);
            $productPrice = $this->getProductPrice($product);

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
	
    private function getTaxRate($countryCode)
    {
        return self::TAX_RATES[$countryCode] ?? 0;
    }

    private function getProductPrice($product)
    {
        return self::PRODUCT_PRICES[$product] ?? 0;
    }	
}

?>