<?php
class Elgentos_Automanufacturers_Model_Observer
{

    public function createManuCats($observer)
    {
        if(Mage::getStoreConfig('automanufacturers/general/enabled')) {
            $request = Mage::app()->getRequest();
            $action = $request->getActionName();
            if($action=='quickCreate') return null;

            $helper = Mage::helper('automanufacturers');

            $productId = $observer->getProduct()->getId();
            $product = Mage::getModel('catalog/product')->load($productId);

            $attributeSets = Mage::getStoreConfig('automanufacturers/general/attributeset_filter');
            $attributeSetsIds = explode(',', $attributeSets);

            if(!in_array($product->getAttributeSetId(), $attributeSetsIds)) return null;

            $attributeInfo = Mage::helper('automanufacturers')->getAttributeInformation(Mage::getStoreConfig('automanufacturers/general/attribute'));
            $attributeCode = $attributeInfo['attribute_code'];

            if($productAttributeIndex = $product->getData($attributeCode)) {
                $manufacturerName = $helper->getAttributeValueByIndex($attributeCode, $productAttributeIndex);
                $mainCategoryId = Mage::getStoreConfig('automanufacturers/general/maincat');

                $mainCategory = Mage::getModel('catalog/category')->load($mainCategoryId);

                $manufacturerCategory = Mage::getModel('catalog/category')
                                                ->getCollection()
                                                ->addAttributeToFilter('parent_id', $mainCategoryId)
                                                ->addAttributeToFilter('name', $manufacturerName)
                                                ->getFirstItem();

                if ($manufacturerCategory->getId() === null) {
                    $manufacturerCategory = Mage::getModel('catalog/category');
                    $manufacturerCategory->setStoreId($mainCategory->getStoreId());
                    $manufacturerCategory->setName($manufacturerName);
                    $manufacturerCategory->setUrlKey($helper->slugify($manufacturerName));
                    $manufacturerCategory->setIsActive(1);
                    $manufacturerCategory->setIsAnchor(1);
                    $manufacturerCategory->setPath($mainCategory->getPath());
                    $manufacturerCategory->save();
                }

                if(Mage::getStoreConfig('automanufacturers/general/add_to_cat')) {
                    $categoryIds = $product->getCategoryIds();
                    if(!in_array($manufacturerCategory->getId(), $categoryIds)) {
                        $categoryIds[] = $manufacturerCategory->getId();
                        $product->setCategoryIds($categoryIds)->save();
                    }
                }
            }
        }
    }
}
