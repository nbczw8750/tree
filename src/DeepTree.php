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
    private $primary = 'id';

    /**
     * 父键名称
     * @var string
     */
    private $parentId = 'parent_id';

    /**
     * 子节点名称
     * @var string
     */
    private $child    = '_child';

    /**
     * 修改主键名称、父键名称、子节点名称
     * @param string $primary
     * @param string $parentId
     * @param string $child
     */
    public function setConfig($primary = '', $parentId = '', $child = ''){
        if(!empty($primary))  $this->primary  = $primary;
        if(!empty($parentId)) $this->parentId = $parentId;
        if(!empty($child))    $this->child    = $child;
    }

    /**
     * 生成Tree
     * @param array $data
     * @param number $index
     * @return array
     */
    public  function  makeTree($data, $index = 0)
    {
        $childs = $this->findChild($data, $index);
        if(empty($childs))
        {
            return $childs;
        }
        foreach($childs as $k => &$v)
        {
            if(empty($data)) break;
            $child = $this->makeTree($data, $v[$this->primary]);
            if(!empty($child))
            {
                $v[$this->child] = $child;
            }
        }
        unset($v);
        return $childs;
    }

    /**
     * 查找子类
     * @param array $data
     * @param number $index
     * @return array
     */
    protected function findChild($data, $index)
    {
        $childs = [];
		foreach ($data as $k => $v){
			if($v[$this->parentId] == $index){
				$childs[]  = $v;
                unset($v);
			}
		}
		return $childs;
    }

    /**
     * 将深度树转换为列表树
     * @param $tree
     * @param int $level
     * @param string $flag
     * @return array
     */
    public function treeToList($tree , $level = 1 , $flag = "level"){
        static $list = array();
        foreach ($tree as $key => $value) {
            $temp = $value;
            if (isset($temp[$this->child])) {
                $temp[$this->child] = true;
                $temp[$flag] = $level;
            } else {
                $temp[$this->child] = false;
                $temp[$flag] = $level;
            }
            array_push($list, $temp);
            if (isset($value[$this->child])) {
                $this->treeToList($value[$this->child], ($level + 1));
            }
        }
        return $list;
    }
}
