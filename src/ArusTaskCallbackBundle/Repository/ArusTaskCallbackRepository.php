<?php

namespace ArusTaskCallbackBundle\Repository;

use Doctrine\Common\Util\Inflector as Inflector;

use ArusTaskCallbackBundle\Entity\Search;
use Actarus\Utils;


/**
 * ArusTaskCallbackRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArusTaskCallbackRepository extends \Doctrine\ORM\EntityRepository
{
	public function search( $data, $offset=null, $limit=null )
	{
		$data = Utils::array2object( $data, 'ArusTaskCallbackBundle\Entity\Search' );
		$t_params = array();
		$qb = $this->_em->createQueryBuilder();
		
		if( $offset < 0 ) {
			$offset = null;
			$count  = true;
			$query = $qb->select( 'count(tc.id)' );
		} else {
			$count  = false;
			$query = $qb->select( array('tc') );
		}
		$query = $query->from('ArusTaskCallbackBundle:ArusTaskCallback','tc');
		
		if( $data )
		{
			if ($data->getId()) {
				$query->andWhere('tc.id=:id');
				$t_params['id'] = $data->getId();
			}
			if ($data->getTaskId()) {
				$query->andWhere('tc.task=:task_id');
				$t_params['task_id'] = $data->getTaskId();
			}
		}
		
		$query->setParameters( $t_params );
		$query->orderBy('tc.priority', 'ASC');
		if( !is_null($limit) ) {
			$query->setMaxResults( $limit );
		}
		if( !is_null($offset) ) {
			$query->setFirstResult($offset);
		}
		
		$t_result = $query->getQuery()->getResult();
		
		if( $count ) {
			return (int)$t_result[0][1];
		} else {
			return $t_result;
		}
	}
}
