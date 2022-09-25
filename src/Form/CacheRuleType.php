<?php

namespace App\Form;

use App\Entity\CacheRule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CacheRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('judgeType', ChoiceType::class, [
                'choices'  => [
                    CacheRule::JUDGE_TYPE_SCHEME_HOST => CacheRule::JUDGE_TYPE_SCHEME_HOST,
                ],
            ])
            ->add('judgeCond')
            ->add('resType', ChoiceType::class, [
                'choices'  => [
                    CacheRule::RES_TYPE_URL_MATCH => CacheRule::RES_TYPE_URL_MATCH,
                    CacheRule::RES_TYPE_SCHEME_HOST_MATCH => CacheRule::RES_TYPE_SCHEME_HOST_MATCH,
                ],
            ])

            ->add('resCond')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CacheRule::class,
        ]);
    }
}
