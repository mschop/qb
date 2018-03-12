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
//            print_r($qb->getParams());
    }

    protected function buildQuery()
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

        return $qb;
    }

    public function test_performance()
    {
        $start = microtime(true);
        for ($x = 0; $x < 1000; $x++) {
            $qb = $this->buildQuery();
            $qb->getQuery();
            $qb->getParams();
        }
        $end = microtime(true);
        $this->assertLessThan(0.03, $end - $start);

    }
}
