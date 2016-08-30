<?php

/**
 * Ruskie rozwiązanie tagow w memcache @AŚ: 
 * 
 * Расширенный адаптер для мемкэша
 *
 * @see http://framework.zend.com/issues/browse/ZF-4253
 */
namespace Enp\Cache\Backend;

/**
 * @package Enp
 * @author  Nergal
 * @author  Artur Świerc
 */
class Memcache extends \Zend_Cache_Backend_Memcached
{
    /**
     * @var string
     */
	protected $_tag_prefix = 'EnpCacheTag_';

    /**
     * @var string
     */
    protected $_tag_list = 'EnpAllTags';

    /**
     * @var integer
     */
    protected $_delta = 30;

    /**
     * @see parent::$_directives
     * @var array
     */
    protected $_directives = array(
        'lifetime' => 3600,
        'logging'  => false,
        'logger'   => null,
        'delta'    => 300,
    );

    /**
     * Геттер для префикса ключей тегов
     *
     * @return string
     */
    private function getTagPrefix()
    {
        return $this->_tag_prefix;
    }

    /**
     * Сеттер для префикса ключей тегов
     *
     * @param string $prefix
     * @return void
     */
    private function setTagPrefix($prefix)
    {
        $this->_tag_prefix = $prefix;
    }

    /**
     * Установка записей конкретному тегу
     *
     * @param string $tag
     * @return array
     */
    public function getTagItems($tag)
    {
        $tag_list = $this->getTagPrefix() . $tag;
        if (!$tags = $this->_memcache->get($tag_list)) {
            $tags = array();
        }
        return $tags;
    }

    /**
     * Установка записей конкретному тегу
     *
     * @param string $tag
     * @param array $items
     * @return boolean
     */
    public function setTagItems($tag, $items = null)
    {
        $result = false;
        $tag_list = $this->getTagPrefix() . $tag;

        if (empty($items)) {
            $result = $this->_memcache->delete($tag_list, 0);
        } else {
            $result = $this->_memcache->set($tag_list, $items);
        }

        return $result;
    }

    /**
     * Сохранение тегов
     *
     * @param string $id
     * @param array $tags
     * @return void
     */
    private function saveTags($id, $tags = null)
    {
        $all_tags = (array) $this->_memcache->get($this->_tag_list);

        if ($tags === null) {
            if (in_array($id, $all_tags)) {
                unset($all_tags[$id]);
            }

            $this->setTagItems($id);
        } else {
            foreach ($tags as $tag) {
                $_tags = (array) $this->getTagItems($tag);

                if (!in_array($id, $_tags)) {
                    $_tags[] = $id;

                    $this->setTagItems($tag, $_tags);

                    if (!in_array($tag, $all_tags)) {
                        $all_tags[] = $tag;
                    }
                }
            }
        }

        $this->_memcache->set($this->_tag_list, $all_tags);
    }

    /**
     * Возврат записей по тегу
     *
     * @param string $tag
     * @return mixed
     */
    private function getItemsByTag($tag)
    {
        $tag_list = $this->getTagPrefix() . $tag;
        return $this->_memcache->get($tag_list);
    }

    /**
     * Выгрузка записи из кэша
     *
     * Схема данных:
     *     - data
     *     - ctime
     *     - lifetime
     * @param  string  $id
     * @param  boolean $doNotTestCacheValidity
     * @return string|false
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $item = $this->_memcache->get($id);
        if (is_array($item) AND count($item) == 3) {
            $is_expired = ($item[1] + $item[2]) < time();
            $is_infinite = $item[2] > 0;

            if ($is_expired === true AND $is_infinite === true) {
                $this->_memcache->delete($id, 0);
                return false;
            }
            return $item[0];
        }

        return false;
    }

    /**
     * Проверка существования записи
     *
     * @param string $id
     * @return mixed|false
     */
    public function test($id)
    {
        $tmp = $this->load($id);
        return $tmp !== false;
    }

    /**
     * Выборка дельты для времени жизни кэша
     *
     * @return int
     */
    public function getLifetimeDelta()
    {
        return ($this->_delta > 0)
              ? $this->_delta
              : $this->_directives['delta'];
    }

    /**
     * Сохранение данных в кэш
     *
     * @see parent::save()
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param int $specificLifetime
     * @return boolean
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $lifetime = $this->getLifetime($specificLifetime);
        $flag = ($this->_options['compression']) ? MEMCACHE_COMPRESSED : 0;

        if ($lifetime > 0 AND $delta = $this->getLifetimeDelta()) {
            $lifetime+= rand(0, $delta);
        }

        $data = array($data, time(), $lifetime);

        $result = $this->_memcache->set($id, $data, $flag, 0);

        if (count($tags) > 0) {
            $this->saveTags($id, $tags);
        }

        return $result;
    }

    /**
     * Очистить кэш
     *
     * @see parent::clean()
     * @param string $mode
     * @param array $tags
     * @return boolean
     */
    public function clean($mode = \Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        if ($mode == \Zend_Cache::CLEANING_MODE_ALL) {
            return $this->_memcache->flush();
        }

        if ($mode == \Zend_Cache::CLEANING_MODE_MATCHING_TAG) {
            foreach($tags as $tag) {
                $_tags = (array) $this->getItemsByTag($tag);

                foreach($_tags as $item) {
                    $this->_memcache->delete($item, 0);
                }

                $this->saveTags($tag);
            }
        }
    }

    /**
     * Возвращает список сохранённых id
     *
     * @return array
     */
    public function getIds()
    {
        $list = array();
        $allSlabs = $this->_memcache->getExtendedStats('slabs');
        $items = $this->_memcache->getExtendedStats('items');

        foreach($allSlabs as $server => $slabs) {
            foreach($slabs AS $slabId => $slabMeta) {
                if (is_numeric($slabId)) {
                    $cdump = $this->_memcache->getExtendedStats('cachedump', (int) $slabId);
                    foreach($cdump AS $server => $entries) {
                        if($entries) {
                            foreach($entries AS $eName => $eData) {
                                $list[] = $eName;
                            }
                        }
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Возвращает заполненность кэша в процентах
     *
     * @throws \Zend_Cache_Exception
     * @return float
     */
    public function getFillingPercentage()
    {
        $mems = $this->_memcache->getExtendedStats();

        $memSize = null;
        $memUsed = null;
        foreach ($mems as $key => $mem) {
            if ($mem === false) {
                $this->_log("can't get stat from {$key}");
                continue;
            }

            $eachSize = $mem['limit_maxbytes'];
            $eachUsed = $mem['bytes'];
            if ($eachUsed > $eachSize) {
                $eachUsed = $eachSize;
            }

            $memSize += $eachSize;
            $memUsed += $eachUsed;
        }

        if ($memSize === null || $memUsed === null) {
            \Zend_Cache::throwException("Can't get filling percentage");
        }

        return (round(100. * ($memUsed / $memSize), 2));
    }

    /**
     * Лок записи в кэше
     *
     * @param string $key
     * @return boolean
     */
    public function lock($key)
    {
        return $this->_memcache->add("lock.{$key}", 1);
    }

    /**
     * Анлок записи
     *
     * @param string $key
     * @return boolean
     */
    public function unlock($key)
    {
        return $this->_memcache->delete($key, 0);
    }


    /**
     * Возвращает параметры совместимости бэкенда
     *
     * @see parent::getCapabilities()
     * @return array
     */
    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => false,
            'tags'               => true,
            'expired_read'       => false,
            'priority'           => true,
            'infinite_lifetime'  => false,
            'get_list'           => false,
        );
    }
}
