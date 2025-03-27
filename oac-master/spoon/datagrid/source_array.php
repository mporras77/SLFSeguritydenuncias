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
 * This class is used for datagrids based on array sources.
 *
 * @package     spoon
 * @subpackage  datagrid
 *
 * @author      Davy Hellemans <davy@spoon-library.com>
 * @since       1.0.0
 */
class SpoonDatagridSourceArray extends SpoonDatagridSource
{
    /**
     * Static ordering (for compare method)
     *
     * @var string
     */
    public static $order;

    /**
     * Class constructor.
     *
     * @param array $array The data.
     */
    public function __construct(array $array)
    {
        // Set data
        $this->data = $array;

        // Set number of results
        $this->setNumResults();
    }

    /**
     * Apply the sorting method.
     *
     * @param array $firstArray  The first element.
     * @param array $secondArray The second element.
     * @return int
     */
    public static function applySorting($firstArray, $secondArray)
    {
        // Verificar si la clave de orden existe en los elementos
        if (!isset($firstArray[self::$order]) || !isset($secondArray[self::$order])) {
            return 0;
        }

        if ($firstArray[self::$order] < $secondArray[self::$order]) {
            return -1;
        } elseif ($firstArray[self::$order] > $secondArray[self::$order]) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Retrieve the columns.
     *
     * @return array
     */
    public function getColumns()
    {
        // Si no hay resultados, retorna un array vacío
        if ($this->numResults === 0) {
            return [];
        }

        // Obtener las claves del primer elemento del array
        $firstRow = reset($this->data);
        
        // Verificar que el primer elemento sea un array válido
        if (!is_array($firstRow)) {
            throw new Exception("Error: La primera fila de los datos no es un array válido.");
        }

        return array_keys($firstRow);
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
        $data = $this->data;

        // Sorting
        if ($order !== null) {
            // Static variable for sorting
            self::$order = $order;

            // Apply sorting
            uasort($data, ['SpoonDatagridSourceArray', 'applySorting']);

            // Reverse order if needed
            if ($sort === 'desc') {
                $data = array_reverse($data, true);
            }
        }

        // Apply offset and limit
        if ($offset !== null && $limit !== null) {
            if (!is_int($offset) || !is_int($limit) || $offset < 0 || $limit <= 0) {
                throw new Exception("Error: Offset y limit deben ser enteros positivos.");
            }
            $data = array_slice($data, $offset, $limit, true);
        }

        return $data;
    }

    /**
     * Sets the number of results.
     */
    private function setNumResults()
    {
        $this->numResults = count($this->data);
    }
}
