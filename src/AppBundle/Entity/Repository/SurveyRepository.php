<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class SurveyRepository extends EntityRepository {


    public function surveyById($id){
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('
					SELECT *
					FROM Survey S
					WHERE id = :id
					');

        $stmt->bindValue('id', $id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function SchoolById($id){
        $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare('
                    SELECT *
                    FROM School S
                    WHERE id = :id
                    ');
        $stmt->bindValue('id', $id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function NumOfSchoolsById($id){

        return $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)')
            ->where('surveyPupil.school = :school')
            ->setParameter('school', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function NumOfSchoolsBySchool($school){
        return $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)')
            ->where('surveyPupil.school = :school')
            ->setParameter('school', $school)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function NumOfSchoolsByIdFiltered($school, $filterArray){

        $qb = $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)')
            ->where('surveyPupil.school = :school')
            ->setParameter('school', $school);
        $queryString = "";
        if(count($filterArray) > 1){
            $counter = 0;
            foreach($filterArray as $filter){
                $qID = $filter[0];
                $aID = $filter[1];
                $queryString .= 'surveyPupil.question'. $qID .' = '.$aID.'';
                if(!($counter == count($filterArray) - 1)){
                    if($filterArray[$counter + 1][0] == $qID){
                        $queryString .= ' OR ';
                    }else{
                        $queryString .= ')';
                        $queryString .= ' AND ';
                        $queryString .= '(';
                    }
                }
                $counter += 1;
            }
        }else{
            $qID = $filterArray[0][0];
            $aID = $filterArray[0][1];
            $queryString = 'surveyPupil.question'. $qID .' = '.$aID.'';
        }
        $qb->andWhere($queryString);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function NumOfAnswersByQuestionIdAndAlternatives($questionId, $alternativeId){

        return $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)')
            ->where('surveyPupil.question'.$questionId.' = :alternativeId ')
            ->setParameter('alternativeId', $alternativeId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function NumOfAnswersByQuestionIdAndAlternativesFiltered($questionId, $alternativeId, $filterArray){
        $qb = $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)')
            ->where('surveyPupil.question'.$questionId.' = :alternativeId ')
            ->setParameter('alternativeId', $alternativeId);
        $queryString = "";
        if(count($filterArray) > 1){
            $counter = 0;
            foreach($filterArray as $filter){
                $qID = $filter[0];
                $aID = $filter[1];
                $queryString .= 'surveyPupil.question'. $qID .' = '.$aID.'';
                if(!($counter == count($filterArray) - 1)){
                    if($filterArray[$counter + 1][0] == $qID){
                        $queryString .= ' OR ';
                    }else{
                        $queryString .= ')';
                        $queryString .= ' AND ';
                        $queryString .= '(';
                    }
                }
                $counter += 1;
            }
        }else{
            $qID = $filterArray[0][0];
            $aID = $filterArray[0][1];
            $queryString = 'surveyPupil.question'. $qID .' = '.$aID.'';
        }
        $qb->andWhere($queryString);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function NumOfSurveys(){
        return $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function NumOfSurveysFiltered($filterArray){
        $qb = $this->createQueryBuilder('surveyPupil')
            ->select('count(surveyPupil.id)');
        $queryString = "(";
        if(count($filterArray) > 1){
            $counter = 0;
            foreach($filterArray as $filter){
                $qID = $filter[0];
                $aID = $filter[1];
                $queryString .= 'surveyPupil.question'. $qID .' = '.$aID.'';
                if(!($counter == count($filterArray) - 1)){
                    if($filterArray[$counter + 1][0] == $qID){
                        $queryString .= ' OR ';
                    }else{
                        $queryString .= ')';
                        $queryString .= ' AND ';
                        $queryString .= '(';
                    }
                }
                $counter += 1;
            }
            $queryString .= ')';
        }else{
            $qID = $filterArray[0][0];
            $aID = $filterArray[0][1];
            $queryString = 'surveyPupil.question'. $qID .' = '.$aID.'';
        }

        $qb->Where($queryString);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function allFeedback(){

        return $this->createQueryBuilder('surveyPupil')
            ->select('surveyPupil.question10')
            ->where('surveyPupil.question10 IS NOT NULL')
            ->getQuery()
            ->getArrayResult();
    }

}