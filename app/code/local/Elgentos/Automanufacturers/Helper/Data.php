<?php

class Elgentos_Automanufacturers_Helper_Data extends Mage_Core_Helper_Abstract {

    public function createCategory($categoryName,$parentcategory=null) {
        return $this->getCategoryIdByName($categoryName,true,$parentcategory);
    }

    public function getCategoryIdByName($categoryName,$create=false,$parentcategory=null) {
        $categoryName = trim($categoryName);
        $category = Mage::getModel('catalog/category')->loadByAttribute('name',$categoryName);
        if($category!=false) {
            return $category->getId();
        } else {
            if($create) {
                $storeId = Mage::app()->getStore()->getStoreId();
                $category = Mage::getModel('catalog/category');
                $category->setStoreId($storeId);
                $category->setName($categoryName);
                $category->setUrlKey($this->slugify($categoryName));
                $category->setIsActive(1);
                $category->setIsAnchor(1);
                if($parentcategory!=null) {
                    if(is_numeric($parentcategory)) {
                        $parentId = $parentcategory;
                    } else {
                        $parentId = $this->getCategoryIdByName($parentcategory,true);
                    }
                } else {
                    $parentId = Mage::app()->getStore($storeId)->getRootCategoryId();
                }
                $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
                $category->save();
                return $category->getId();
            } else {
                throw new Exception($categoryName." category could not be found.");
            }
        }
    }

    public function getAttributeInformation($attribute) {
        if(is_numeric($attribute)) {
            $attributeId = $attribute;
        } else{
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product',$attribute);
        }
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        return $attribute->getData();
    }

    public function getAttributeValueByIndex($attribute,$index) {
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product',$attribute);
        foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
            $attributeArray[$option['value']] = $option['label'];
        }
        if(isset($attributeArray[$index])) {
            return $attributeArray[$index];
        } else {
            return false;
        }
    }

    private function transcribe($string) {
        $string = strtr($string,
        "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7
        \xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1
        \xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0
        \xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC
        \xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8
        \xF9\xFA\xFB\xFD\xFF",
        "!ao?AAAAAC
        EEEEIIIIDN
        OOOOOUUUYa
        aaaaceeeei
        iiidnooooo
        uuuyy");
        $string = strtr($string, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
        return($string);
    }

    public function slugify($output,$divider="-") {
        // Transcribe (remove umlauts, accents, etc)
        $output = $this->transcribe($output);
        // Replace spaces with hypens
        $output = preg_replace("/\s/e" , "'_'" , $output);
        // Remove non-word characters
        $output = preg_replace("/\W/e" , " " , $output);
        // Turn quadruple, triple and double dashes into single dashes
        $output = str_replace("____",$divider,$output);
        $output = str_replace("___",$divider,$output);
        $output = str_replace("__",$divider,$output);
        $output = str_replace("_",$divider,$output);
        return strtolower($output);
    }
}
