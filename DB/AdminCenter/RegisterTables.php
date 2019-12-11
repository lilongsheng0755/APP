<?php
/**
 * Author: skylong
 * CreateTime: 2019/12/11 15:00
 * Description: 注册数据表对象
 */

namespace DB\AdminCenter;


use DB\DBDesign;
use DB\TableConstMap;
use DB\TableDesign;

class RegisterTables
{
    public static function register()
    {
        // 设置数据库名称
        DBDesign::getInstance()->setDbName('admin_center');

        // 注册数据表：后台管理中心 - 接入应用表
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_apps');
        $table_obj->setTableNotes('后台管理中心 - 接入应用表');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_MYISAM);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8MB4);
        $table_obj->setTableFieldAttr('id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('app_name', TableConstMap::FIELD_TYPE_VARCHAR, '50', false, '', '应用名称');
        $table_obj->setTableFieldAttr('app_id', TableConstMap::FIELD_TYPE_SMALLINT, '5', false, '0', '接入系统应用ID', true);
        $table_obj->setTableFieldAttr('app_status', TableConstMap::FIELD_TYPE_TINYINT, '3', false, '1', '状态：0-已下架，1-已上架', true);
        $table_obj->setTableFieldAttr('app_remark', TableConstMap::FIELD_TYPE_VARCHAR, '255', false, '', '备注信息');
        $table_obj->setTableFieldAttr('create_time', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '创建时间', true);
        $table_obj->setTableFieldAttr('modify_time', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '更新时间', true);
        $table_obj->setPrimaryKey(['id']);
        $table_obj->setUniqueKey('uk_app_id', ['app_id']);
        DBDesign::getInstance()->addTableObject($table_obj);

        // 注册数据表：后台管理中心 - 菜单表
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_menu');
        $table_obj->setTableNotes('后台管理中心 - 菜单表');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_MYISAM);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8);
        $table_obj->setTableFieldAttr('menu_id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('menu_name', TableConstMap::FIELD_TYPE_VARCHAR, '50', false, '', '菜单名称');
        $table_obj->setTableFieldAttr('menu_icon', TableConstMap::FIELD_TYPE_VARCHAR, '30', false, '', '菜单图标');
        $table_obj->setTableFieldAttr('menu_path', TableConstMap::FIELD_TYPE_VARCHAR, '100', false, '', '菜单路径');
        $table_obj->setTableFieldAttr('menu_position', TableConstMap::FIELD_TYPE_TINYINT, '3', false, '0', '菜单展示位置：0-左侧，1-顶部，2-底部，3右侧', true);
        $table_obj->setTableFieldAttr('menu_status', TableConstMap::FIELD_TYPE_TINYINT, '3', false, '1', '菜单状态 0.禁用 1.启用 默认1', true);
        $table_obj->setTableFieldAttr('menu_parent_id', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '菜单父级ID', true);
        $table_obj->setTableFieldAttr('menu_create', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '菜单创建时间', true);
        $table_obj->setTableFieldAttr('menu_modify', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '菜单更新时间', true);
        $table_obj->setTableFieldAttr('menu_remark', TableConstMap::FIELD_TYPE_VARCHAR, '255', false, '', '菜单备注');
        $table_obj->setPrimaryKey(['menu_id']);
        $table_obj->setUniqueKey('uk_menu_name', ['menu_name']);
        DBDesign::getInstance()->addTableObject($table_obj);

        // 注册数据表：后台管理中心 - 后台访问记录
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_request_history');
        $table_obj->setTableNotes('后台管理中心 - 后台访问记录');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_INNODB);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8MB4);
        $table_obj->setTableFieldAttr('id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('user_id', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台用户ID', true);
        $table_obj->setTableFieldAttr('request_method', TableConstMap::FIELD_TYPE_VARCHAR, '10', false, '', '请求方法');
        $table_obj->setTableFieldAttr('rule_path', TableConstMap::FIELD_TYPE_VARCHAR, '100', false, '', '路由地址');
        $table_obj->setTableFieldAttr('rule_path_md5', TableConstMap::FIELD_TYPE_CHAR, '32', false, '', '用于查询检索');
        $table_obj->setTableFieldAttr('request_params', TableConstMap::FIELD_TYPE_TEXT, null, false, null, '请求参数：json格式');
        $table_obj->setTableFieldAttr('create_time', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '创建时间', true);
        $table_obj->setPrimaryKey(['id']);
        $table_obj->setIndexKey('k_rule_path_md5', ['rule_path_md5']);
        DBDesign::getInstance()->addTableObject($table_obj);

        // 注册数据表：后台管理中心 - 用户角色表
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_roles');
        $table_obj->setTableNotes('后台管理中心 - 用户角色表');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_MYISAM);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8);
        $table_obj->setTableFieldAttr('role_id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('role_tag', TableConstMap::FIELD_TYPE_VARCHAR, '30', false, '', '角色唯一标识');
        $table_obj->setTableFieldAttr('role_name', TableConstMap::FIELD_TYPE_VARCHAR, '50', false, '', '角色名称');
        $table_obj->setTableFieldAttr('role_status', TableConstMap::FIELD_TYPE_TINYINT, '3', false, '1', '角色状态 0.禁用 1.启用 默认1', true);
        $table_obj->setTableFieldAttr('role_create', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '创建时间', true);
        $table_obj->setTableFieldAttr('role_modify', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '更新时间', true);
        $table_obj->setTableFieldAttr('role_remark', TableConstMap::FIELD_TYPE_VARCHAR, '255', false, '', '备注');
        $table_obj->setPrimaryKey(['role_id']);
        $table_obj->setUniqueKey('uk_role_tag', ['role_tag']);
        DBDesign::getInstance()->addTableObject($table_obj);

        // 注册数据表：后台管理中心 - 用户角色权限关系表
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_roles_rules');
        $table_obj->setTableNotes('后台管理中心 - 用户角色权限关系表');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_MYISAM);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8);
        $table_obj->setTableFieldAttr('id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('role_id', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '用户角色ID', true);
        $table_obj->setTableFieldAttr('rule_id', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '权限规则ID', true);
        $table_obj->setPrimaryKey(['id']);
        $table_obj->setUniqueKey('uk_role_id', ['role_id', 'rule_id']);
        DBDesign::getInstance()->addTableObject($table_obj);

        // 注册数据表：后台管理中心 - 用户角色关系表
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_roles_users');
        $table_obj->setTableNotes('后台管理中心 - 用户角色关系表');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_MYISAM);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8);
        $table_obj->setTableFieldAttr('id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('user_id', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台用户ID', true);
        $table_obj->setTableFieldAttr('role_id', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台角色ID', true);
        $table_obj->setPrimaryKey(['id']);
        $table_obj->setUniqueKey('uk_user_id', ['user_id', 'role_id']);
        DBDesign::getInstance()->addTableObject($table_obj);

        // 注册数据表：后台管理中心 - 权限规则表++++++++++++++++++++
        $table_obj = new TableDesign();
        $table_obj->setTableName('admin_rules');
        $table_obj->setTableNotes('后台管理中心 - 权限规则表');
        $table_obj->setTableEngine(TableConstMap::TABLE_ENGINE_MYISAM);
        $table_obj->setTableDefaultCharset(TableConstMap::TABLE_CHARSET_UTF8);
        $table_obj->setTableFieldAttr('rule_id', TableConstMap::FIELD_TYPE_INT, '10', false, null, '自增ID', true, true);
        $table_obj->setTableFieldAttr('rule_tag', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台用户ID', true);
        $table_obj->setTableFieldAttr('rule_name', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台角色ID', true);
        $table_obj->setTableFieldAttr('rule_status', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台角色ID', true);
        $table_obj->setTableFieldAttr('rule_create', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台角色ID', true);
        $table_obj->setTableFieldAttr('rule_modify', TableConstMap::FIELD_TYPE_INT, '10', false, '0', '后台角色ID', true);
        $table_obj->setPrimaryKey(['id']);
        $table_obj->setUniqueKey('uk_user_id', ['user_id', 'role_id']);
        DBDesign::getInstance()->addTableObject($table_obj);

        $sql = DBDesign::getInstance()->getCreateTablesSql();

        echo '<pre>';
        echo $sql;
    }
}