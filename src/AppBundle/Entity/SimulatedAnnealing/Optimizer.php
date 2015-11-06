<?php


namespace AppBundle\Entity\SimulatedAnnealing;



class Optimizer
{
    private $startSolution;
    private $temp;
    private $dt;
    private $currentSolution;

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
        if($bestSolution->getSolution()->evaluate() === 100) return $bestSolution->getSolution();

        while($this->temp > 0){
            $neighbours = $this->currentSolution->generateNeighbours();

            $bestScore = 0;
            foreach($neighbours as $n){
                $currentScore = $n->getSolution()->evaluate();
                if($currentScore === 100){
                    $n->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
                    return $n->getSolution();
                }

                if($currentScore > $bestScore){
                    $bestScore = $currentScore;
                    if($bestScore > $bestSolution->getSolution()->evaluate()){
                        $bestSolution = $n;
                    }
                }
            }
            $q = ($bestScore - $this->currentSolution->getSolution()->evaluate())/$this->currentSolution->getSolution()->evaluate();
            $p = min(1, exp(-$q/$this->temp));
            $x = rand(0,1);

            if($x > $p){
                $this->currentSolution = $bestSolution;
            }else{
                $this->currentSolution = $neighbours[array_rand($neighbours,1)];
            }
            $this->temp -= $this->dt;
        }
        $bestSolution->getSolution()->optimizeTime = (round(microtime(true) * 1000)-$startTime)/1000;
        return $bestSolution->getSolution();
    }

}