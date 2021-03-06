<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Tests\Filter\ORM;

use Sonata\AdminBundle\Filter\ORM\CallbackFilter;

class CallbackFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterClosure()
    {
        $builder = new QueryBuilder;

        $filter = new CallbackFilter;
        $filter->initialize('field_name', array(
            'callback' => function($builder, $alias, $field, $value) {
                $builder->andWhere(sprintf('CUSTOM QUERY %s.%s', $alias, $field));
                $builder->setParameter('value', $value);
            }
        ));

        $filter->filter($builder, 'alias', 'field', 'myValue');

        $this->assertEquals(array('CUSTOM QUERY alias.field'), $builder->query);
        $this->assertEquals(array('value' => 'myValue'), $builder->parameters);
    }

    public function testFilterMethod()
    {
        $builder = new QueryBuilder;

        $filter = new CallbackFilter;
        $filter->initialize('field_name', array(
            'callback' => array($this, 'customCallback')
        ));

        $filter->filter($builder, 'alias', 'field', 'myValue');

        $this->assertEquals(array('CUSTOM QUERY alias.field'), $builder->query);
        $this->assertEquals(array('value' => 'myValue'), $builder->parameters);
    }

    public function customCallback($builder, $alias, $field, $value) {
        $builder->andWhere(sprintf('CUSTOM QUERY %s.%s', $alias, $field));
        $builder->setParameter('value', $value);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFilterException()
    {
        $builder = new QueryBuilder;

        $filter = new CallbackFilter;
        $filter->initialize('field_name', array());

        $filter->filter($builder, 'alias', 'field', 'myValue');
    }
}