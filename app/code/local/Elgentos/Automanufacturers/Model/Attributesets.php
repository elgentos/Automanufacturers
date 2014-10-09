<?php
class Elgentos_Automanufacturers_Model_Attributesets
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $attributeSets = Mage::getModel('eav/entity_attribute_set')->getCollection()->setEntityTypeFilter($entityTypeId);
        foreach($attributeSets as $attributeSet) {
            $optionArray[] = array(
                'value' => $attributeSet->getId(),
                'label' => $attributeSet->getAttributeSetName()
            );
        }
        return $optionArray;
    }
}
