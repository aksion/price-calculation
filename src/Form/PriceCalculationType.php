<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Event\PostSubmitEvent;

use Symfony\Component\Form\Extension\Core\EventListener\TransformationFailureListener;

class PriceCalculationType extends AbstractType
{
    private const ERROR_MESSAGE_TEMPLATES = [
        'DE' => 'DEXXXXXXXXX - for residents of Germany',
        'IT' => 'ITXXXXXXXXXXX - for residents of Italy',
        'GR' => 'GRXXXXXXXXX - for residents of Greece',
        'default' => 'YY + several X numbers, first two characters YY (DE/IT/GR) are the country code',
    ];
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', ChoiceType::class, [
                'choices' => [
                    'Headphones (100 euro)' => 'headphones',
                    'Phone case (20 euro)' => 'phone_case',
                ],
                'label' => 'Product'
            ])
            ->add('taxNumber', TextType::class, [
                'label' => 'Tax Number'
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'validateTaxNumber']);
    }	
	
    private function isValidTaxNumber($taxNumber)
    {
        if (preg_match('/^(IT)\d{11}$/', $taxNumber)) {
            return true;
        }
		
        if (preg_match('/^(DE|GR)\d{9}$/', $taxNumber)) {
            return true;
        }		

        return false;
    }
	
    public function validateTaxNumber(PostSubmitEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();
        $taxNumber = $data['taxNumber'];
        $countryCode = substr($taxNumber, 0, 2);

        $errorMessageTemplate = self::ERROR_MESSAGE_TEMPLATES[$countryCode] ?? self::ERROR_MESSAGE_TEMPLATES['default'];
        $errorMessage = "Invalid tax number. Please use template like: $errorMessageTemplate, X is any digit from 0 to 9.";

        if (!$this->isValidTaxNumber($taxNumber)) {
            $form['taxNumber']->addError(new FormError($errorMessage));
        }
    }	
}

?>