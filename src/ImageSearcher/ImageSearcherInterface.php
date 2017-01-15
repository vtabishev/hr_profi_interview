<?php
namespace Interview\ImageSearcher;

interface ImageSearcherInterface
{
    /**
     * Выполняет поисковый запрос
     *
     * @param string $searchQuery - Строка поиска
     * @param int    $offset      - Смещение
     *
     * @return array
     */
    public function search($searchQuery, $offset = 0);

    /**
     * Шаг для смещения
     *
     * @return int
     */
    public function getStep();

    /**
     * Возращает номер первой страницы
     *
     * @return int
     */
    public function getInitialPageNumber();
}