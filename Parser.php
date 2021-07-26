<?php

namespace Codememory\Components\SqlParser;

use PHPSQLParser\PHPSQLParser;

/**
 * Class Parser
 *
 * @package Codememory\Components\SqlParser
 *
 * @author  Codememory
 */
class Parser
{

    /**
     * @var string
     */
    private string $sql;

    /**
     * @var PHPSQLParser
     */
    private PHPSQLParser $parser;

    /**
     * @var Lookup
     */
    private Lookup $lookup;

    /**
     * Parser constructor.
     *
     * @param string $sql
     */
    public function __construct(string $sql)
    {

        $this->sql = $sql;
        $this->parser = new PHPSQLParser();
        $this->lookup = new Lookup();

    }

    /**
     * @return array
     */
    public function getTableNames(): array
    {

        return $this->lookup->lookupTableNames(
            $this->parser->parse($this->sql)
        )->getResult();

    }

    /**
     * @return array
     */
    public function getColumnNames(): array
    {

        return $this->lookup->lookupColumnNames(
            $this->parser->parse($this->sql)
        )->getResult();

    }

    /**
     * @param string|null $alias
     *
     * @return string|null
     */
    public function getTableNameByAlias(?string $alias): ?string
    {

        $tables = $this->getTableNames();

        if (null === $alias) {
            $firstDataTable = $tables[array_key_first($tables)] ?? false;

            if (false !== $firstDataTable) {
                return $firstDataTable['table-name'];
            }

            return null;
        }

        foreach ($tables as $table) {
            if ($table['alias-table'] === $alias) {
                return $table['table-name'];
            }
        }

        return null;

    }

}