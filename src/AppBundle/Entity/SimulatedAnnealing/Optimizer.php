<?php


namespace AppBundle\Entity\SimulatedAnnealing;



class Optimizer
{
    private $startNode;
    private $temp;
    private $dt;

    /**
     * Optimizer constructor.
     * @param $startNode
     * @param $temp
     * @param $dt
     */
    public function __construct(Node $startNode, $temp, $dt)
    {
        $this->startNode = $startNode;
        $this->temp = $temp;//Set maxTemperature
        $this->dt = $dt;
    }

    public function optimize(){
        $startTime = round(microtime(true) * 1000);
        $bestNode = $this->startNode;
        $currentNode = $bestNode;

        while($this->temp > 0){
            //Return the solution if score === 100 (Perfect solution)
            if($currentNode->getSolution()->evaluate() === 100){
                $currentNode->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                return $currentNode->getSolution();
            }
            $neighbours = $currentNode->generateNeighbours();

            //The node in the neighbour list with the highest score
            $pMax = null;
            $bestScore = 0;

            foreach($neighbours as $n){
                $currentScore = $n->getSolution()->evaluate();
                if($currentScore === 100){
                    $n->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                    return $n->getSolution();
                }

                //Find the best node in the neighbour list
                if($currentScore > $bestScore){
                    $bestScore = $currentScore;
                    $pMax = $n;
                    if($bestScore > $bestNode->getSolution()->evaluate()){
                        $bestNode = $n;
                    }
                }
            }

            //$q = [0,1]. 0 when $currentNode is as good as $bestNode. 1 when $currentNode score = 0
            $q = ($bestScore - $currentNode->getSolution()->evaluate())/$currentNode->getSolution()->evaluate();

            //The value of $p will be very close to 1 when temperature is high and we are not close to the bestNode
            //Larger $p means more likely to explore than exploit
            $p = min(1, exp(-$q/$this->temp));
            $x = rand(0,100)/100;

            //q, p and x will make the algorithm search for new random solutions when we are at a bad node and at early iterations (temperature is still high).
            //The algorithm will search for close solutions when a good node is found and at later iterations (temperature is low).
            if($x > $p){
                $currentNode = $pMax;//Exploiting
            }else{
                $currentNode = $neighbours[array_rand($neighbours,1)];//Exploring
            }
            //Decrease temperature
            $this->temp -= $this->dt;
        }
        //Temperature === 0. The perfect solution was not found or does not exists. Return the most optimal solution found.
        $bestNode->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
        return $bestNode->getSolution();
    }

}