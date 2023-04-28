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
            ->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $taxNumber = $data['taxNumber'];
				
				$countryCode = substr($taxNumber, 0, 2);
				
				switch ($countryCode) {
					case 'DE':
					    $messageErrorTemplate = 'DEXXXXXXXXX - for residents of Germany';
						break;
					case 'IT':
						$messageErrorTemplate = 'ITXXXXXXXXXXX - for residents of Italy';
						break;
					case 'GR':
						$messageErrorTemplate = 'GRXXXXXXXXX - for residents of Greece';
						break;
					default:
						$messageErrorTemplate = 'YY + several X numbers, first two characters YY are the country code';
				}				

                if (!$this->isValidTaxNumber($taxNumber)) {
                    $form['taxNumber']->addError(new FormError("Invalid tax number. 
					Please use template like: $messageErrorTemplate, X is any digit from 0 to 9."));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    private function isValidTaxNumber($taxNumber)
    {
        if (preg_match('/^(DE|IT|GR)\d{9,12}$/', $taxNumber)) {
            return true;
        }

        return false;
    }
}

?>