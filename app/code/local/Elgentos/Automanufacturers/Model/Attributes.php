<?php
class Elgentos_Automanufacturers_Model_Attributes
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attrs = Mage::getModel('eav/entity_attribute')->getCollection();
        $return = array(''=>'');
        foreach($attrs as $attr) {
            $return[] = array('value'=>$attr->getId(),'label'=>$attr->getName());
        }
        return $return;
    }

}