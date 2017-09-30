<?php
namespace nbczw8750\tree;
/**
 +------------------------------------------------------------------------------
 * CCDeepTree 构建tree状数据
 +------------------------------------------------------------------------------
 * @author    nbczw8750@qq.com
 * @version   v1.0
 +------------------------------------------------------------------------------
 */
class DeepTree
{
    /**
     * 主键名称
     * @var string
     */
    private $_primary = 'id';

    /**
     * 父键名称
     * @var string
     */
    private $_parentId = 'parent_id';

    /**
     * 子节点名称
     * @var string
     */
    private $_child    = '_child';

    /**
     * 列表树层级
     * @var string
     */
    private $_level = "level";

    private $_emptyTemplate = array(
        "top" => "&nbsp;&nbsp;&nbsp;&nbsp;|",
        "middle" => "&nbsp;&nbsp;&nbsp;&nbsp;├─&nbsp;",
        "bottom" => "&nbsp;&nbsp;&nbsp;&nbsp;└─&nbsp;"
    );

    /**
     * 修改主键名称、父键名称、子节点名称
     * @param string $primary
     * @param string $parentId
     * @param string $child
     */
    public function setConfig($primary = '', $parentId = '', $child = ''){
        if(!empty($primary))  $this->_primary  = $primary;
        if(!empty($parentId)) $this->_parentId = $parentId;
        if(!empty($child))    $this->_child    = $child;
    }

    /**
     * 生成Tree
     * @param array $data
     * @param number $index
     * @return array
     */
    public  function  makeTree($data, $index = 0)
    {
        $children = $this->findChild($data, $index);
        if(empty($children))
        {
            return $children;
        }
        foreach($children as $k => &$v)
        {
            if(empty($data)) break;
            $child = $this->makeTree($data, $v[$this->_primary]);
            if(!empty($child))
            {
                $v[$this->_child] = $child;
            }
        }
        unset($v);
        $this->_tree = $children;
        return $children;
    }

    /**
     * 查找子类
     * @param array $data
     * @param number $index
     * @return array
     */
    protected function findChild($data, $index)
    {
        $children = [];
		foreach ($data as $k => $v){
			if($v[$this->_parentId] == $index){
                $children[]  = $v;
                unset($v);
			}
		}
		return $children;
    }

    /**
     * 将深度树转换为列表树
     * @param array $tree
     * @param int $level
     * @param string $flag
     * @return array
     */
    public function treeToList($tree = array() , $level = 1 , $flag = "level"){
        $this->_level = $flag;
        $list = array();
        foreach ($tree as $key => $value) {
            $temp = $value;
            if (isset($temp[$this->_child])) {
                $temp[$this->_child] = true;
                $temp[$flag] = $level;
            } else {
                $temp[$this->_child] = false;
                $temp[$flag] = $level;
            }
            array_push($list, $temp);
            if (isset($value[$this->_child])) {
                $list = array_merge($list,$this->treeToList($value[$this->_child], ($level + 1)));
            }
        }
        return $list;
    }


    /**
     * 生成数据列表树层级递进显示字段
     * @param array $list
     * @param string $flag 字段名
     * @return array
     */
    public function listEmptyDeal($list = array() , $flag = "level_show"){
        foreach ($list as $key => &$value){
            $index = ($key+1);
            $nextParentId = isset($list[$index][$this->_parentId]) ? $list[$index][$this->_parentId] : '';
            $value[$flag] = $this->_cateEmptyDeal($value, $nextParentId);
        }
        return $list;
    }

    /**
     * 设置列表树层级递进显示模板
     * [
     *      "top" => "&nbsp;&nbsp;&nbsp;&nbsp;|",
     *      "middle" => "&nbsp;&nbsp;&nbsp;&nbsp;├─&nbsp;",
     *      "bottom" => "&nbsp;&nbsp;&nbsp;&nbsp;└─&nbsp;"
     * ]
     * @param array $config
     */
    public function setEmptyTemplate($config = array()){
        $this->_emptyTemplate = array_merge($this->_emptyTemplate , $config);
    }

    private function _cateEmptyDeal($cat, $nextParentId){
        $str = "";
        if ($cat[$this->_parentId]) {
            for ($i=2; $i < $cat[$this->_level]; $i++) {
                $str .= $this->_emptyTemplate["top"];
            }
            if ($cat[$this->_parentId] != $nextParentId && !$cat['_child']) {
                $str .= $this->_emptyTemplate["bottom"];
            } else {
                $str .= $this->_emptyTemplate["middle"];
            }
        }
        return $str;
    }
}
