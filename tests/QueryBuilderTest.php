<?php

namespace SecureMy;


use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public function test_happyPath()
    {
        $qb = QueryBuilder::create();
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
            ->rightJoin(
                'product_translations',
                $qb->and(
                    $qb->eq(
                        $qb->column('pt.productId'),
                        $qb->column('p.id')
                    ),
                    $qb->eq(
                        $qb->column('pt.languageId'),
                        10
                    ),
                    $qb->not($qb->column('pt.inactive'))
                )
            )
            ->where(
                $qb->eq(
                    $qb->column('products', 'manufacturerId'),
                    $qb->param('manufacturerId')
                ),
                $qb->not($qb->column('products.inactive'))
            )
            ->bind('manufacturerId', 15);

        echo($qb->getQuery());
        print_r($qb->getParams());
    }
}
