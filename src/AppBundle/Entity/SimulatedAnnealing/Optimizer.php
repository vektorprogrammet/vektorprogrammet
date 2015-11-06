<?php


namespace AppBundle\Entity\SimulatedAnnealing;



class Optimizer
{
    private $startSolution;
    private $temp;
    private $dt;

    /**
     * Optimizer constructor.
     * @param $startSolution
     * @param $temp
     * @param $dt
     */
    public function __construct(Node $startSolution, $temp, $dt)
    {
        $this->startSolution = $startSolution;
        $this->temp = $temp;
        $this->dt = $dt;
        $this->currentSolution = new Node($startSolution->getSolution());
    }

    public function optimize(){
        $startTime = round(microtime(true) * 1000);
        $bestSolution = $this->startSolution;
        $currentSolution = $bestSolution;

        while($this->temp > 0){
            if($currentSolution->getSolution()->evaluate() === 100){
                $currentSolution->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                return $currentSolution->getSolution();
            }
            $neighbours = $this->currentSolution->generateNeighbours();

            $bestScore = 0;
            $pMax = null;
            foreach($neighbours as $n){
                $currentScore = $n->getSolution()->evaluate();
                if($currentScore === 100){
                    $n->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                    return $n->getSolution();
                }

                if($currentScore > $bestScore){
                    $bestScore = $currentScore;
                    $pMax = $n;
                    if($bestScore > $bestSolution->getSolution()->evaluate()){
                        $bestSolution = $n;
                    }
                }
            }
            $q = ($bestScore - $this->currentSolution->getSolution()->evaluate())/$this->currentSolution->getSolution()->evaluate();
            $p = min(1, exp(-$q/$this->temp));
            $x = rand(0,1);

            if($x > $p){
                $this->currentSolution = $pMax;
            }else{
                $this->currentSolution = $neighbours[array_rand($neighbours,1)];
            }
            $this->temp -= $this->dt;
        }
        $bestSolution->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
        return $bestSolution->getSolution();
    }

}