<?php

namespace Lib\Cache;

use Lib\System\Log;
use Config\ConfigLog;

/**
 * Author: skylong
 * CreateTime: 2018-6-13 23:22:18
 * Description: 基于php redis扩展的操作管理类
 */
class SRedis {

    /**
     * 访问redis地址
     *
     * @var string
     */
    private $host = '';

    /**
     * 访问redis端口号
     *
     * @var int
     */
    private $port = 6379;

    /**
     * 访问redis需要的密码
     *
     * @var string
     */
    private $passwd = '';

    /**
     * redis是否连接成功
     *
     * @var boolean
     */
    private $is_connect = false;

    /**
     * redis请求超时秒数
     */
    const TIME_OUT = 3;

    /**
     * redis实例
     *
     * @var \Redis 
     */
    private $redis = null;

    /**
     * 参数初始化
     * 
     * @param string $host
     * @param int $port
     * @param string $passwd
     */
    public function __construct($host, $port = 6379, $passwd = '') {
        $this->host = $host;
        $this->port = $port;
        $this->passwd = $passwd;
    }

    /**
     * 创建redis连接
     * 
     * @return boolean
     */
    private function connect() {
        if ($this->is_connect) {
            return $this->is_connect;
        }
        try {
            $this->redis = new \Redis();
            $this->is_connect = $this->redis->connect($this->host, $this->port, self::TIME_OUT);
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE); //Redis::SERIALIZER_NONE不序列化.Redis::SERIALIZER_IGBINARY二进制序列化
            $this->passwd && $this->redis->auth($this->passwd);
            defined('PROJECT_NS') && $this->redis->setOption(\Redis::OPT_PREFIX, strtoupper(PROJECT_NS . '_')); //设置key的前缀
        } catch (\RedisException $e) {
            $arr = (array) $e->getTrace();
            $trace = (array) array_pop($arr);
            $err_file = (string) $trace['file'] . '(' . (string) $trace['line'] . ')';
            $this->writeErrLog($err_file, $e->getCode(), $e->getMessage());
        }
        return $this->is_connect;
    }

    /**
     * 字符串(String)数据类型  获取值
     * 
     * @param string $key
     * @return string|boolean
     */
    public function get($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->get($key);
    }

    /**
     * 字符串(String)数据类型  设置值
     * 
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function set($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->set($key, $value);
    }

    /**
     * 字符串(String)数据类型 设置一个有生命周期的值
     * 
     * @param string $key
     * @param string $value
     * @param int $expire  失效时间秒数
     * @return boolean
     */
    public function setex($key, $value, $expire = 86400) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->setex($key, (int) $expire, $value);
    }

    /**
     * 字符串(String)数据类型  设置值
     * 这个函数会先判断Redis中是否有这个KEY，如果没有就SET，有就返回False
     * 
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function setnx($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->setnx($key, $value);
    }

    /**
     * 字符串(String)数据类型  添加字符串到指定KEY的字符串中
     * 
     * @param string $key
     * @param string $value
     * @return boolean|int 成功返回追加后的字符串长度
     */
    public function append($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->append($key, $value);
    }

    /**
     * 字符串(String)数据类型  返回字符串的一部分
     * 
     * @param string $key
     * @param int $start  起始下标，如果是负数，查找位置从字符串结尾开始
     * @param int $end  结束下标，如果是负数，查找位置从字符串结尾开始
     * @return boolean|string
     */
    public function getRange($key, $start, $end) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->getRange($key, $start, $end);
    }

    /**
     * 字符串(String)数据类型 修改字符串的一部分
     * 
     * @param string $key
     * @param int $offset  起始下标
     * @param string $sub_str 指定下标开始替换的字符串
     * @return boolean|int 成功返回修改后的字符串长度
     */
    public function setRange($key, $offset, $sub_str) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->setRange($key, $offset, $sub_str);
    }

    /**
     * 字符串(String)数据类型 获取字符串的长度
     * 
     * @param type $key
     * @return boolean|int
     */
    public function strlen($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->strlen($key);
    }

    /**
     * 字符串(String)数据类型 对指定KEY的值自增
     * 
     * @param string $key
     * @param int|float $num
     * @return boolean|int|float  返回自增后的值
     */
    public function incr($key, $num = 1) {
        if (!$this->connect()) {
            return false;
        }
        if (is_int($num) && $num > 1) {
            return $this->redis->incrBy($key, $num);
        }
        if (is_float($num)) {
            return $this->redis->incrByFloat($key, $num);
        }
        return $this->redis->incr($key);
    }

    /**
     * 字符串(String)数据类型 对指定KEY的值自减，仅整数有效
     * 
     * @param string $key
     * @param int $num
     * @return boolean|int  返回自减后的值
     */
    public function decr($key, $num = 1) {
        if (!$this->connect()) {
            return false;
        }
        if (is_int($num) && $num > 1) {
            return $this->redis->decrBy($key, $num);
        }
        return $this->redis->decr($key);
    }

    /**
     * 列表(List)数据类型 添加一个字符串值到LIST容器的顶部（左侧），如果KEY不存在，曾创建一个LIST容器，如果KEY存在并且不是一个LIST容器，那么返回FLASE。
     * 
     * @param string $key
     * @param string $value
     * @return boolean|int 成功返回添加后的元素个数
     */
    public function lPush($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lPush($key, $value);
    }

    /**
     * 列表(List)数据类型 返回LIST顶部（左侧）的VALUE，并且从LIST中把该VALUE弹出。
     * 
     * @param string $key
     * @return boolean|string
     */
    public function lPop($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lPop($key);
    }

    /**
     * 列表类(List)数据类型 添加一个字符串值到LIST容器的底部（右侧），如果KEY不存在，曾创建一个LIST容器，如果KEY存在并且不是一个LIST容器，那么返回FLASE。 
     * 
     * @param string $key
     * @param string $value
     * @return boolean|int 成功返回添加后的元素个数
     */
    public function rPush($key, $value) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->rPush($key, $value);
    }

    /**
     * 列表(List)数据类型 返回LIST底部（右侧）的VALUE，并且从LIST中把该VALUE弹出。
     * 
     * @param string $key
     * @return boolean|string
     */
    public function rPop($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->rPop($key);
    }

    /**
     * 列表(List)数据类型 根据KEY返回LIST的长度，如果这个LIST不存在或者为空，那么ISIZE返回0，如果指定的KEY的数据类型不是LIST或者不为空，那么返回FALSE.
     * 
     * @param string $key
     * @return boolean|int
     */
    public function lSize($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lSize($key);
    }

    /**
     * 列表(List)数据类型 根据索引值返回指定KEY LIST中的元素
     * 
     * @param string $key
     * @param int $index
     * @return boolean|string
     */
    public function lGet($key, $index) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lGet($key, $index);
    }

    /**
     * 列表(List)数据类型 根据索引值设置新的VAULE
     * 
     * @param string $key
     * @param int $index
     * @param string $val
     * @return boolean
     */
    public function lSet($key, $index, $val) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lSet($key, $index, $val);
    }

    /**
     * 列表(List)数据类型 取得指定索引值范围内的所有元素(不包含起始索引元素)
     * 
     * @param string $key
     * @param int $offset
     * @param int $end
     * @return boolean|array
     */
    public function lRange($key, $offset, $end) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lrange($key, $offset, $end);
    }

    /**
     * 列表(List)数据类型 取得指定索引值范围内的所有元素(包含起始索引元素)，并清空指定索引值范围以外的元素
     * 
     * @param string $key
     * @param int $offset
     * @param int $end
     * @return boolean|array
     */
    public function lTrim($key, $offset, $end) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->ltrim($key, $offset, $end);
    }

    /**
     * 列表(List)数据类型 移除指定元素
     * 
     * @param string $key
     * @param string $val  需要移除的元素
     * @param int $count  移除个数，count等于0：所有符合删除条件的元素，count大于0时：将从左至右删除count个符合条件的元素，count小于0时：从右至左删除count个符合条件的元素
     * @return boolean|int  成功返回移除的个数
     */
    public function lRem($key, $val, $count = 0) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lrem($key, $val, $count);
    }

    /**
     * 列表(List)数据类型 指定元素的左侧或者右侧插入值。如果这个LIST不存在，或者查找的字符串不存在，那么这个值不会被插入
     * 
     * @param string $key
     * @param int $insert_mode 插入模式，Redis::BEFORE（在查找的元素前插入），Redis::AFTER（在查找的元素后插入）
     * @param string $search 查找的字符串
     * @param string $val  需要插入的字符串
     * @return boolean|int  成功返回元素个数，小于0表示key不存在或者没有匹配的元素
     */
    public function lInsert($key, $insert_mode, $search, $val) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->lInsert($key, $insert_mode, $search, $val);
    }

    /**
     * 列表(List)数据类型 从源LIST的最后弹出一个元素，并且把这个元素从目标LIST的顶部（左侧）压入目标LIST
     * 
     * @param string $srckey 源list
     * @param string $dstkey 目标list
     * @return boolean|string 成功返回弹出的元素
     */
    public function rpoplpush($srckey, $dstkey) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->rpoplpush($srckey, $dstkey);
    }

    /**
     * 集合(Set)数据类型 添加一个元素到集合中，如果这个元素存在于集合中，那么返回FLASE。
     * 
     * @param string $key
     * @param string $member
     * @return boolean
     */
    public function sAdd($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sAdd($key, $member);
    }

    /**
     * 集合(Set)数据类型 从集合中移除一个元素
     * 
     * @param string $key
     * @param string $member
     * @return boolean
     */
    public function sRemove($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sRemove($key, $member);
    }

    /**
     * 集合(Set)数据类型  移动一个指定的元素从源集合到指定的另一个集合中
     * 
     * @param string $srcKey 源集合
     * @param string $dstKey 目标集合
     * @param string $member 元素
     * @return boolean
     */
    public function sMove($srcKey, $dstKey, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sMove($srcKey, $dstKey, $member);
    }

    /**
     * 集合(Set)数据类型 检查元素是否是集合中的成员
     * 
     * @param string $key
     * @param string $member
     * @return boolean
     */
    public function sIsMember($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sismember($key, $member);
    }

    /**
     * 集合(Set)数据类型 当前集合的成员数
     * 
     * @param string $key
     * @return boolean|int
     */
    public function sSize($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sSize($key);
    }

    /**
     * 集合(Set)数据类型 随机返回一个元素，并且在集合中移除该元素
     * 
     * @param string $key
     * @return boolean|string
     */
    public function sPop($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sPop($key);
    }

    /**
     * 集合(Set)数据类型 取得指定集合中的一个随机元素，但不会在集合中移除它
     * 
     * @param string $key
     * @return boolean|string
     */
    public function sRandMember($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sRandMember($key);
    }

    /**
     * 集合(Set)数据类型 取得所有集合的交集,如果所涉及到的集合没有交集，那么将返回一个空数组
     * 
     * @param string ...$keys
     * @return boolean|array
     */
    public function sInter(...$keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sInter(...$keys);
    }

    /**
     * 集合(Set)数据类型 执行一个交集操作，并把结果存储到一个新的集合中
     * 
     * @param string ...$keys  不定参数的第一个参数为目标集合key
     * @return boolean|int 成功返回交集个数
     */
    public function sInterStore(...$keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sInterStore(...$keys);
    }

    /**
     * 集合(Set)数据类型 取得所有集合的并集，已经去重
     * 
     * @param string ...$keys
     * @return boolean|array
     */
    public function sUnion(...$keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sUnion(...$keys);
    }

    /**
     * 集合(Set)数据类型 执行一个并集操作，已经去重，并把结果存储到一个新的集合中
     * 
     * @param string ...$keys  不定参数的第一个参数为目标集合key
     * @return boolean|int 成功返回并集个数
     */
    public function sUnionStore(...$keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sUnionStore(...$keys);
    }

    /**
     * 集合(Set)数据类型 返回的是第一个集合相对于其他集合的差集
     * 
     * @param string ...$keys
     * @return boolean|array
     */
    public function sDiff(...$keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sDiff(...$keys);
    }

    /**
     * 集合(Set)数据类型 执行一个差集操作，已经去重，并把结果存储到一个新的集合中
     * 
     * @param string ...$keys  不定参数的第一个参数为目标集合key
     * @return boolean|int 成功返回差集个数
     */
    public function sDiffStore(...$keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sDiffStore(...$keys);
    }

    /**
     * 集合(Set)数据类型  当前集合的所有元素
     * 
     * @param string $key
     * @return boolean|array
     */
    public function sMembers($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->sMembers($key);
    }

    /**
     * 有序集合(zSet)数据类型 增加元素到有序集合，如果该元素已经存在，更新它的socre值
     * 虽然有序集合有序，但它也是集合，不能重复元素，添加重复元素只会更新原有元素的score值
     * 
     * @param string $key
     * @param string $member
     * @param double $score
     * @return boolean|array
     */
    public function zAdd($key, $member, $score = 0) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zAdd($key, $score, $member);
    }

    /**
     * 有序集合(zSet)数据类型 取得特定范围内的排序元素
     * 
     * @param string $key
     * @param int $start 开始索引
     * @param int $end 0代表第一个元素,1代表第二个以此类推。-1代表最后一个,-2代表倒数第二个...
     * @param boolean $withscores  是否输出格式为：array(member=>score,...)
     * @return boolean|array
     */
    public function zRange($key, $start, $end, $withscores = false) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRange($key, $start, $end, $withscores);
    }

    /**
     * 有序集合(zSet)数据类型 从有序集合中删除指定的成员
     * 
     * @param string $key
     * @param string $member
     * @return boolean
     */
    public function zDelete($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zDelete($key, $member);
    }

    /**
     * 有序集合(zSet)数据类型 取得特定范围内的所有元素，这些元素按照score从高到低的顺序进行排列。
     * 对于具有相同的score的元素而言，将会按照递减的字典顺序进行排列
     * 
     * @param string $key
     * @param int $start 开始索引
     * @param int $end 0代表第一个元素,1代表第二个以此类推。-1代表最后一个,-2代表倒数第二个...
     * @param boolean $withscores  是否输出格式为：array(member=>score,...)
     * @return boolean|array
     */
    public function zRevRange($key, $start, $end, $withscores = false) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRevRange($key, $start, $end, $withscores);
    }

    /**
     * 有序集合(zSet)数据类型 取得有序集合中score介于min和max之间的所有元素（包哈score等于min或者max的元素）。
     * 元素按照score从低到高的顺序排列。如果元素具有相同的score，那么会按照字典顺序排列。
     * 可选的选项LIMIT可以用来获取一定范围内的匹配元素，格式：limit=>array(start,end) 。
     * 如果偏移值较大，有序集合需要在获得将要返回的元素之前进行遍历，因此会增加O(N)的时间复杂度。
     * 可选的选项WITHSCORES可以使得在返回元素的同时返回元素的score，该选项自从Redis 2.0版本后可用。
     * 
     * @param string $key
     * @param string $min_score 最小分数
     * @param string $max_score  最大分数
     * @param array $options 格式：array('withscores' => TRUE, 'limit' => array(1, 1)); 
     * @return boolean|array
     */
    public function zRangeByScore($key, $min_score, $max_score, $options = array()) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRangeByScore($key, $min_score, $max_score, $options);
    }

    /**
     * 有序集合(zSet)数据类型 取得有序集合中介于min和max间的元素的个数
     * 
     * @param string $key
     * @param string $min_score
     * @param string $max_score
     * @return boolean|int
     */
    public function zCount($key, $min_score, $max_score) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zCount($key, $min_score, $max_score);
    }

    /**
     * 有序集合(zSet)数据类型 移除有序集合中scroe位于min和max（包含端点）之间的所有元素
     * 
     * @param string $key
     * @param string $min_score
     * @param string $max_score
     * @return boolean|int 成功返回移除的个数
     */
    public function zRemRangeByScore($key, $min_score, $max_score) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRemRangeByScore($key, $min_score, $max_score);
    }

    /**
     * 有序集合(zSet)数据类型 移除有序集合中rank值介于start和end之间的所有元素。
     * start和end均是从0开始的，并且两者均可以是负值。
     * 当索引值为负值时，表明偏移值从有序集合中score值最高的元素开始。
     * 例如：-1表示具有最高score的元素，而-2表示具有次高score的元素，以此类推。
     * 
     * @param string $key
     * @param int $start
     * @param int $end
     * @return boolean|int 成功返回移除的个数
     */
    public function zRemRangeByRank($key, $start, $end) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRemRangeByRank($key, $start, $end);
    }

    /**
     * 有序集合(zSet)数据类型 当前有序集合中的元素的个数
     * 
     * @param string $key
     * @return boolean|int
     */
    public function zSize($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zSize($key);
    }

    /**
     * 有序集合(zSet)数据类型 获取有序集合中member的score值，如果member在有序集合中不存在，那么将会返回false。
     * 
     * @param string $key
     * @param string $member
     * @return boolean|double
     */
    public function zScore($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zScore($key, $member);
    }

    /**
     * 有序集合(zSet)数据类型 获取有序集合中member元素的索引值，元素按照score从低到高进行排列。
     * rank值（或index）是从0开始的，这意味着具有最低score值的元素的rank值为0,不存在返回false。
     * 
     * @param string $key
     * @param string $member
     * @return boolean|int
     */
    public function zRank($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRank($key, $member);
    }

    /**
     * 有序集合(zSet)数据类型 获取有序集合中member元素的索引值，元素按照score从高到低进行排列
     * rank值（或index）是从0开始的，这意味着具有最高score值的元素的rank值为0。
     * 
     * @param string $key
     * @param string $member
     * @return boolean|int
     */
    public function zRevRank($key, $member) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zRevRank($key, $member);
    }

    /**
     * 有序集合(zSet)数据类型 有序集合中member元素的scroe加上increment。
     * 如果指定的member不存在，那么将会添加该元素，并且其score的初始值为increment。
     * 如果key不存在，那么将会创建一个新的有序列表，其中包含member这一唯一的元素。
     * 如果key对应的值不是有序列表，那么将会发生错误。
     * 同时，你也可用提供一个负值，这样将减少score的值。
     * 
     * @param string $key
     * @param string $member
     * @param double $incr_score
     * @return boolean|double
     */
    public function zIncrBy($key, $member, $incr_score) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zIncrBy($key, $incr_score, $member);
    }

    /**
     * 有序集合(zSet)数据类型 计算合集，并将结果存储在目标集合中。
     * 在默认情况下，元素的结果score是包含该元素的所有有序集合中score的和。
     * 如果使用WEIGHTS选项，你可以对每一个有序集合指定一个操作因子，这意味着每一个有序集合中的每个元素的score在传递给聚合函数之前均会被乘以该因子。
     * 当WEIGHTS没有指定时，操作因子默认为1，使用AGGREGATE选项，你可以指定交集中的结果如何被聚合。该选项默认值为SUM，在这种情况下，一个元素的所有score值均会被相加。
     * 当选项被设置为MIN或MAX时，结果集合中将会包含一个元素的最大或者最小的score值。
     * 如果destination已经存在，那么它将会被重写。
     * 
     * @param string $dstkey
     * @param array $arr_keys 必选参数，格式：array('key1','key2')
     * @param array $arr_weights 必选参数，格式：array(1,1)
     * @param array $func "sum", "min", or "max"
     * @return boolean|int 成功返回合并后元素的个数
     */
    public function zUnion($dstkey, $arr_keys = array(), $arr_weights = array(), $func = 'sum') {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zUnion($dstkey, $arr_keys, $arr_weights, $func);
    }

    /**
     * 有序集合(zSet)数据类型 计算交集，并将结果存储在目标集合中。
     * 在默认情况下，元素的结果score是包含该元素的所有有序集合中score的和。
     * 如果使用WEIGHTS选项，你可以对每一个有序集合指定一个操作因子，这意味着每一个有序集合中的每个元素的score在传递给聚合函数之前均会被乘以该因子。
     * 当WEIGHTS没有指定时，操作因子默认为1，使用AGGREGATE选项，你可以指定交集中的结果如何被聚合。该选项默认值为SUM，在这种情况下，一个元素的所有score值均会被相加。
     * 当选项被设置为MIN或MAX时，结果集合中将会包含一个元素的最大或者最小的score值。
     * 如果destination已经存在，那么它将会被重写。
     * 
     * @param string $dstkey
     * @param array $arr_keys
     * @param array $arr_weights
     * @param array $func "sum", "min", or "max"
     * @return boolean|int 成功返回合并后元素的个数
     */
    public function zInter($dstkey, $arr_keys = array(), $arr_weights = array(), $func = 'sum') {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->zInter($dstkey, $arr_keys, $arr_weights, $func);
    }

    /**
     * 哈希(Hash)数据类型 添加一条数据到哈希表中
     * 
     * @param string $hash_key 哈希表key
     * @param string $key
     * @param string $val  
     * @return boolean|int  1-成功添加，0-已经存在并且被替换，false-添加失败
     */
    public function hSet($hash_key, $key, $val) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hSet($hash_key, $key, $val);
    }

    /**
     * 哈希(Hash)数据类型 仅当数据未在哈希中时，将值添加到键中
     * 
     * @param string $hash_key 哈希表key
     * @param string $key
     * @param string $val  
     * @return boolean
     */
    public function hSetNx($hash_key, $key, $val) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hSetNx($hash_key, $key, $val);
    }

    /**
     * 哈希(Hash)数据类型 从hash表中获取一条数据
     * 
     * @param string $hash_key
     * @param string $key
     * @return boolean|string
     */
    public function hGet($hash_key, $key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hGet($hash_key, $key);
    }

    /**
     * 哈希(Hash)数据类型 从hash表中读取所有数据
     * 
     * @param string $hash_key
     * @return boolean|array
     */
    public function hGetAll($hash_key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hGetAll($hash_key);
    }

    /**
     * 哈希(Hash)数据类型 当前hash表的长度
     * 
     * @param string $hash_key
     * @return boolean|int
     */
    public function hLen($hash_key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hLen($hash_key);
    }

    /**
     * 哈希(Hash)数据类型 从hash表中删除一条数据
     * 
     * @param string $hash_key
     * @param string $key
     * @return boolean
     */
    public function hDel($hash_key, $key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hDel($hash_key, $key);
    }

    /**
     * 哈希(Hash)数据类型 取得hash表中的所有key
     * 
     * @param string $hash_key
     * @return boolean|array
     */
    public function hKeys($hash_key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hKeys($hash_key);
    }

    /**
     * 哈希(Hash)数据类型 取得hash表中的所有值
     * 
     * @param string $hash_key
     * @return boolean|array
     */
    public function hVals($hash_key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hVals($hash_key);
    }

    /**
     * 哈希(Hash)数据类型 验证hash表中是否存在指定的key->val
     * 
     * @param string $hash_key
     * @param string $key
     * @return boolean
     */
    public function hExists($hash_key, $key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hExists($hash_key, $key);
    }

    /**
     * 哈希(Hash)数据类型 根据hash表中的KEY，自增值
     * 
     * @param string $hash_key
     * @param string $key
     * @param int|float $incr_num
     * @return boolean|int|float 成功返回自增后的值
     */
    public function hIncrBy($hash_key, $key, $incr_num = 1) {
        if (!$this->connect()) {
            return false;
        }
        if (is_int($incr_num)) {
            return $this->redis->hIncrBy($hash_key, $key, $incr_num);
        }
        return $this->redis->hIncrByFloat($hash_key, $key, $incr_num);
    }

    /**
     * 哈希(Hash)数据类型 批量填充hash表,不是字符串类型的VALUE，自动转换成字符串类型
     * NULL值将被储存为一个空的字符串
     * 
     * @param string $hash_key
     * @param array $hash_data 格式：array(key1=>val1,key2=>val2,...)
     * @return boolean
     */
    public function hMset($hash_key, $hash_data = array()) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hMset($hash_key, $hash_data);
    }

    /**
     * 哈希(Hash)数据类型 批量获取hash表中的值
     * 
     * @param string $hash_key
     * @param array $keys 格式：array(key1,key2,...)
     * @return boolean
     */
    public function hMget($hash_key, $keys = array()) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->hMget($hash_key, $keys);
    }

    /**
     * 删除key
     * 
     * @param string|array $keys
     * @return int 成功删除的个数
     */
    public function delete($keys) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->delete($keys);
    }

    /**
     * 验证一个指定的KEY是否存在
     * 
     * @param type $key
     * @return boolean
     */
    public function exists($key) {
        if (!$this->connect()) {
            return false;
        }
        return $this->redis->exists($key);
    }

    /**
     * 写操作redis失败的日志
     * 
     * @param string $err_file  发生错误文件
     * @param int $errno  错误编号
     * @param int $error  错误信息
     */
    private function writeErrLog($err_file, $errno, $error) {
        !PRODUCTION_ENV && die($err_file . '=======Redis Err：' . $error);
        $data = "file:{$err_file}\r\n";
        $data .= "time:" . date('Y-m-d H:i:s') . "\r\n";
        $data .= "errno:{$errno}\r\n";
        $data .= "error:\"{$error}\"\r\n";
        $data .= "======================================================================\r\n";
        Log::writeErrLog('error_redis' . date('Ymd'), $data, ConfigLog::REDIS_ERR_LOG_TYPE);
    }

}
