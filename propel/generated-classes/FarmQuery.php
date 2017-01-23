<?php

use Base\FarmQuery as BaseFarmQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'farm' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class FarmQuery extends BaseFarmQuery {

  public function filterBySubscriber ($user) {
    $list = SubscriptionQuery::create()
      ->select('subscription_id')
      ->filterByUserId($user->getId())
      ->filterBySubscriptionType('farm')
      ->find();
    return $this->filterById($list->toArray());
  }

}
