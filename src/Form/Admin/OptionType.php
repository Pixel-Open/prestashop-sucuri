<?php
/**
 * Copyright (C) 2023 Pixel Développement
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pixel\Module\Sucuri\Form\Admin;

use Pixel\Module\Sucuri\Helper\Config;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class OptionType extends AbstractType
{
    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @var Config $config
     */
    protected $config;

    /**
     * @param TranslatorInterface $translator
     * @param Config $config
     */
    public function __construct(TranslatorInterface $translator, Config $config)
    {
        $this->translator = $translator;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $option = $options['label'] ?? 'value';
        $choices = $this->getChoiceOptions($option);

        $field = [
            'label' => $option,
            'help' => $options['label_help_box'] ?? '',
            'required' => false,
            'constraints' => [
                new CleanHtml([
                    'message' => $this->translator->trans(
                        'Option is invalid',
                        [],
                        'Modules.Pixelsucuri.Admin'
                    ),
                ]),
            ],
        ];

        if (is_array($choices)) {
            $field['choices'] = $choices;
        }

        $builder->add('value', $choices ? ChoiceType::class : TextType::class, $field);
    }

    /**
     * Retrieve choice options
     *
     * @param string $option
     * @return string[]|null
     */
    public function getChoiceOptions(string $option): ?array
    {
        return $this->config->getOption($option)['options'] ?? null;
    }
}
