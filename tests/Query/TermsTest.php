<?php

declare(strict_types=1);

/*
 * This file is part of Solr Client Symfony package.
 *
 * (c) ingatlan.com Zrt. <fejlesztes@ingatlan.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace iCom\SolrClient\Tests\Query;

use iCom\SolrClient\Query\Terms;
use PHPUnit\Framework\TestCase;

/**
 * @covers \iCom\SolrClient\Query\Terms
 */
final class TermsTest extends TestCase
{
    /** @test */
    public function it_throws_exception_on_invalid_method(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Terms::create('id', [])->method('method');
    }

    /**
     * @test
     * @dataProvider provideValidTerms
     */
    public function it_creates_terms_query_string(\Closure $terms, string $expectedQuery): void
    {
        $this->assertSame($expectedQuery, (string) $terms());
    }

    public function provideValidTerms(): iterable
    {
        yield 'field' => [
            static function (): Terms { return Terms::create('id', []); },
            '{!terms f=id}',
        ];

        yield 'method' => [
            static function (): Terms { return Terms::create('id', [])->method('termsFilter'); },
            '{!terms f=id method=termsFilter}',
        ];

        yield 'separator-comma' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->separator(','); },
            '{!terms f=id separator=","}1,2,3',
        ];

        yield 'separator-space' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->separator(' '); },
            '{!terms f=id separator=" "}1 2 3',
        ];

        yield 'separator-double-quote' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->separator('"'); },
            '{!terms f=id separator="\""}1"2"3',
        ];

        yield 'separator-single-quote-with-slash' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->separator("'"); },
            sprintf("{!terms f=id separator=%s%s%s}1'2'3", '"', "\'", '"'),
        ];

        yield 'values' => [
            static function (): Terms { return Terms::create('id', ['doc1', 'doc2', 'doc3']); },
            '{!terms f=id}doc1,doc2,doc3',
        ];

        yield 'cache-true' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->cache(true); },
            '{!terms f=id cache=true}1,2,3',
        ];

        yield 'cache-false' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->cache(false); },
            '{!terms f=id cache=false}1,2,3',
        ];

        yield 'all' => [
            static function (): Terms { return Terms::create('id', [1, 2, 3])->method('termsFilter')->separator(' ')->cache(false); },
            '{!terms f=id method=termsFilter separator=" " cache=false}1 2 3',
        ];
    }
}
