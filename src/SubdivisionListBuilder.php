<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionListBuilder.
 */

namespace Drupal\addressfield;

use CommerceGuys\Addressing\Model\SubdivisionInterface;
use Drupal\addressfield\AddressFormatInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Provides a listing of Subdivisions.
 */
class SubdivisionListBuilder extends ConfigEntityListBuilder {

  /**
   * The address format.
   *
   * @var \Drupal\addressfield\AddressFormatInterface
   */
  protected $addressFormat;

  /**
   * The parent subdivision.
   *
   * @var \CommerceGuys\Addressing\Model\SubdivisionInterface
   */
  protected $parent;

  /**
   * Sets the address format.
   *
   * @param \Drupal\addressfield\AddressFormatInterface $address_format
   *   The address format.
   */
  public function setAddressFormat(AddressFormatInterface $address_format) {
    $this->addressFormat = $address_format;
  }

  /**
   * Sets the parent subdivision.
   *
   * @param $parent
   *   The parent subdivision.
   */
  public function setParent(SubdivisionInterface $parent = NULL) {
    $this->parent = $parent;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    return $this->storage->loadByProperties(array(
      'countryCode' => $this->addressFormat->getCountryCode(),
      'parentId' => $this->parent ? $this->parent->id() : null,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $entity */
    $operations = parent::getDefaultOperations($entity);
    $operations['children'] = array(
      'title' => $this->t('List children'),
      'weight' => 1000,
      'url' => Url::fromRoute('entity.subdivision.collection', array(
        'address_format' => $entity->getCountryCode(),
        'parent' => $entity->id(),
      )),
    );

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    $header['code'] = $this->t('Code');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['name'] = $this->getLabel($entity);
    $row['code'] = $entity->getCode();

    return $row + parent::buildRow($entity);
  }

}
