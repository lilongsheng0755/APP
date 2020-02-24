<?php


namespace Lib\System;

/**
 * Author: skylong
 * CreateTime: 2020/2/17 21:03
 * Description: 数据验证类
 */
class Validate
{
    /**
     * 字段类型：日期格式 0000-00-00或0000/00/00
     */
    const FIELD_IS_DATE = 0;

    /**
     * 字段类型：邮箱地址
     */
    const FIELD_IS_EMAIL = 1;

    /**
     * 字段类型：URL地址
     */
    const FIELD_IS_URL = 2;

    /**
     * 默认错误信息
     *
     * @var array
     */
    private $default_msg = [
        'require'     => '%s 不能为空！',
        'int'         => '%s 必须为整数！',
        'float'       => '%s 必须为浮点数字！',
        'boolean'     => '%s 必须为布尔值！',
        'email'       => '%s 必须为email地址！', // 采用filter_var验证
        'array'       => '%s 必须为数组！',
        'accepted'    => '%s 必须为yes,on！',
        'date'        => '%s 必须为有效的日期！',
        'alpha'       => '%s 只能是纯字母！',
        'alphaNum'    => '%s 只能是字母和数字！',
        'alphaDash'   => '%s 只能是字母和数字，下划线及减号！',
        'chs'         => '%s 只能是汉字！',
        'chsAlpha'    => '%s 只能是汉字、字母！',
        'chsAlphaNum' => '%s 只能是汉字、字母和数字！',
        'chsDash'     => '%s 只能是汉字、字母、数字和下划线及减号！',
        'activeUrl'   => '%s 必须为有效的域名或者IP！',
        'url'         => '%s 必须为有效的URL地址！', // 采用filter_var验证
        'ip'          => '%s 必须为有效的IP地址！', // 采用filter_var验证,支持验证ipv4和ipv6格式的IP地址
        'dateFormat'  => '%s 必须为指定格式的日期！', // 例如：'create_time'=>'dateFormat:y-m-d'
        'in'          => '%s 只能是%s！', // 例如：'num'=>'in:1,2,3'
        'notIn'       => '%s 不能是%s！', // 例如：'num'=>'notIn:1,2,3'
        'between'     => '%s 必须在%s ~ %s这个范围（包括两端）！', // 例如：'num'=>'between:1,10'
        'notBetween'  => '%s 不能在%s ~ %s这个范围（包括两端）！', // 例如：'num'=>'notBetween:1,10'
        'length'      => '%s 长度必须在%s ~ %s这个范围（包括两端）！',// 例如：'name'=>'length:4,25'或'name'=>'length:4'
        'maxLength'   => '%s 最大长度不能超过%s！',// 例如：'name'=>'maxLength:25'
        'mixLength'   => '%s 最小长度不能低于%s！',// 例如：'name'=>'minLength:4'
        'after'       => '%s 必须在%s之后！',// 例如：'begin_time' => 'after:2016-3-18',
        'before'      => '%s 必须在%s之前！',// 例如：'end_time' => 'before:2016-3-18',
        'expire'      => '%s 必须在%s ~ %s之内（包括两端）！',// 例如：'expire_time'   => 'expire:2016-2-1,2016-10-01',
        'confirm'     => '%s 的值必须和%s的值一致！',// 例如：'repassword'=>'require|confirm:password'
        'different'   => '%s 的值必须和%s的值不一致！',// 例如：'name'=>'require|different:account'
        'eq'          => '%s 必须等于%s！',// 例如：'score'=>'eq:100'
        'egt'         => '%s 必须大于等于%s！',// 例如：'score'=>'egt:60'
        'gt'          => '%s 必须大于%s！',// 例如：'score'=>'eq:100'
        'elt'         => '%s 必须小于等于%s！',// 例如：'score'=>'eq:100'
        'lt'          => '%s 必须小于%s！',// 例如：'score'=>'eq:100'
        'regex'       => '%s 格式不对！',// 例如：'accepted'=>['regex'=>'/^(yes|on|1)$/i']
    ];

    /**
     * 默认错误码
     *
     * @var array
     */
    private $default_code = [
        'require'     => 10001,
        'int'         => 10002,
        'float'       => 10003,
        'boolean'     => 10004,
        'email'       => 10005,
        'array'       => 10006,
        'accepted'    => 10007,
        'date'        => 10008,
        'alpha'       => 10009,
        'alphaNum'    => 10010,
        'alphaDash'   => 10011,
        'chs'         => 10012,
        'chsAlpha'    => 10013,
        'chsAlphaNum' => 10014,
        'chsDash'     => 10015,
        'activeUrl'   => 10016,
        'url'         => 10017,
        'ip'          => 10018,
        'dateFormat'  => 10019,
        'in'          => 10020,
        'notIn'       => 10021,
        'between'     => 10022,
        'notBetween'  => 10023,
        'length'      => 10024,
        'maxLength'   => 10025,
        'mixLength'   => 10026,
        'after'       => 10027,
        'before'      => 10028,
        'expire'      => 10029,
        'confirm'     => 10030,
        'different'   => 10031,
        'eq'          => 10032,
        'egt'         => 10033,
        'gt'          => 10034,
        'elt'         => 10035,
        'lt'          => 10036,
        'regex'       => 10037,
    ];

    /**
     * 需要验证的字段规则
     *
     * @var array
     */
    protected $rule = [];

    /**
     * 验证字段的错误信息
     *
     * @var array
     */
    protected $message = [];

    /**
     * 验证字段的错误码
     *
     * @var array
     */
    protected $err_code = [];

    /**
     * 验证场景
     *
     * @var array
     */
    protected $scene = [];

    /**
     * 字段验证规则
     *
     * @param $rule
     *
     * @return $this
     */
    public function rule($rule)
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * 自定义验证规则错误信息
     *
     * @param array $message
     *
     * @return $this
     */
    public function message($message = [])
    {
        $this->message = $message;
        return $this;
    }

    /**
     * 自定义验证规则错误码
     *
     * @param $err_code
     *
     * @return $this
     */
    public function err_code($err_code)
    {
        $this->err_code = $err_code;
        return $this;
    }

    /**
     * 需要验证的场景【控制器，模型】
     *
     * @param string $scene
     *
     * @return Validate
     */
    public function scene($scene = '')
    {
        $this->scene = $scene;
        return $this;
    }

    /**
     * 验证数据是否有效
     *
     * @param array $data
     *
     * @return bool
     * @throws SException
     */
    public function check($data = [])
    {
        if (!$this->scene || !is_array($this->scene)) {
            return false;
        }
        // 遍历需要验证的方法字段
        foreach ($this->scene as $scene => $fields) {
            if (!$fields || !is_array($fields)) {
                continue;
            }
            //遍历需要验证的字段
            foreach ($fields as $field) {
                if (!isset($this->rule[$field]) || !isset($data[$field])) {
                    continue;
                }
                // 解析验证规则
                if (is_array($this->rule[$field]) && isset($this->rule[$field]['regex'])) {
                    $flag = self::validateRegex($data[$field], $this->rule[$field]['regex']);
                    if (!$flag) {
                        $err_msg = isset($this->message[$field . '.regex']) ? $this->message[$field . '.regex'] : sprintf($this->default_msg['regex'], $field);
                        $err_code = isset($this->err_code[$field . '.regex']) ? $this->err_code[$field . '.regex'] : $this->default_code['regex'];
                        throw new SException($err_msg, $err_code);
                    }
                } elseif ($this->rule[$field]) {
                    $rule_arr = explode('|', $this->rule[$field]);
                    foreach ($rule_arr as $rule) { // 执行规则验证
                        $val = '';
                        if (strrpos($rule, ':') !== false) {
                            list($ck, $val) = explode(':', $rule);
                            $func = 'validate' . ucfirst($ck);
                            $flag = self::$func($data[$field], $val);
                        } else {
                            $func = 'validate' . ucfirst($rule);
                            $flag = self::$func($data[$field]);
                        }
                        if (!$flag) {
                            $rule_name = isset($ck) ? $ck : $rule;
                            $s_num = substr_count($this->default_msg[$rule_name], 's');
                            if ($s_num == 2) {
                                $default_msg = sprintf($this->default_msg[$field . '.' . $rule_name], $field, $val);
                            } elseif ($s_num == 3) {
                                $val_arr = explode(',', $val);
                                $default_msg = sprintf($this->default_msg[$field . '.' . $rule_name], $field, $val_arr[0], $val_arr[1]);
                            } else {
                                $default_msg = sprintf($this->default_msg[$field . '.' . $rule_name], $field);
                            }
                            $err_msg = isset($this->message[$field . '.' . $rule_name]) ? $this->message[$field . '.' . $rule_name] : $default_msg;
                            $err_code = isset($this->err_code[$field . '.' . $rule_name]) ? $this->err_code[$field . '.' . $rule_name] : $this->default_code[$field . '.' . $rule_name];
                            throw new SException($err_msg, $err_code);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * 验证是否为null，''
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateRequire($var = '')
    {
        $var = trim($var);
        if ($var === null || $var === '') {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为整型
     *
     * @param int $var
     *
     * @return bool
     */
    public static function validateInt($var = 0)
    {
        if (is_string($var)) {
            return false;
        }
        if (!is_int($var)) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为浮点型
     *
     * @param float $var
     *
     * @return bool
     */
    public static function validateFloat($var = 0.0)
    {
        if (is_string($var)) {
            return false;
        }
        if (!is_float($var)) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为布尔型
     *
     * @param bool $var
     *
     * @return bool
     */
    public static function validateBoolean($var = false)
    {
        if ($var !== false && $var !== true) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为邮箱地址
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateEmail($var = '')
    {
        return (bool)preg_match('/^[A-Za-z0-9]+([_\.][A-Za-z0-9]+)*@([A-Za-z0-9\-]+\.)+[A-Za-z]{2,6}$/', $var);
    }

    /**
     * 验证是否为数组
     *
     * @param array $var
     *
     * @return bool
     */
    public static function validateArray($var = [])
    {
        if (!is_array($var)) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为'yes' or 'no'
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateAccepted($var = 'yes')
    {
        if (!in_array($var, ['yes', 'no', 'Yes', 'No'])) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为日期格式
     *
     * @param string $var 日期格式：0000-00-00 或 0000/00/00
     *
     * @return bool
     */
    public static function validateDate($var = '0000-00-00')
    {
        $time = strtotime($var);
        if ($var != date('Y-m-d', $time) && $var != date('Y/m/d', $time)) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否为纯字母格式
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateAlpha($var = '')
    {
        return (bool)preg_match('/^[A-Za-z]+$/', $var);
    }

    /**
     * 验证是否为字母、数字
     *
     * @param string $var
     *
     * @param bool   $is_all 是否完全匹配，默认：false，匹配其中一种就可以
     *
     * @return bool
     */
    public static function validateAlphaNum($var = '', $is_all = false)
    {
        $preg_flag = (bool)preg_match('/^[A-Za-z0-9]+$/', $var);
        if (!$is_all || $preg_flag === false) {
            return $preg_flag;
        }
        // 完全匹配代码待补充
        return true;
    }

    /**
     * 验证是否为字母、数字，下划线、减号
     *
     * @param string $var
     *
     * @param bool   $is_all 是否必须包含了字母和数字，默认：false，匹配字母或数字其中一种就可以
     *
     * @return bool
     */
    public static function validateAlphaDash($var = '', $is_all = false)
    {
        $preg_flag = (bool)preg_match('/^[A-Za-z0-9_\-]+$/', $var);
        if (!$is_all || $preg_flag === false) {
            return $preg_flag;
        }
        // 完全匹配代码待补充
        return true;
    }

    /**
     * 验证是否为纯中文汉字
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateChs($var = '')
    {
        return (bool)preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $var);
    }

    /**
     * 验证是否为中文、字母
     *
     * @param string $var
     * @param bool   $is_all 是否必须包含了字母和数字，默认：false，匹配字母或数字其中一种就可以
     *
     * @return bool
     */
    public static function validateChsAlpha($var = '', $is_all = false)
    {
        $preg_flag = (bool)preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z]+$/u', $var);
        if (!$is_all || $preg_flag === false) {
            return $preg_flag;
        }
        // 完全匹配代码待补充
        return true;
    }

    /**
     * 验证是否为中文、字母、数字
     *
     * @param string $var
     * @param bool   $is_all 是否必须包含了字母和数字，默认：false，匹配字母或数字其中一种就可以
     *
     * @return bool
     */
    public static function validateChsAlphaNum($var = '', $is_all = false)
    {
        $preg_flag = (bool)preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $var);
        if (!$is_all || $preg_flag === false) {
            return $preg_flag;
        }
        // 完全匹配代码待补充
        return true;
    }

    /**
     * 验证是否为中文、字母、数字、下划线、减号
     *
     * @param string $var
     * @param bool   $is_all 是否必须包含了字母和数字，默认：false，匹配字母或数字其中一种就可以
     *
     * @return bool
     */
    public static function validateChsDash($var = '', $is_all = false)
    {
        $preg_flag = (bool)preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\-]+$/u', $var);
        if (!$is_all || $preg_flag === false) {
            return $preg_flag;
        }
        // 完全匹配代码待补充
        return true;
    }

    /**
     * 验证是否为有效的域名或IP
     *
     * @param string $var
     * @param string $rule 支持A，MX，NS，SOA，PTR，CNAME，AAAA，A6， SRV，NAPTR，TXT 或者 ANY类型
     *
     * @return bool
     */
    public static function validateActiveUrl($var = '', $rule = 'MX')
    {
        if (!in_array($rule, ['A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY'])) {
            $rule = 'MX';
        }

        return checkdnsrr($var, $rule);
    }

    /**
     * 验证是否为URL地址
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateUrl($var = '')
    {
        return (bool)filter_var($var, FILTER_VALIDATE_URL);
    }

    /**
     * 验证是否为IP地址
     *
     * @param string $var
     *
     * @return bool
     */
    public static function validateIp($var = '')
    {
        return (bool)filter_var($var, FILTER_VALIDATE_IP);
    }

    /**
     * 验证指定格式的日期，内置方法：date_parse_from_format
     *
     * @param string $rule 日期规则：y-m-d
     * @param string $value
     *
     * @return bool
     */
    public static function validateDateFormat($rule = '', $value = '')
    {
        $info = date_parse_from_format($rule, $value);
        return 0 == $info['warning_count'] && 0 == $info['error_count'];
    }

    /**
     * 验证是否在指定值内
     *
     * @param string $var
     * @param string $in_list 多个指定值用逗号隔开
     *
     * @return bool
     */
    public static function validateIn($var = '', $in_list = '')
    {
        if (!$in_list) {
            return false;
        }
        if (!in_array($var, explode(',', $in_list))) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否不在指定值内
     *
     * @param string $var
     * @param string $in_list 多个指定值用逗号隔开:1,2,3
     *
     * @return bool
     */
    public static function validateNotIn($var = '', $in_list = '')
    {
        if (!$in_list) {
            return false;
        }
        if (in_array($var, explode(',', $in_list))) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否在指定范围值
     *
     * @param string $var
     * @param string $between 范围值：0,10
     *
     * @return bool
     */
    public static function validateBetween($var = '', $between = '')
    {
        if (!$between || strpos($between, ',') === false) {
            return false;
        }
        list($min, $max) = explode(',', $between);
        if ($var < $min || $var > $max) {
            return false;
        }
        return true;
    }

    /**
     * 验证是否不在指定范围值
     *
     * @param string $var
     * @param string $between 范围值：0,10
     *
     * @return bool
     */
    public static function validateNotBetween($var = '', $between = '')
    {
        if (!$between || strpos($between, ',') === false) {
            return false;
        }
        list($min, $max) = explode(',', $between);
        if ($var >= $min && $var <= $max) {
            return false;
        }
        return true;
    }

    /**
     * 验证长度是否在指定范围值
     *
     * @param string $var
     * @param string $round_length 长度范围：0,10
     *
     * @return bool
     */
    public static function validateLength($var = '', $round_length = '')
    {
        if (!$round_length || strpos($round_length, ',') === false) {
            return false;
        }
        $var_length = mb_strlen($var);
        list($min, $max) = explode(',', $round_length);
        if ($var_length < $min || $var_length > $max) {
            return false;
        }
        return true;
    }

    /**
     * 验证长度是否低于最小长度
     *
     * @param string $var
     * @param int length
     *
     * @return bool
     */
    public static function validateMinLength($var = '', $length = 0)
    {
        if (!$length) {
            return false;
        }
        $var_length = mb_strlen($var);
        if ($var_length < $length) {
            return false;
        }
        return true;
    }

    /**
     * 验证长度是否高于最大长度
     *
     * @param string $var
     * @param int length
     *
     * @return bool
     */
    public static function validateMaxLength($var = '', $length = 0)
    {
        if (!$length) {
            return false;
        }
        $var_length = mb_strlen($var);
        if ($var_length > $length) {
            return false;
        }
        return true;
    }

    /**
     * 验证日期是否在指定日期之后
     *
     * @param string $var
     * @param string $date
     *
     * @return bool
     */
    public static function validateAfter($var = '', $date = '')
    {
        return strtotime($var) >= strtotime($date);
    }

    /**
     * 验证日期是否在指定日期之前
     *
     * @param string $var
     * @param string $date
     *
     * @return bool
     */
    public static function validateBefore($var = '', $date = '')
    {
        return strtotime($var) <= strtotime($date);
    }


    /**
     * 验证日期是否在指定日期范围内
     *
     * @param string $var        字段值
     * @param string $round_date 日期格式：2020-02-22,2020-02-24或1582536000,1582536809
     *
     * @return bool
     */
    public static function validateExpire($var = '', $round_date = '')
    {
        if (!is_string($round_date) || strpos($round_date, ',') === false) {
            return false;
        }

        list($start, $end) = explode(',', $round_date);
        if (!is_numeric($start)) {
            $start = strtotime($start);
        }

        if (!is_numeric($end)) {
            $end = strtotime($end);
        }

        if (!is_numeric($var)) {
            $var = strtotime($var);
        }

        return $var >= $start && $var <= $end;
    }

    /**
     * 验证两个值是否一致
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateConfirm($var = '', $c_var = '')
    {
        return $var === $c_var;
    }

    /**
     * 验证两个值是否不一致
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateDifferent($var = '', $c_var = '')
    {
        return $var !== $c_var;
    }

    /**
     * 验证两个值是否相等
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateEq($var = '', $c_var = '')
    {
        return $var == $c_var;
    }

    /**
     * 验证两个值是否大于等于
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateEgt($var = '', $c_var = '')
    {
        return $var >= $c_var;
    }

    /**
     * 验证两个值是否大于
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateGt($var = '', $c_var = '')
    {
        return $var > $c_var;
    }

    /**
     * 验证两个值是否小于等于
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateElt($var = '', $c_var = '')
    {
        return $var <= $c_var;
    }

    /**
     * 验证两个值是否小于
     *
     * @param string $var
     * @param string $c_var
     *
     * @return bool
     */
    public static function validateLt($var = '', $c_var = '')
    {
        return $var < $c_var;
    }

    /**
     * 验证是否匹配正则表达式
     *
     * @param string $var
     * @param string $preg
     *
     * @return bool
     */
    public static function validateRegex($var = '', $preg = '')
    {
        return (bool)preg_match($preg, $var);
    }
}