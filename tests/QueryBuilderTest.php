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
                    $qb->col('p', 'productId'),
                    $qb->col('pc', 'id')
                ),
                'pc'
            )
            ->rightJoin(
                'product_translations',
                $qb->and(
                    $qb->eq(
                        $qb->col('pt.productId'),
                        $qb->col('p.id')
                    ),
                    $qb->eq(
                        $qb->col('pt.languageId'),
                        10
                    ),
                    $qb->not($qb->col('pt.inactive'))
                )
            )
            ->where(
                $qb->eq(
                    $qb->col('products', 'manufacturerId'),
                    $qb->param('manufacturerId')
                ),
                $qb->not($qb->col('products.inactive'))
            )
            ->bind('manufacturerId', 15);

        echo($qb->getQuery());
        print_r($qb->getParams());
    }
}
