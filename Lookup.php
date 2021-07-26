<?php

namespace Codememory\Components\SqlParser;

use Generator;

/**
 * Class IterationHandler
 *
 * @package Codememory\Components\SqlParser
 *
 * @author  Codememory
 */
final class Lookup
{

    /**
     * @var array
     */
    private array $result = [];

    /**
     * @param array $lexers
     *
     * @return Lookup
     */
    public function lookupTableNames(array $lexers): Lookup
    {

        foreach ($this->iterationOverLexers($lexers) as [$key, $value]) {
            if (is_array($value)) {
                if (array_key_exists('expr_type', $value) && $value['expr_type'] === 'table') {
                    $this->result[] = [
                        'full-table-name' => $value['table'],
                        'table-name'      => $value['no_quotes']['parts'][0],
                        'alias-table'     => $value['alias']['name'] ?? null
                    ];
                }

                $this->lookupTableNames($value);
            }
        }

        return $this;

    }

    /**
     * @param array $lexers
     *
     * @return Lookup
     */
    public function lookupColumnNames(array $lexers): Lookup
    {

        foreach ($this->iterationOverLexers($lexers) as [$key, $value]) {
            if (is_array($value)) {
                if (array_key_exists('expr_type', $value)
                    && array_key_exists('no_quotes', $value)
                    && $value['expr_type'] === 'colref') {
                    $this->result[] = [
                        'table'          => count($value['no_quotes']['parts']) < 2 ? null : $value['no_quotes']['parts'][0],
                        'col-name'       => count($value['no_quotes']['parts']) < 2 ? $value['no_quotes']['parts'][0] : $value['no_quotes']['parts'][1],
                        'col-name-alias' => $value['alias']['name'] ?? null
                    ];
                }

                $this->lookupColumnNames($value);
            }
        }

        return $this;

    }

    /**
     * @return array
     */
    public function getResult(): array
    {

        return $this->result;

    }

    /**
     * @param array $lexers
     *
     * @return Generator
     */
    private function iterationOverLexers(array $lexers): Generator
    {

        foreach ($lexers as $key => $value) {
            yield [$key, $value];
        }

    }

}