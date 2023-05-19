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
interface CustomernotorderInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
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
     * email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * group
     *
     * @return string
     */
    public function getGroupId();

    /**
     * Set group_id
     *
     * @param string $group_id
     * @return $this
     */
    public function setGroupId($group_id);

    /**
     * group_label
     *
     * @return string
     */
    public function getGroupLabel();

    /**
     * Set group_label
     *
     * @param string $group_label
     * @return $this
     */
    public function setGroupLabel($group_label);

    /**
     * billing_telephone
     *
     * @return string
     */
    public function getBillingTelephone();

    /**
     * Set billing_telephone
     *
     * @param string $billing_telephone
     * @return $this
     */
    public function setBillingTelephone($billing_telephone);

    /**
     * billing_postcode
     *
     * @return string
     */
    public function getBillingPostcode();

    /**
     * Set billing_postcode
     *
     * @param string $billing_postcode
     * @return $this
     */
    public function setBillingPostcode($billing_postcode);

    /**
     * billing_country_id
     *
     * @return string
     */
    public function getBillingCountryId();

    /**
     * Set billing_country_id
     *
     * @param string $billing_country_id
     * @return $this
     */
    public function setBillingCountryId($billing_country_id);

    /**
     * billing_country_label
     *
     * @return string
     */
    public function getBillingCountryLabel();

    /**
     * Set billing_country_label
     *
     * @param string $billing_country_label
     * @return $this
     */
    public function setBillingCountryLabel($billing_country_label);

    /**
     * billing_region
     *
     * @return string
     */
    public function getBillingRegion();

    /**
     * Set billing_region
     *
     * @param string $billing_region
     * @return $this
     */
    public function setBillingRegion($billing_region);

    /**
     * created_at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);

    /**
     * website_id
     *
     * @return string
     */
    public function getWebsiteId();

    /**
     * Set website_id
     *
     * @param string $website_id
     * @return $this
     */
    public function setWebsiteId($website_id);
}
