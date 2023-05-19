<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\AdvancedReports\Api\Data;

/**
 * @api
 */
interface ProductsnotsoldInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    

    /**#@-*/

    /**
     * entity_id
     *
     * @return string
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param string $entity_id
     * @return $this
     */
    public function setEntityId($entity_id);

     /**
     * name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * type
     *
     * @return string
     */
    public function getCustomName();

    /**
     * Set custom_name
     *
     * @param string $custom_name
     * @return $this
     */
    public function setCustomName($custom_name);

    /**
     * type
     *
     * @return string
     */
    public function getTypeId();

    /**
     * Set type_id
     *
     * @param string $type_id
     * @return $this
     */
    public function setTypeId($type_id);

    /**
     * type_label
     *
     * @return string
     */
    public function getTypeLabel();

    /**
     * Set type_label
     *
     * @param string $type_label
     * @return $this
     */
    public function setTypeLabel($type_label);


    /**
     * attribute_set_id
     *
     * @return string
     */
    public function getAttributeSetId();

    /**
     * Set attribute_set_id
     *
     * @param string $attribute_set_id
     * @return $this
     */
    public function setAttributeSetId($attribute_set_id);

    /**
     * attribute_set_label
     *
     * @return string
     */
    public function getAttributeSetLabel();

    /**
     * Set attribute_set_label
     *
     * @param string $attribute_set_label
     * @return $this
     */
    public function setAttributeSetLabel($attribute_set_label);

    /**
     * sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Set sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * price
     *
     * @return string
     */
    public function getPrice();

    /**
     * Set price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * price currency
     *
     * @return string
     */
    public function getPriceCurrency();

    /**
     * Set price_currency
     *
     * @param string $price_currency
     * @return $this
     */
    public function setPriceCurrency($price_currency);

    /**
     * qty
     *
     * @return string
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param string $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * visibility
     *
     * @return string
     */
    public function getVisibility();

    /**
     * Set visibility
     *
     * @param string $visibility
     * @return $this
     */
    public function setVisibility($visibility);

    /**
     * visibility_label
     *
     * @return string
     */
    public function getVisibilityLabel();

    /**
     * Set visibility_label
     *
     * @param string $visibility_label
     * @return $this
     */
    public function setVisibilityLabel($visibility_label);

    /**
     * status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * status_label
     *
     * @return string
     */
    public function getStatusLabel();

    /**
     * Set status_label
     *
     * @param string $status_label
     * @return $this
     */
    public function setStatusLabel($status_label);

    /**
     * websites
     *
     * @return string
     */
    public function getWebsites();

    /**
     * Set websites
     *
     * @param string $websites
     * @return $this
     */
    public function setWebsites($websites);

    /**
     * websites_label
     *
     * @return string
     */
    public function getWebsitesLabel();

    /**
     * Set websites_label
     *
     * @param string $websites_label
     * @return $this
     */
    public function setWebsitesLabel($websites_label);


}
