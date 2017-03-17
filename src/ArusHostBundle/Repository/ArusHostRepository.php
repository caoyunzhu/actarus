<?php

namespace ArusHostBundle\Repository;

use ArusEntityAlertBundle\Entity\Search as EntityAlertSearch;
use ArusEntityCommentBundle\Entity\Search as EntityCommentSearch;
use ArusEntityTaskBundle\Entity\Search as EntityTaskSearch;

use ArusEntityTechnologyBundle\Entity\ArusEntityTechnology;

use Actarus\Utils;


/**
 * ArusHostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArusHostRepository extends \Doctrine\ORM\EntityRepository
{
	public function search( $data, $offset=null, $limit=null )
	{
		$data = Utils::array2object( $data, 'ArusHostBundle\Entity\Search' );
		$t_params = array();
		$qb = $this->_em->createQueryBuilder();

		if( $offset < 0 ) {
			$offset = null;
			$count  = true;
			$query  = $qb->select( 'count(h.id)' );
		} else {
			$count  = false;
			$query  = $qb->select( array('h,d') );
		}
		$query = $query->from('ArusHostBundle:ArusHost','h')
						->leftJoin('h.domain','d');

		if( $data )
		{
			if( ($id=$data->getId()) ) {
				if( !is_array($id) ) {
					$id = [ $id, '=' ];
				}
				$query->andWhere( 'h.id '.$id[1].' :id' );
				$t_params['id'] = $id[0];
			}
			if ($data->getProject()) {
				$query->andWhere('h.project=:project_id');
				$t_params['project_id'] = $data->getProject()->getId();
			}
			/*if ($data->getServer()) {
				$query->andWhere('h.server=:server_id');
				$t_params['server_id'] = $data->getServer()->getId();
			}*/
			if ($data->getDomain()) {
				$query->andWhere('h.domain=:domain_id');
				$t_params['domain_id'] = $data->getDomain()->getId();
			}
			if( ($name=$data->getName()) ) {
				if( !is_array($name) ) {
					$name = [ '%'.$name.'%', 'LIKE' ];
				}
				$query->andWhere('h.name '.$name[1].' :name');
				$t_params['name'] = $name[0];
			}
			if ( !is_null($data->getStatus()) ) {
				$status = $data->getStatus();
				if( !is_array($status) ) {
					$status = [ $status, '=' ];
				}
				$query->andWhere('h.status '.$status[1].' :status');
				$t_params['status'] = $status[0];
			}
            /*if ($data->getIp()) {
                $query->andWhere('s.name LIKE :s_name');
                $t_params['s_name'] = $data->getIp() . '%';
            }*/
			if ($data->getMinCreatedAt()) {
				$query->andWhere('h.createdAt >= :min_created_date');
				$t_params['min_created_date'] = date( 'Y-m-d 00:00:00', Utils::dateFR2Time($data->getMinCreatedAt()) );
			}
			if ($data->getMaxCreatedAt()) {
				$query->andWhere('h.createdAt <= :max_created_date');
				$t_params['max_created_date'] = date( 'Y-m-d 23:59:59', Utils::dateFR2Time($data->getMaxCreatedAt()) );
			}
		}

		$query->setParameters( $t_params );
		$query->orderBy('h.id', 'DESC');
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


	public function getTasks( $host )
	{
		$search = new EntityTaskSearch();
		$search->setEntityId( $host->getEntityId() );
		$t_task = $this->_em->getRepository('ArusEntityTaskBundle:ArusEntityTask')->search( $search );

		return $t_task;
	}


	public function getComments( $host )
	{
		$search = new EntityTaskSearch();
		$search->setEntityId( $host->getEntityId() );
		$t_task = $this->_em->getRepository('ArusEntityTaskBundle:ArusEntityTask')->search( $search );

		return $t_task;
	}
}
