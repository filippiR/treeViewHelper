<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * CakePHP TreeViewHelper
 */
class TreeViewHelper extends Helper {

    public $helpers = ['Html', 'Url'];
    public $link;
    public $lsPics;
    public $linkOptions;

    public function createTree($tree, $link, $lsPics=array(), $linkOptions = array()) {
        $this->link = $link;
        $this->lsPics = array();
// 		$this->lsPics = $lsPics;
        $this->linkOptions = $linkOptions;
        $out = '';
        $count = 0;
        $buffer = null;
        foreach ($tree as $id => $item) {
            $depth = $this->getDepth($item, '_');
            //pr($depth);
            if ($depth == 0) {
                $clean_item = $item;
            } else {

                $clean_item = substr($item, strrpos($item, '_') + 1);
            }
            //pr($clean_item);
            if ($buffer != null) {
                $out .= $this->makeLi($buffer, $depth, $id, $lsPics);
            }
            $buffer['item'] = $clean_item;
            $buffer['depth'] = $depth;
            $buffer['id'] = $id;
        }
        if ($buffer != null)
            $out .=$this->makeLi($buffer, 0, $id, $lsPics);
        //pr($out);
        return $out;
    }

    public function createLinkedTree($tree, $link, $lsPics = array(), $adicionalLinks = array(), $linkOptions = array()) {
        $this->link = $link;
        $this->lsPics = $lsPics;
        $this->linkOptions = $linkOptions;
        $out = '';
        $count = 0;
        $buffer = null;
        foreach ($tree as $id => $item) {
            $depth = $this->getDepth($item, '_');
            if ($depth == 0) {
                $depth = 0;
                $clean_item = $item;
            } else {
                $depth = $depth + 1;
                $clean_item = substr($item, strrpos($item, '_') + 1);
            }
            if ($buffer != null) {
                $out .= $this->makeAllLinkedLi($buffer, $depth, $id, $lsPics, $adicionalLinks);
            }
            $buffer['item'] = $clean_item;
            $buffer['depth'] = $depth;
            $buffer['id'] = $id;
        }
        if ($buffer != null)
            $out .=$this->makeAllLinkedLi($buffer, 0, $id, $lsPics, $adicionalLinks);
        return $out;
    }

    protected function getDepth($string, $ident = '_') {
        $array = str_split($string);
        $count = 0;
        foreach ($array as $ar) {
            if ($ar == '_') {
                $count++;
            } else {
                return $count;
            }
        }
    }

    protected function makeLi($buffer, $depth, $id) {
        $out = '';
        $class = '';
        $foto = isset($this->lsPics[$buffer['id']]) ? $this->Html->image($this->lsPics[$buffer['id']], array('height' => '40', 'width' => '40')) : '';
        if ($buffer['depth'] == 0) {
            $class = 'raiz';
        }
        if ($buffer['depth'] == $depth) {
            $out = '<li class="' . $class . ' hasul">' . $this->makeLabel($buffer['item'], $buffer['id'], $foto) . "</li>\n";
        } elseif ($buffer['depth'] < $depth) {
            $out = '<li class="' . $class . ' hasul">' . $this->makeLabel($buffer['item'], null, $foto) . "\n<ul>\n";
        } elseif ($buffer['depth'] > $depth) {
            $out = "<li class=''.$class.' hasul'>" . $this->makeLabel($buffer['item'], $buffer['id'], $foto) . "</li>\n";

            $diff = $buffer['depth'] - $depth;
            for ($i = 0; $i < $diff; $i++) {
                $out .= "</ul> \n </li>\n";
            }
        }
        return $out;
    }

    protected function makeAllLinkedLi($buffer, $depth, $id, $lsPics = array(), $adicionalLinks = array()) {
        $out = '';
        $foto = isset($this->lsPics[$buffer['id']]) ? $this->Html->image($this->lsPics[$buffer['id']], array('height' => '40', 'width' => '40')) : '';
        if ($buffer['depth'] == $depth) {
            $out = '<li>' . $this->makeLabel($buffer['item'], $buffer['id'], $foto, $adicionalLinks) . "</li>\n";
        } elseif ($buffer['depth'] < $depth) {
            $out = '<li>' . $this->makeLabel($buffer['item'], $buffer['id'], $foto, $adicionalLinks) . "\n<ul data-role='listview'>\n";
        } elseif ($buffer['depth'] > $depth) {
            $out = "<li>" . $this->makeLabel($buffer['item'], $buffer['id'], $foto, $adicionalLinks) . "</li>\n";

            $diff = $buffer['depth'] - $depth;
            for ($i = 0; $i < $diff; $i++) {
                $out .= "</ul> \n </li>\n";
            }
        }
        return $out;
    }

    protected function makeLabel($item, $id = null, $foto = '', $links = array()) {
        if ($id != null) {
            if (isset($this->linkOptions['ajaxMobile']) and $this->linkOptions['ajaxMobile'] == true) {
// 				return  $this->Html->link($foto.' <span>'.$item.'</span>', '#',array('data-url'=>$this->mountLink($id),'data-transation'=>'slide',"class"=>"",'escape'=>false)).' '.$this->mountAdicLinks($links, $id);

                return '<a href="' . $this->Url->build($this->mountLink($id)) . '" class="ui-link-inherit" data-transation="slide" data-theme="e" data-content-theme="e" >' . $foto . ' <span>' . $item . '</span></a>';
            }
            return $this->Html->link($foto . ' <span>' . $item . '</span>', $this->mountLink($id), array("class" => "ui-link-inherit", 'data-ajax' => 'false', 'escape' => false)) . ' ' . $this->mountAdicLinks($links, $id);
        }
        return ' <a href="#">' . $foto . '<span>' . $item . '</span></a>';
    }

    protected function mountLink($id) {
        if (is_array($this->link)) {
            $link = $this->link;
            $link[] = $id;
        } else {
            $link = rtrim($this->link, '/') . '/' . $id;
        }
        //pr($link);
        return $link;
    }

    protected function mountAdicLinks($links, $id) {
        $out = '';
        if (count($links) > 0) {
            foreach ($links as $lnk) {

                if (is_array($lnk['url'])) {
                    $link = $lnk['url'];
                    $link[] = $id;
                } else {
                    $link = rtrim($lnk['url'], '/') . '/' . $id;
                }
                $label = isset($lnk['imagem']) ? $lnk['imagem'] : $lnk['label'];
                if (isset($this->linkOptions['ajaxMobile']) and $this->linkOptions['ajaxMobile'] == true) {
                    $out .= $this->Html->link($label, '#', array('data-url' => $link, 'data-transation' => 'slide', 'data-role' => 'button', 'escape' => false));
                } else {
                    $out .= $this->Html->link($label, $link, array('escape' => false));
                }
            }
        }
        return $out;
    }

}
