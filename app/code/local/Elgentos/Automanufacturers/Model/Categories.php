<?php
class Elgentos_Automanufacturers_Model_Categories
{
    protected $optionArray = array(''=>'');

    private function setSubOptions($categorieIds) {
        $categorieIds = explode(',', $categorieIds);
        foreach ($categorieIds as $categoryId){
            $category = Mage::getModel('catalog/category')->load($categoryId);

            $indent = '';
            for ($i=0; $i < $category->getLevel()-1; $i++) {
                $indent .= '-';
            }


            $this->optionArray[] = array(
                'value' => $category->getId(),
                'label' => $indent . $category->getName()
            );

            if ($subCategorieIds = $category->getChildren()) {
                $this->setSubOptions($subCategorieIds);
            }
        }
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $stores = Mage::app()->getStores();
        foreach ($stores as $storeId => $value) {
            $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
            $rootCategory = Mage::getModel('catalog/category')->load($rootCategoryId);
            $this->optionArray[] = array(
                'value' => $rootCategory->getId(),
                'label' => $rootCategory->getName()
            );
            if ($subCategorieIds = $rootCategory->getChildren()) {
                $this->setSubOptions($subCategorieIds);
            }
        }
        return $this->optionArray;
    }

}
