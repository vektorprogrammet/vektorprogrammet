<?php


namespace AppBundle\Entity\SimulatedAnnealing;



class Optimizer
{
    private $startSolution;
    private $temp;
    private $dt;
    private $startTime;
    private $maxTime;

    /**
     * Optimizer constructor.
     * @param $startSolution
     * @param $temp
     * @param $dt
     * @param $maxTime in seconds
     */
    public function __construct(Solution $startSolution, $temp, $dt, $maxTime)
    {
        $this->startSolution = $startSolution;
        $this->temp = $temp;//Set maxTemperature
        $this->dt = $dt;
        $this->maxTime = $maxTime;
        $this->startTime = round(microtime(true) * 1000)/1000;
    }

    public function optimize(){
        $startTime = round(microtime(true) * 1000);
        $bestSolution = $this->startSolution;
        $currentSolution = $bestSolution;

        while($this->temp > 0 && (round(microtime(true) * 1000)/1000 - $this->startTime) < $this->maxTime){
            //Return the solution if score === 100 (Perfect solution)
            if($currentSolution->evaluate() === 100){
                $currentSolution->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                return $currentSolution;
            }
            $neighbours = $currentSolution->generateNeighbours();

            if(sizeof($neighbours) === 0)break;
            //The node in the neighbour list with the highest score
            $pMax = null;
            $bestScore = 0;

            foreach($neighbours as $n){
                $currentScore = $n->evaluate();
                if($currentScore === 100){
                    $n->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                    return $n;
                }

                //Find the best node in the neighbour list
                if($currentScore > $bestScore){
                    $bestScore = $currentScore;
                    $pMax = $n;
                    if($bestScore > $bestSolution->evaluate()){
                        $bestSolution = $n;
                    }
                }
            }

            //$q = [0,1]. 0 when $currentSolution is as good as $bestSolution. 1 when $currentSolution score = 0
            $q = ($bestScore - $currentSolution->evaluate())/$currentSolution->evaluate();

            //The value of $p will be very close to 1 when temperature is high and we are not close to the bestSolution
            //Larger $p means more likely to explore than exploit
            $p = min(1, exp(-$q/$this->temp));
            $x = rand(0,100)/100;

            //q, p and x will make the algorithm search for new random solutions when we are at a bad node and at early iterations (temperature is still high).
            //The algorithm will search for close solutions when a good node is found and at later iterations (temperature is low).
            if($x > $p){
                $currentSolution = $pMax;//Exploiting
            }else{
                $currentSolution = $neighbours[array_rand($neighbours,1)];//Exploring
            }
            //Decrease temperature
            $this->temp -= $this->dt;
        }
        //Temperature === 0. The perfect solution was not found or does not exists. Return the most optimal solution found.
        $bestSolution->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
        return $bestSolution;
    }

}