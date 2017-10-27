<?php

namespace TMSolution\MenuBundle\Form\Type\Tools\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Flexix\GeneratorBundle\Transformer\ArrayToJSONStringTransformer;

class InsertFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('name',null,  ['label' => 'tools.menu_item.name'])            
            ->add('route',null,  ['label' => 'tools.menu_item.route'])            
            ->add('routeParameters',null,  ['label' => 'tools.menu_item.route_parameters'])            
            ->add('position',null,  ['label' => 'tools.menu_item.position'])                
            ->add('parent',EntityType::class, [
                    'label' => 'tools.menu_item.parent', 
                    'class'=>'TMSolutionMenuBundle:MenuItem',
                    'query_builder' => function($repository) {
                     $qb = $repository->createQueryBuilder('u');
                     $qb->setMaxResults(100);
                     return $qb;},
                     'empty_data' => NULL,
                     'required' => false,
                     'choice_translation_domain' => true
                    ])                
            ->add('root',EntityType::class, [
                    'label' => 'tools.menu_item.root', 
                    'class'=>'TMSolutionMenuBundle:MenuItem',
                    'query_builder' => function($repository) {
                     $qb = $repository->createQueryBuilder('u');
                     $qb->setMaxResults(100);
                     return $qb;},
                     'empty_data' => NULL,
                     'required' => false,
                     'choice_translation_domain' => true
                    ]);            
             $builder->get('routeParameters')
            ->addModelTransformer(new ArrayToJSONStringTransformer());;
            
            
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TMSolution\MenuBundle\Entity\MenuItem',
            'attr' => array('novalidate' => 'novalidate'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tools_menu-item_insert';
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }


}

