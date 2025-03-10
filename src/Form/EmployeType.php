<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Enum\EmployeStatut;


class EmployeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('nom')
      ->add('prenom')
      ->add('email', EmailType::class)
      ->add('statut', ChoiceType::class, [
        'choices' => [
          'CDI' => EmployeStatut::Cdi,
          'CDD' => EmployeStatut::Cdd,
          'Freelance' => EmployeStatut::Freelance,
        ],
        'expanded' => false,
        'multiple' => false,
        'choice_label' => function (?EmployeStatut $status) {
          return $status?->getLabel();
        },
      ])
      ->add('dateArrivee', DateType::class, ['widget' => 'single_text', 'label' => 'Date d\'entrée'])
      ->add('admin', ChoiceType::class, [
        'label' => 'Rôle',
        'choices' => [
          'Collaborateur' => false,
          'Chef de projet' => true,
        ],
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Employe::class,
    ]);
  }
}
