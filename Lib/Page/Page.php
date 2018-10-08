<?php

namespace Lib\Page;

defined('IN_APP') or die('Access denied!');

/**
 * Author: skylong
 * CreateTime: 2018-7-22 13:57:17
 * Description: 数据分页类
 */
class Page {

    /**
     * 数据表中总记录数
     *
     * @var int 
     */
    private $total;

    /**
     * 每页显示行数
     *
     * @var int 
     */
    private $list_rows;

    /**
     * SQL语句使用limit从句，限制获取记录个数 
     *
     * @var int
     */
    private $limit;

    /**
     * 自动获取uri的请求地址
     *
     * @var string
     */
    private $url;

    /**
     * 总页数
     *
     * @var int
     */
    private $page_num;

    /**
     * 当前页
     *
     * @var int 
     */
    private $page;

    /**
     * 在分页信息中显示内容，可以自己通过set方法设置
     *
     * @var array
     */
    private $config = array(
        'head'  => '条记录',
        'prev'  => '上一页',
        'next'  => '下一页',
        'first' => '首页',
        'last'  => '末页',
    );

    /**
     * 分页码显示个数
     *
     * @var int
     */
    private $list_num;

    /**
     * 分页类初始化
     * 
     * @param int $total  总记录数
     * @param int $list_rows 每页显示记录数
     * @param int $list_num  分页码显示个数
     * @param mixed $query  查询参数，可以是字符串，也可以是数组
     */
    public function __construct($total, $list_rows = 25, $list_num = 10, $query = '') {
        $this->total     = $total;
        $this->list_rows = $list_rows;
        $this->list_num  = $list_num;
        $this->url       = $this->getUrl($query);
        $this->page_num  = ceil($this->total / $this->list_rows);
        $page            = $_GET['page'] ? $_GET['page'] : 1;
        if ($total > 0) {
            if (preg_match('/\D/', $page)) {
                $this->page = 1;
            } else {
                $this->page = $page;
            }
        } else {
            $this->page = 0;
        }
        $this->limit = 'LIMIT ' . $this->setLimit();
    }

    /**
     * 设置分页中显示的内容
     * 
     * @param string $param
     * @param string $value
     * @return $this
     */
    public function set($param, $value) {
        if (array_key_exists($param, $this->config)) {
            $this->config[$param] = $value;
        }
        return $this;
    }

    /**
     * 外部获取私有属性
     * 
     * @param string $args
     * @return string|null
     */
    public function __get($args) {
        if ($args == 'limit' || $args == 'page') {
            return $this->$args;
        }
        return null;
    }

    /**
     * 按指定格式输出分页
     * 
     * @return string
     */
    public function fpage() {
        $arr     = func_get_args();
        $html[0] = "&nbsp;共<b> {$this->total} </b>{$this->config['head']}&nbsp;";
        $html[1] = "&nbsp;本页 <b>{$this->disnum()}</b> 条&nbsp;";
        $html[2] = "&nbsp;本页从 <b>{$this->start()}-{$this->end()}</b> 条&nbsp;";
        $html[3] = "&nbsp;<b> {$this->page}/{$this->page_num}</b>页&nbsp;";
        $html[4] = $this->firstprev();
        $html[5] = $this->pageList();
        $html[6] = $this->nextlast();
        $html[7] = $this->goPage();
        $fpage   = '<div style="font:12px">';
        (count($arr) < 1) && $arr     = array(0, 1, 2, 3, 4, 5, 6, 7);
        for ($i = 0; $i < count($arr); $i++) {
            $fpage .= $html[$arr[$i]];
        }
        $fpage .= '</div>';
        return $fpage;
    }

    /**
     * 设置limit取值范围
     * 
     * @return int
     */
    private function setLimit() {
        if ($this->page > 0) {
            return ($this->page - 1) * $this->list_rows . ", {$this->list_rows}";
        } else {
            return 0;
        }
    }

    /**
     * 获取当前url地址
     * 
     * @param mixed $query
     * @return string
     */
    private function getUrl($query) {
        $request_uri = $_SERVER['REQUEST_URI'];
        $url         = strstr($request_uri, '?') ? $request_uri : $request_uri . '?';
        if ($query && is_array($query)) {
            $url .= http_build_query($query);
        } elseif ($query) {
            $url .= '&' . trim($query, '?&');
        }

        $url_arr = parse_url($url);
        if (isset($url_arr['query'])) {
            $url_params = array();
            parse_str($url_arr['query'], $url_params);
            unset($url_params['page']);
            $url        = $url_arr['path'] . '?' . http_build_query($url_params);
        }

        if (substr($url, -1) != '?') {
            $url = $url . '&';
        }
        return $url;
    }

    /**
     * 本页开始记录
     * 
     * @return int
     */
    private function start() {
        if ($this->total == 0) {
            return 0;
        }
        return ($this->page - 1) * $this->list_rows + 1;
    }

    /**
     * 本页结束记录
     * 
     * @return int
     */
    private function end() {
        return min($this->page * $this->list_rows, $this->total);
    }

    /**
     * 首页 上一页
     * 
     * @return string
     */
    private function firstprev() {
        if ($this->page > 1) {
            $str = "&nbsp;<a href='{$this->url}page=1'>{$this->config['first']}</a>&nbsp;";
            $str .= "&nbsp;<a href='{$this->url}page=" . ($this->page - 1) . "'>{$this->config['prev']}</a>&nbsp;";
            return $str;
        }
    }

    /**
     * 页码列表
     * 
     * @return string
     */
    private function pageList() {
        $linkPage = "&nbsp;<b>";
        $inum     = $this->page > floor($this->list_num / 2) ? floor($this->list_num / 2) : $this->list_num;
        if ($this->page > ($this->page_num - floor($this->list_num / 2))) {
            $page = $this->page_num - $this->list_num;
            for ($i = 1; $i <= $this->list_num; $i++) {
                $page++;
                if ($page == $this->page) {
                    $linkPage .= "<span style='padding:1px 8px;background:#BBB;color:white;border:1px solid #CCCCCC;'>{$this->page}</span>&nbsp";
                } else {
                    $linkPage .= "<a style='padding:1px 8px; border:1px solid #CCCCCC; text-decoration:none;' href='{$this->url}page={$page}'>{$page}</a>&nbsp;";
                }
            }
            $linkPage .= '</b>';
            return $linkPage;
        }
        for ($i = $inum; $i >= 1; $i--) {
            $page = $this->page - $i;
            if ($page >= 1) {
                $linkPage .= "<a style='padding:1px 8px; border:1px solid #CCCCCC; text-decoration:none;' href='{$this->url}page={$page}'>{$page}</a>&nbsp;";
            }
        }
        if ($this->page_num > 1) {
            $linkPage .= "<span style='padding:1px 8px;background:#BBB;color:white;border:1px solid #CCCCCC;'>{$this->page}</span>&nbsp";
        }
        $offset = $this->page > floor($this->list_num / 2) ? $inum : $inum - $this->page;
        for ($i = 1; $i <= $offset; $i++) {
            $page = $this->page + $i;
            if ($page <= $this->page_num) {
                $linkPage .= "<a style='padding:1px 8px; border:1px solid #CCCCCC; text-decoration:none;' href='{$this->url}page={$page}'>{$page}</a>&nbsp;";
            } else {
                break;
            }
        }
        $linkPage .= '</b>';
        return $linkPage;
    }

    /**
     * 下一页，最后一页
     * 
     * @return string
     */
    private function nextlast() {
        if ($this->page != $this->page_num) {
            $str = "&nbsp;<a href='{$this->url}page=" . ($this->page + 1) . "'>{$this->config['next']}</a>&nbsp;";
            $str .= "&nbsp;<a href='{$this->url}page={$this->page_num}'>{$this->config['last']}</a>&nbsp;";
            return $str;
        }
    }

    /**
     * 跳转页码
     * 
     * @return string
     */
    private function goPage() {
        if ($this->page_num <= 1) {
            return '';
        }
        $to_page_str = '&nbsp;<input style="width:30px;border:1px solid #CCCCCC;" type="text"';
        $to_page_str .= ' onkeydown="javascript:if(event.keyCode==13){var page=(this.value>' . $this->page_num . ')?' . $this->page_num . ':this.value;';
        $to_page_str .= ' location=\'' . $this->url . 'page=\'+page+\'\';}" value="' . $this->page . '">';
        $to_page_str .= '<input style="cursor:pointer;" type="button" value="GO"';
        $to_page_str .= ' onclick="javascript:var page=(this.previousSibling.value >' . $this->page_num . ')?' . $this->page_num . ':this.previousSibling.value;';
        $to_page_str .= 'location=\'' . $this->url . 'page=\'+page+\'\';">&nbsp;';
        return $to_page_str;
    }

    /**
     * 本页记录数
     * 
     * @return int
     */
    private function disnum() {
        if ($this->total > 0) {
            return $this->end() - $this->start() + 1;
        }
        return 0;
    }

}
