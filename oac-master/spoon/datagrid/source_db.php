<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package     spoon
 * @subpackage  datagrid
 *
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @since       1.0.0
 */

/**
 * This class is used for datagrids based on database sources.
 *
 * @package     spoon
 * @subpackage  datagrid
 *
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @since       1.0.0
 */
class SpoonDatagridSourceDB extends SpoonDatagridSource
{
    /**
     * SpoonDatabase instance
     *
     * @var SpoonDatabase
     */
    private $db;

    /**
     * Query to calculate the number of results
     *
     * @var string|null
     */
    private $numResultsQuery;

    /**
     * Custom parameters for the numResults query
     *
     * @var array
     */
    private $numResultsQueryParameters = [];

    /**
     * Query to fetch the results
     *
     * @var string
     */
    private $query;

    /**
     * Custom parameters for the query
     *
     * @var array
     */
    private $queryParameters = [];

    /**
     * Class constructor.
     *
     * @param SpoonDatabase $dbConnection      The database connection.
     * @param string        $query             The query to execute.
     * @param string|null   $numResultsQuery   The query to use to retrieve the number of results.
     */
    public function __construct(SpoonDatabase $dbConnection, $query, $numResultsQuery = null)
    {
        // Database connection
        $this->db = $dbConnection;

        // Set queries
        $this->setQuery($query, $numResultsQuery);
    }

    /**
     * Get the list of columns.
     *
     * @return array
     */
    public function getColumns()
    {
        // Has results
        if ($this->numResults === 0) {
            return [];
        }

        // Build query
        switch ($this->db->getDriver()) {
            case 'mysql':
                $query = (stripos($this->query, 'LIMIT ') !== false) ? $this->query : $this->query . ' LIMIT 1';
                break;

            default:
                throw new SpoonDataGridException('No datagrid support has been written for this database backend (' . $this->db->getDriver() . ')');
        }

        // Fetch record
        $record = $this->db->getRecord($query, $this->queryParameters);

        // Validar si el registro es válido
        if (!is_array($record) || empty($record)) {
            throw new Exception("Error: No se pudo obtener columnas de los datos proporcionados.");
        }

        // Fetch columns
        return array_keys($record);
    }

    /**
     * Fetch the data as an array.
     *
     * @param int|null    $offset The offset to start from.
     * @param int|null    $limit  The maximum number of items to retrieve.
     * @param string|null $order  The column to order on.
     * @param string|null $sort   The sorting method.
     * @return array
     */
    public function getData($offset = null, $limit = null, $order = null, $sort = null)
    {
        $query = $this->query;

// Lista blanca de columnas permitidas (opcional, pero recomendado)
$allowedColumns = ['id', 'name', 'email', 'created_at']; // Agrega las columnas permitidas aquí

// Verificar si la columna es válida
if ($order !== null && in_array($order, $allowedColumns, true)) {
    $sort = strtoupper($sort);
    if (!in_array($sort, ['ASC', 'DESC'], true)) {
        $sort = 'ASC'; // Valor por defecto si el usuario envía algo inválido
    }
    $query .= ' ORDER BY ' . $order . ' ' . $sort;
}


        // Offset & limit defined
        if ($offset !== null && $limit !== null) {
            if (!is_int($offset) || !is_int($limit) || $offset < 0 || $limit <= 0) {
                throw new Exception("Error: Offset y limit deben ser enteros positivos.");
            }
            $query .= ' LIMIT ' . intval($offset) . ', ' . intval($limit);
        }

        // Fetch data
        return (array) $this->db->getRecords($query, $this->queryParameters);
    }

    /**
     * Set the number of results.
     */
    private function setNumResults()
    {
        // Based on resultsQuery
        if (!empty($this->numResultsQuery)) {
            $this->numResults = (int) $this->db->getVar($this->numResultsQuery, $this->numResultsQueryParameters);
        } else {
            // Based on regular query
            $this->numResults = (int) $this->db->getNumRows($this->query, $this->queryParameters);
        }
    }

    /**
     * Set the queries.
     *
     * @param string      $query           The query to execute.
     * @param string|null $numResultsQuery The query to use to retrieve the number of results.
     */
    private function setQuery($query, $numResultsQuery = null)
    {
        // Query with parameters
        if (is_array($query) && count($query) > 1 && isset($query[0], $query[1])) {
            // Remove trailing semicolon(s) to enable adding "ORDER BY" etc.
            $this->query = rtrim((string) $query[0], ';');
            $this->queryParameters = (array) $query[1];
        } else {
            $this->query = rtrim((string) $query, ';');
        }

        // NumResults query with parameters
        if (is_array($numResultsQuery) && count($numResultsQuery) > 1 && isset($numResultsQuery[0], $numResultsQuery[1])) {
            $this->numResultsQuery = rtrim((string) $numResultsQuery[0], ';');
            $this->numResultsQueryParameters = (array) $numResultsQuery[1];
        } elseif ($numResultsQuery !== null) {
            $this->numResultsQuery = (string) $numResultsQuery;
        } else {
            $this->numResultsQuery = null;
        }

        // Set num results
        $this->setNumResults();
    }
}
