<?php

namespace BookBundle\Form;


use BookBundle\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isbn', TextType::class)
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('authors', CollectionType::class, array(
                'entry_type' =>  AuthorType::class
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class
        ]);
    }

    public function getName()
    {
        return 'Book';
    }
}