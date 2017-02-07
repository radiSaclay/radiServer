<?php

use Base\ProductQuery as BaseProductQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'product' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ProductQuery extends BaseProductQuery {

  public function filterBySubscriber ($user) {
    $list = SubscriptionQuery::create()
      ->select('subscription_id')
      ->filterByUserId($user->getId())
      ->filterBySubscriptionType('product')
      ->find();
    return $this->filterById($list->toArray());
  }
}
