<?php

namespace ComposableQB;


class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_happyPath()
    {
        $qb = new QueryBuilder();
        $qb = $qb
            ->from('products', 'p')
            ->select('id')
            ->select('manufacturerId')
            ->join(
                'product_categories',
                $qb->eq(
                    $qb->column('p', 'productId'),
                    $qb->column('pc', 'id')
                ),
                'pc'
            )
            ->rightOuterJoin(
                'product_translations',
                $qb->and(
                    $qb->eq(
                        $qb->column('pt', 'productId'),
                        $qb->column('p', 'id')
                    ),
                    $qb->eq(
                        $qb->column('pt', 'languageId'),
                        10
                    ),
                    $qb->not($qb->column('pt', 'inactive'))
                )
            )
            ->where(
                $qb->eq(
                    $qb->column('products', 'manufacturerId'),
                    $qb->param('manufacturerId')
                )
            );

        echo($qb->build()->getQuery());
    }
}
