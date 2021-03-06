<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\CoreBundle\Form\Type\BooleanType;

class BooleanFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('type', $data) || !array_key_exists('value', $data)) {
            return;
        }

        if (is_array($data['value'])) {
            $values = [];
            foreach ($data['value'] as $v) {
                if (!in_array($v, [BooleanType::TYPE_NO, BooleanType::TYPE_YES])) {
                    continue;
                }

                $values[] = (BooleanType::TYPE_YES == $v);
            }

            if (0 == count($values)) {
                return;
            }

            $queryBuilder = new \Elastica\Query\Builder();
            $queryBuilder
                ->fieldOpen('terms')
                    ->field($field, $values)
                ->fieldClose();

            $query->addMust($queryBuilder);
        } else {
            if (!in_array($data['value'], [BooleanType::TYPE_NO, BooleanType::TYPE_YES])) {
                return;
            }

            $queryBuilder = new \Elastica\Query\Builder();
            $queryBuilder
                ->fieldOpen('term')
                    ->field($field, (BooleanType::TYPE_YES == $data['value']))
                ->fieldClose();

            $query->addMust($queryBuilder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return ['sonata_type_filter_default', [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'operator_type' => 'hidden',
            'operator_options' => [],
            'label' => $this->getLabel(),
        ]];
    }
}
