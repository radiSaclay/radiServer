<?php

use Base\EventQuery as BaseEventQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'event' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class EventQuery extends BaseEventQuery {

  public function filterBySubscriber ($user) {
    $farmIds = FarmQuery::create()
      ->select('id')
      ->filterBySubscriber($user)
      ->find()
      ->toArray();
    $productIds = ProductQuery::create()
      ->select('id')
      ->filterBySubscriber($user)
      ->find()
      ->toArray();
    $ids = EventProductQuery::create()
      ->select('event_id')
      ->filterByProductId($productIds)
      ->find()
      ->toArray();
    return $this
      ->filterByFarmId($farmIds)
      ->_or()
      ->filterById($ids);
  }

}
