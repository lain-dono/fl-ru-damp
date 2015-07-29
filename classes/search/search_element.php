<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/stdf.php';

/**
 * Абстрактный класс элемента поиска.
 * Класс элемента может переопределить любые переменные и методы, среди них обяхательные:
 * Переменные:
 * - $name
 * Методы:
 * - setHtml().
 */
abstract class searchElement
{
    /**
     * Режим вывода результатов.
     * Построчный вывод - по одному элементу в строку с нумерацией строк.
     */
    const LAYOUT_LINE = 1;

    /**
     * Режим вывода результатов.
     * Блочный вывод - по три элемента в строку.
     */
    const LAYOUT_BLOCK = 2;

    /**
     * Режим вывода результатов.
     * Построчный вывод - по одному элементу в строку без нумерации строк.
     */
    const LAYOUT_ROW = 3;

    /**
     * Массив индексов элемента. Первый член массива должен быть главным индексом!
     *
     * @var array
     */
    protected $_indexes = array();

    /**
     * Префикс индекса (относительно базового).
     *
     * @see searchElement::setIndexes()
     *
     * @var string
     */
    protected $_indexPfx = '';

    /**
     * Суффикс индекса (относительно базового).
     *
     * @see searchElement::setIndexes()
     *
     * @var string
     */
    protected $_indexSfx = '';

    /**
     * Движок поиска или наследник от него.
     *
     * @see search
     *
     * @var object
     */
    protected $_engine;

    /**
     * Можно ли производить поиск по данному элементу сейчас (например, включил ли его юзер в настройках).
     *
     * @var bool
     */
    protected $_active = true;

    /**
     * Режим вывода результатов.
     *
     * @var int
     */
    public $layout = self::LAYOUT_LINE;

    /**
     * Дополнительные параметры подсветки найденных слов.
     *
     * @var array
     */
    protected $_opts = array(
        'before_match' => '<em>',
        'after_match' => '</em>',
        'limit' => 250,
        'exact_phrase' => false,
    );

    /**
     * Интерфейсное имя элемента поиска, например "Личные заметки".
     *
     * @var string
     */
    public $name = '';

    /**
     * Формы слов для вывода числа найденных документов.
     *
     * @var array
     */
    public $totalwords = array('совпадение', 'совпадения', 'совпадений');

    /**
     * Кусок поисковой фразы, используеый механизмом поиска для подсветки найденных совпадений.
     *
     * @var string
     */
    public $words = '';

    /**
     * Общее количество найденных документов.
     *
     * @var int
     */
    public $total = 0;

    /**
     * Сформатированная строка для вывода числа найденных документов.
     *
     * @var string
     */
    public $totalStr = '';

    /**
     * Массив идентификаторов найденных документов.
     *
     * @var array
     */
    public $matches = array();

    /**
     * Массив найденных документов в формате HTML-блоков для вывода в браузер.
     *
     * @var array
     */
    public $html = array();

    /**
     * Все следующие переменные полностью соотвествуют одноименным переменным класса {@link SphinxClient}
     * Элемент поиска может просто переопределить данные переменные:
     * а) прямо в декларации;
     * б) динамически, в перегруженном методе searchElement::setEngine() в самом начале.
     */
    protected $_host = SEARCHHOST;
    protected $_port = SEARCHPORT;
    protected $_offset = 0;
    protected $_limit = 5;
    protected $_mode = SPH_MATCH_ALL;
    protected $_weights = array();
    protected $_sort = SPH_SORT_ATTR_DESC;
    protected $_sortby = 'post_time';
    protected $_min_id = 0;
    protected $_max_id = 0;
    protected $_filtersV = array(); // array( array( "attr"=> $attribute, "values"=>$values) )
    protected $_filtersR = array(); // array( array( "attr"=> $attribute, "min"=>$min, "max"=>$max) )
    protected $_filtersRF = array(); // array( array( "attr"=> $attribute, "min"=>$min, "max"=>$max) )
    protected $_groupby = '';
    protected $_groupfunc = SPH_GROUPBY_DAY;
    protected $_groupsort = '@group desc';
    protected $_groupdistinct = '';
    protected $_maxmatches = 1000;
    protected $_cutoff = 0;
    protected $_retrycount = 0;
    protected $_retrydelay = 0;
    protected $_anchor = array(); // ( "attrlat"=>$attrlat, "attrlong"=>$attrlong, "lat"=>$lat, "long"=>$long )
    protected $_indexweights = array();
    protected $_ranker = SPH_RANK_PROXIMITY_BM25;
    protected $_maxquerytime = 0;
    protected $_fieldweights = array();
    protected $_overrides = array(); // ( "attr"=>$attrname, "type"=>$attrtype, "values"=>$values )
    protected $_select = '*';
    protected $_arrayresult = false;
    protected $_advanced = false; // Наш внутренний фильтр для внутренней обработки
    protected $_advanced_page = 0;
    protected $_advanced_limit = 5;

    /**
     * Конструктор.
     *
     * @param object $engine объект класса search, наследника SphinxClient.
     * @param bool   $active можно ли производить поиск по данному элементу сейчас (например, включил ли его юзер в настройках).
     */
    public function __construct($engine, $active = true)
    {
        $this->_active = $active;
        $this->_engine = $engine;
    }

    public function setUserLimit($limit)
    {
        $this->_limit = (int) $limit;
    }

    public function setServer($host, $port = 0)
    {
        $this->_host = $host;
        $this->_port = $port;
    }

    /**
     * Инициализирует параметры поиска данного элемента и производит поиск по заданной фразе.
     *
     * @param string $string поисковая фраза.
     * @param int    $page   номер текущей страницы (используется при поиске по конкретному элементу).
     */
    public function search($string, $page = 0)
    {
        if (!$this->isActive() || !$this->isAllowed()) {
            return;
        }
        // Максимальный лимит для выборки всех результатов поиска, реализовано для расширенного поиска
        if ($this->isAdvanced() !== false) {
            $this->_advanced_limit = $this->_limit;
            $this->_limit = $this->_maxmatches;
        }
        $this->setPage($page);
        $this->setEngine();
        $this->setIndexes();
        $this->resetResult();

        //print_r($this->_mode);exit;

        $this->setResult($this->_engine->Query($string, implode(';', $this->_indexes)));
        $this->setWords($string);
        // Возвращаем все на место
        if ($this->isAdvanced() !== false) {
            $this->_limit = $this->_advanced_limit;
        }
    }

    /**
     * Задает параметры limit и offset в соотвествии с номером текущей страницы.
     * $this->isAdvanced() - если идет расширенный поиск то в setPage нету смысла, так как может отсеится половина 
     * найденных результатов, при расширенном поиске свои лимиты @see $this->_advanced_page, $this->_advanced_limit.
     *
     * @param int $page номер текущей страницы (используется при поиске по конкретному элементу).
     */
    public function setPage($page)
    {
        if ($page > 0 && !$this->isAdvanced()) {
            $this->_limit *= $this->_layout == self::LAYOUT_BLOCK ? 3 : 1;
            $this->_offset = ($page - 1) * $this->_limit;
        }
    }

    /**
     * Устанавливает названия индексов, если они не заданы явно.
     *
     * @param int $page номер текущей страницы (используется при поиске по конкретному элементу).
     */
    public function setIndexes()
    {
        if (!$this->_indexes) {
            $this->_indexes[0] = $this->_indexPfx.$this->_engine->getElementKey($this).$this->_indexSfx;
            $this->_indexes[1] = 'delta_'.$this->_indexes[0];
        }
    }

    /**
     * Передает текущие параметры элемента поиска API SphinxClient.
     * Необходимо определить все переменные до вызова этой функции (например, в перегруженной версии).
     */
    public function setEngine()
    {
        $this->_engine->SetServer($this->_host, $this->_port);
        $this->_engine->SetLimits($this->_offset, $this->_limit, $this->_maxmatches, $this->_cutoff);
        $this->_engine->SetMaxQueryTime($this->_maxquerytime);
        $this->_engine->SetRankingMode($this->_ranker);
        $this->_engine->SetMatchMode($this->_mode);
        $this->_engine->SetFieldWeights($this->_fieldweights);
        $this->_engine->SetIndexWeights($this->_indexweights);
        $this->_engine->SetRetries($this->_retrycount, $this->_retrydelay);
        $this->_engine->SetArrayResult($this->_arrayresult);
        $this->_engine->ResetFilters();
        $this->_engine->ResetGroupBy();
        $this->_engine->ResetOverrides();
        $this->_engine->SetIDRange($this->_min_id, $this->_max_id);
        $this->_engine->SetSelect($this->_select);
        if ($this->_overrides) {
            call_user_func_array(array($this->_engine, 'SetOverride'), $this->_overrides);
        }
        if ($this->_anchor) {
            call_user_func_array(array($this->_engine, 'SetGeoAnchor'), $this->_anchor);
        }
        foreach ($this->_filtersV  as $f) {
            call_user_func_array(array($this->_engine, 'SetFilter'), $f);
        }
        foreach ($this->_filtersR  as $f) {
            call_user_func_array(array($this->_engine, 'SetFilterRange'), $f);
        }
        foreach ($this->_filtersRF as $f) {
            call_user_func_array(array($this->_engine, 'SetFilterFloatRange'), $f);
        }
        $this->_engine->SetGroupBy($this->_groupby, $this->_groupfunc, $this->_groupsort);
        $this->_engine->SetGroupDistinct($this->_groupdistinct);
        $this->_engine->SetSortMode($this->_sort, $this->_sortby);
    }

    /**
     * Вернуть обьект API Sphinx.
     * 
     * @return type
     */
    public function getEngine()
    {
        return $this->_engine;
    }

    /**
     * Сбрасывает результаты поиска.
     */
    public function resetResult()
    {
        $this->words = '';
        $this->total = 0;
        $this->totalStr = '';
        $this->matches = array();
        $this->html = array();
    }

    /**
     * Разбирает результаты поиска.
     *
     * @param array $result массив результатов, полученный вызовом SphinxClient::Query().
     */
    public function setResult($result)
    {
        if ($result && $result['total']) {
            //$this->words = str_replace('*', '', @implode(' ', @array_keys($result['words'])));
            $this->words = @implode(' ', @array_keys($result['words']));
            if ($result['matches']) {
                $this->matches = array_keys($result['matches']);
            }
            $this->total = $result['total'];
            $this->totalStr = ending((int) $result['total'], $this->totalwords[0], $this->totalwords[1], $this->totalwords[2]);
            // $this->setHtml(); // @todo надо везде убрать функцию, там идет лишний вызов self::getRecords();
            $this->setResults();
        }
    }

    /**
     * Можно ли производить поиск по данному элементу сейчас (например, включил ли его юзер в настройках).
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->_active;
    }

    /**
     * Проверяет, разрешен ли поиск по данному элементу при текущих параметрах окружения (например, проверка на авторизованность).
     *
     * @return bool
     */
    public function isAllowed()
    {
        return true;
    }

    /**
     * Возвращает значение переменной класса, определенной с префиксом '_'.
     * Может понадобиться для получения некоторых закрытых свойств.
     *
     * @return mixed
     */
    public function getProperty($name)
    {
        $pname = '_'.$name;
        if (isset($this->$pname)) {
            return $this->$pname;
        }

        return;
    }

    /**
     * Взять информацию по найденным результатам.
     *
     * @return array
     */
    public function getRecords($order_by = null)
    {
        if ($this->matches && $this->active_search) {
            if ($this->_indexes[0] == 'blogs') { // 0014900. После теста можно на всех VIEW попробовать.
                $set_sql = 'SET join_collapse_limit = 1;';
            }
            $sql = "{$set_sql}SELECT * FROM search_{$this->_indexes[0]} WHERE id IN (".implode(', ', $this->matches).')';
            if ($order_by) {
                $sql .= " ORDER BY {$order_by}";
            } elseif ($this->_sortby && (($desc = $this->_sort == SPH_SORT_ATTR_DESC) || $this->_sort == SPH_SORT_ATTR_ASC)) {
                $sql .= " ORDER BY {$this->_sortby}".($desc ? ' DESC' : '');
            }
            if ($res = pg_query(DBConnect(), $sql)) {
                return pg_fetch_all($res);
            }
        }

        return;
    }

    /**
     * Формирует документы в HTML-блоки и записывает их в $this->html.
     * Необходимо перегрузить в классе каждого элемента.
     */
    public function setHtml()
    {
    }

    public function setResults()
    {
        return true;
    }

    public function setAdvancedSearch($page = 0, $filter)
    {
        $this->_advanced = $filter;
        $this->_advanced_page = $page;
        $this->_advanced_limit = $this->_limit;
    }

    public function isAdvanced()
    {
        return $this->_advanced;
    }

    /**
     * Подсветка найденных совпадений в документе.
     *
     * @param array $data массив объектов поиска (например, заголовок проекта).
     *
     * @return array
     */
    public function mark($data)
    {
        return $this->_engine->BuildExcerpts($data, $this->_indexes[0], $this->words, $this->_opts);
    }
    public function setWords($words)
    {
        $this->words = $words;//str_replace('*', '', $words);
    }

    /**
     * Получает значение свойства из массива _opts по его ключу.
     * 
     * @param string $key
     *
     * @return string
     */
    public function getOpts($key)
    {
        if (!isset($this->_opts[$key])) {
            return;
        }

        return $this->_opts[$key];
    }

    /**
     * Устанавливает значение свойства в _opts.
     * 
     * @param string $key   Ключ
     * @param string $value Значение
     */
    public function setOpts($key, $value)
    {
        $this->_opts[$key] = $value;
    }

    /**
     * Установить режим поиска.
     * 
     * @param type $mode
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
    }
}
